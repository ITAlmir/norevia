<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collaboration_reminders', function (Blueprint $t) {
  $t->id();
  $t->foreignId('user_id')->constrained()->cascadeOnDelete();
  $t->foreignId('collaboration_id')->constrained()->cascadeOnDelete();

  $t->dateTime('send_at')->index();
  $t->string('status')->default('pending')->index(); // pending|sent|cancelled|failed
  $t->dateTime('sent_at')->nullable();
  $t->text('last_error')->nullable();

  $t->string('dedupe_key')->unique(); // npr u:1|c:33|at:2026-02-10 09:00
  $t->timestamps();
  $t->index(['collaboration_id','status']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaboration_reminders');
    }
};
