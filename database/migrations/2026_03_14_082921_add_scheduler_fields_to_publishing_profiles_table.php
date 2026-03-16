<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publishing_profiles', function (Blueprint $table) {
            $table->json('publish_times')->nullable()->after('default_publish_time');
            $table->string('schedule_type')->default('daily')->after('publish_times');
            $table->json('schedule_days')->nullable()->after('schedule_type');
        });
    }

    public function down(): void
    {
        Schema::table('publishing_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'publish_times',
                'schedule_type',
                'schedule_days',
            ]);
        });
    }
};