<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publishing_profiles', function (Blueprint $table) {
            $table->string('content_group')->nullable()->after('schedule_days');
            $table->boolean('use_shared_content')->default(false)->after('content_group');
        });
    }

    public function down(): void
    {
        Schema::table('publishing_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'content_group',
                'use_shared_content',
            ]);
        });
    }
};