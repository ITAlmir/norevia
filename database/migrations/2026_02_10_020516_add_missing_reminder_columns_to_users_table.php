<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'reminders_enabled')) {
                $table->boolean('reminders_enabled')->default(true);
            }

            if (!Schema::hasColumn('users', 'reminder_days_before')) {
                $table->unsignedTinyInteger('reminder_days_before')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'reminders_enabled')) {
                $table->dropColumn('reminders_enabled');
            }

            if (Schema::hasColumn('users', 'reminder_days_before')) {
                $table->dropColumn('reminder_days_before');
            }
        });
    }
};
