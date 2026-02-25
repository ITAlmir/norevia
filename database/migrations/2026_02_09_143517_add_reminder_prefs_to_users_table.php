<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('email_reminders')->default(true);
            $table->time('reminder_time')->default('09:00:00');
            $table->timestamp('last_followup_digest_sent_at')->nullable()->index();
            $table->timestamp('last_followup_escalation_sent_at')->nullable()->index();
            $table->unsignedTinyInteger('reminder_days_before')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_reminders',
                'reminder_time',
                'last_followup_digest_sent_at',
                'last_followup_escalation_sent_at',
            ]);
        });
    }
};
