<?php

// app/Jobs/SendCollaborationReminderJob.php
namespace App\Jobs;

use App\Models\CollaborationReminder;
use App\Notifications\FollowUpReminder;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCollaborationReminderJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(public int $reminderId) {}

  public function handle(): void
  {
    $r = CollaborationReminder::with(['collaboration.sponsor','user'])->find($this->reminderId);
    if (!$r || $r->status !== 'pending') return;

    // safety: ako je collab zatvoren ili bez followup -> cancel
    $c = $r->collaboration;
    if (!$c || !$c->follow_up_date || in_array($c->status, ['finished','lost'], true)) {
      $r->update(['status' => 'cancelled']);
      return;
    }

    try {
      $r->user->notify(new FollowUpReminder($c));
      $r->update(['status' => 'sent', 'sent_at' => now(), 'last_error' => null]);
    } catch (Exception $e) {
      $r->update(['status' => 'failed', 'last_error' => $e->getMessage()]);
      throw $e; // da queue retry radi
    }
  }
}

