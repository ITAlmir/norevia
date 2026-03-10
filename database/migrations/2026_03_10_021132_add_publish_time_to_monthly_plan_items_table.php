<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_plan_items', function (Blueprint $table) {
            $table->time('publish_time')->nullable()->after('voice_tool');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_plan_items', function (Blueprint $table) {
            $table->dropColumn('publish_time');
        });
    }
};