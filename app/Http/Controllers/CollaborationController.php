<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use App\Services\ReminderService;


class CollaborationController extends Controller
{
    private array $allowedTransitions = [
    'idea' => ['contacted', 'lost'],
    'contacted' => ['replied', 'lost'],
    'replied' => ['negotiating', 'lost'],
    'negotiating' => ['active', 'lost'],
    'active' => ['finished'],
    'finished' => [],
    'lost' => [],
];

    public function index()
    {
        $collaborations = Collaboration::where('user_id', auth()->id())
            ->with('sponsor')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($collaborations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sponsor_id' => 'required|exists:sponsors,id',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // sigurnost: sponsor mora pripadati useru
        Sponsor::where('id', $validated['sponsor_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $collaboration = Collaboration::create([
            'user_id' => auth()->id(),
            'sponsor_id' => $validated['sponsor_id'],
            'title' => $validated['title'],
            'status' => 'idea',
            'follow_up_date' => now()->addDays(7),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($collaboration, 201);
    }

    public function updateStatus(Request $request, $id, ReminderService $reminders)
{
    $collaboration = Collaboration::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $validated = $request->validate([
        'status' => 'required|string|in:idea,contacted,replied,negotiating,active,finished,lost',
    ]);

    $collaboration->status = $validated['status'];

    switch ($validated['status']) {
        case 'contacted':
            $collaboration->follow_up_date = now()->addDays(7)->toDateString();
            break;
        case 'replied':
        case 'negotiating':
            $collaboration->follow_up_date = now()->addDays(3)->toDateString();
            break;
        case 'active':
        case 'finished':
        case 'lost':
            $collaboration->follow_up_date = null;
            break;
    }

//     $collaboration->save();

//     // ✅ AUTO: kreiraj/cancel reminder nakon promjene statusa/follow-up-a
//     $reminders->syncForCollaboration($collaboration->fresh(['user', 'sponsor']));

//     return response()->json([
//         'success' => true,
//         'collaboration' => $collaboration,
//     ]);
$collaboration->save();

try {
    $reminders->syncForCollaboration($collaboration->fresh(['user', 'sponsor']));
} catch (\Throwable $e) {
    \Log::error('Reminder sync failed', [
        'collaboration_id' => $collaboration->id,
        'error' => $e->getMessage(),
    ]);
}

return response()->json(['ok' => true]);


 }



public function destroy(Collaboration $collaboration)
{
    abort_unless($collaboration->user_id === auth()->id(), 403);

    $collaboration->delete();

    return response()->json(['success' => true]);
}


public function updateFollowUp(Request $request, $id, ReminderService $svc)
{
    $data = $request->validate([
        'follow_up_date' => ['nullable','date_format:Y-m-d'],
        'reminder_send_at' => ['nullable','date_format:Y-m-d\TH:i'], // datetime-local format
        'reminder_enabled' => ['nullable','boolean'],
    ]);

    $collaboration = Collaboration::with('user','sponsor')
        ->where('user_id', $request->user()->id)
        ->findOrFail($id);

    $collaboration->follow_up_date = $data['follow_up_date'] ?? null;

    // Exact reminder
    $enabled = $data['reminder_enabled'] ?? false;
    $collaboration->reminder_enabled = $enabled;

    $collaboration->reminder_send_at = $enabled && !empty($data['reminder_send_at'])
        ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $data['reminder_send_at'])->seconds(0)
        : null;

    $collaboration->save();

    // Sync reminders table (upcoming list)
    $svc->syncForCollaboration($collaboration->fresh(['user','sponsor']));

    return response()->json(['ok' => true]);
}



}
