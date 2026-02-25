<?php

// app/Console/Commands/SendReminders.php
namespace App\Console\Commands;

use App\Jobs\SendCollaborationReminderJob;
use App\Models\CollaborationReminder;
use Illuminate\Console\Command;

class SendReminders extends Command
{
  protected $signature = 'reminders:send';
  protected $description = 'Dispatch due collaboration reminders';

  public function handle(): int
  {
    $due = CollaborationReminder::where('status','pending')
      ->where('send_at','<=', now())
      ->orderBy('send_at')
      ->limit(200)
      ->get(['id']);

    foreach ($due as $r) {
      SendCollaborationReminderJob::dispatch($r->id)->onQueue('reminders');
    }

    return self::SUCCESS;
  }
}
