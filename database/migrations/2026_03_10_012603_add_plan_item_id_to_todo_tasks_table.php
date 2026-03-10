<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->foreignId('plan_item_id')
                ->nullable()
                ->after('user_id')
                ->constrained('monthly_plan_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropForeign(['plan_item_id']);
            $table->dropColumn('plan_item_id');
        });
    }
};
