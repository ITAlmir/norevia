<?php

// app/Services/ReminderService.php
namespace App\Services;

use App\Models\Collaboration;
use App\Models\CollaborationReminder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReminderService
{
  public function syncForCollaboration(Collaboration $c): void
{
    $user = $c->user;
    if (!$user) return;

    // očisti pending
    $this->cancelPending($c);

    // zatvoreno / nema followup
    if (!$c->follow_up_date || in_array($c->status, ['finished','lost'], true)) return;

    // enabled (collab override ili user)
    $enabled = $c->reminder_enabled;
    if ($enabled === null) $enabled = (bool) $user->reminders_enabled;
    if (!$enabled) return;

    // ✅ ako ima custom reminder_send_at, to je istina
    if (!$c->reminder_send_at) return;

    $sendAt = Carbon::parse($c->reminder_send_at); // može biti u prošlosti -> expired

    $dedupe = "u:{$user->id}|c:{$c->id}";

    CollaborationReminder::updateOrCreate(
        ['dedupe_key' => $dedupe],
        [
            'user_id' => $user->id,
            'collaboration_id' => $c->id,
            'send_at' => $sendAt,
            'channel' => 'email',
            'status' => 'pending', // može biti expired u UI jer send_at past
            'last_error' => null,
        ]
    );
}


public function cancelPending(Collaboration $c): void
{
    CollaborationReminder::where('user_id', $c->user_id)
        ->where('collaboration_id', $c->id)
        ->where('status', 'pending')
        ->update(['status' => 'cancelled']);
}





  private function dedupeKey(int $userId, int $collabId): string
{
  return "u:{$userId}|c:{$collabId}";
}


}

