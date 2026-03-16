<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->timestamp('cleared_at')->nullable()->after('status');

            $table->index(['user_id','cleared_at']);
        });
    }

    public function down(): void
    {
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropColumn('cleared_at');
        });
    }
};