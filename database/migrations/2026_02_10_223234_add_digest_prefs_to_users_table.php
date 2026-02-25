<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      // reminders_enabled već postoji - ne diramo

      if (!Schema::hasColumn('users', 'daily_digest_enabled')) {
        $table->boolean('daily_digest_enabled')->default(false);
      }

      if (!Schema::hasColumn('users', 'daily_digest_time')) {
        $table->string('daily_digest_time', 5)->default('08:30'); // HH:mm
      }
    });
  }

  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      if (Schema::hasColumn('users', 'daily_digest_enabled')) {
        $table->dropColumn('daily_digest_enabled');
      }

      if (Schema::hasColumn('users', 'daily_digest_time')) {
        $table->dropColumn('daily_digest_time');
      }
    });
  }
};

