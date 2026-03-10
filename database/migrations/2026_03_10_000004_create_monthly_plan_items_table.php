<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_plan_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monthly_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_topic_id')->nullable()->constrained('content_topics')->nullOnDelete();

            $table->date('plan_date');
            $table->string('task_title');
            $table->string('platform')->nullable();
            $table->string('series')->nullable();
            $table->string('voice_tool')->nullable();

            $table->enum('status', ['planned', 'done', 'skipped'])->default('planned');

            $table->timestamps();

            $table->index(['user_id', 'plan_date']);
            $table->index(['monthly_plan_id', 'plan_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_plan_items');
    }
};