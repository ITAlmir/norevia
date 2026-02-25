<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollaborationReminder;

class RemindersPrune extends Command
{
    protected $signature = 'reminders:prune';
    protected $description = 'Delete old reminders older than 15 days (sent/cancelled)';

    public function handle(): int
    {
        $cutoff = now()->subDays(15);

        $deleted = CollaborationReminder::query()
            ->whereIn('status', ['sent','cancelled'])
            ->where('send_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deleted} old reminders.");
        return self::SUCCESS;
    }
}

