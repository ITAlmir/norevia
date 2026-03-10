<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('platform')->nullable();     // tiktok, fb, youtube...
            $table->string('series')->nullable();       // cs2fps, svijet40...
            $table->string('voice_tool')->nullable();   // TTSMaker, PlayHT...
            $table->date('scheduled_for')->nullable();
            $table->time('publish_time')->nullable();

            $table->enum('status', ['pending', 'done', 'late'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'scheduled_for']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_tasks');
    }
};