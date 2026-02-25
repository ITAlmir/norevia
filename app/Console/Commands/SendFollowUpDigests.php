<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\FollowUpReminder;
use App\Notifications\FollowUpDigestNotification;
use Illuminate\Console\Command;

class SendFollowUpDigests extends Command
{
    protected $signature = 'digests:followups';
    protected $description = 'Send daily follow-up digest emails (today + needs attention)';

    public function handle(): int
    {
        $today = now()->toDateString();

        User::query()
            ->whereNotNull('email')
            ->where('email_reminders', true)
            ->chunkById(200, function ($users) use ($today) {

                foreach ($users as $user) {
                    // Anti-spam: max 1/day
                    if ($user->last_followup_digest_sent_at && $user->last_followup_digest_sent_at->isToday()) {
                        continue;
                    }

                    $snap = FollowUpReminder::snapshotFor($user, 10);
                    $total = ($snap['todayCount'] ?? 0) + ($snap['overdueCount'] ?? 0);

                    if ($total <= 0) continue;

                    $user->notify(new FollowUpDigestNotification(
    $today,
    (int) ($snap['todayCount'] ?? 0),
    (int) ($snap['overdueCount'] ?? 0),
    $snap['emailItems'] ?? []
));


                    $user->forceFill(['last_followup_digest_sent_at' => now()])->save();
                }
            });

        return self::SUCCESS;
    }
}
