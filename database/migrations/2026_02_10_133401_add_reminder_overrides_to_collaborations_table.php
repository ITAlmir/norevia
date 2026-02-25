<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->boolean('reminder_enabled')->nullable(); // null = use user default
            $table->string('reminder_time')->nullable(); // "08:30"
            $table->unsignedTinyInteger('reminder_days_before')->nullable(); // 0..30
        });
    }

    public function down(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropColumn(['reminder_enabled','reminder_time','reminder_days_before']);
        });
    }
};
