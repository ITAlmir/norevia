<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Models\CollaborationReminder;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
  public function upcoming(Request $request)
{
    $u = $request->user();

    $cutoff = now()->subDays(15);

    $rows = \App\Models\CollaborationReminder::with(['collaboration.sponsor'])
        ->where('user_id', $u->id)
        ->whereIn('status', ['pending','cancelled'])
        ->where('send_at', '>=', $cutoff)   // ✅ samo zadnjih 15 dana + future
        ->get();

    // ✅ future prvo, expired zadnje
    $rows = $rows->sortBy(fn($r) => ($r->send_at && $r->send_at->isPast()) ? 1 : 0)
                 ->sortBy('send_at') // unutar grupe po vremenu
                 ->take(15)          // koliko želiš prikazati
                 ->values();

    return $rows->map(fn($r) => [
        'id' => $r->id,
        'collaboration_id' => $r->collaboration_id,
        'send_at' => $r->send_at?->toDateTimeString(),
        'follow_up_date' => $r->collaboration?->follow_up_date,
        'title' => $r->collaboration?->title,
        'sponsor' => $r->collaboration?->sponsor?->name,
        'status' => $r->status,
        'expired' => $r->send_at ? $r->send_at->isPast() : false,
    ]);
}


  public function rebuild(Request $request, \App\Services\ReminderService $svc)
{
    try {
        $u = $request->user();

        // hard reset - sve pending -> cancelled
        CollaborationReminder::where('user_id', $u->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $collabs = Collaboration::with('user','sponsor')
            ->where('user_id', $u->id)
            ->whereNotIn('status', ['finished','lost'])
            ->where('reminder_enabled', 1)
            ->whereNotNull('reminder_send_at')
            ->get();

        foreach ($collabs as $c) {
            $svc->syncForCollaboration($c);
        }

        return response()->json(['ok' => true, 'count' => $collabs->count()]);
    } catch (\Throwable $e) {
        \Log::error('Reminders rebuild failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json(['ok' => false, 'message' => 'Rebuild failed'], 500);
    }
}


public function toggle(Request $request, CollaborationReminder $reminder, ReminderService $svc)
{
    abort_unless($reminder->user_id === $request->user()->id, 403);

    $data = $request->validate([
        'enabled' => ['required','boolean'],
    ]);

    if (!$data['enabled']) {
        $reminder->update(['status' => 'cancelled']);
        return response()->json(['ok' => true]);
    }

    // ENABLE: uzmi collaboration truth
    $c = Collaboration::with('user','sponsor')
        ->where('user_id', $request->user()->id)
        ->findOrFail($reminder->collaboration_id);

    // ako nema reminder_send_at, nema šta enable
    if (!$c->reminder_send_at) {
        return response()->json(['ok' => false, 'message' => 'No reminder time set'], 422);
    }

    // najjednostavnije: re-sync (ovo mora napraviti pending)
    $svc->syncForCollaboration($c);

    return response()->json(['ok' => true]);
}

public function destroy(Request $request, CollaborationReminder $reminder)
{
    abort_unless($reminder->user_id === $request->user()->id, 403);
    $reminder->delete();
    return response()->json(['ok' => true]);
}


}

