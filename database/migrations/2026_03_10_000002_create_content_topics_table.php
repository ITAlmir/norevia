<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('category')->nullable();     // cs2, fb, world
            $table->string('platform')->nullable();     // tiktok, fb, youtube
            $table->string('series')->nullable();       // cs2fps, fact, place
            $table->string('topic_code')->nullable();   // cs2#fps_myth
            $table->string('title');
            $table->string('voice_tool')->nullable();

            $table->enum('status', ['available', 'used', 'archived'])->default('available');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_topics');
    }
};