<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publishing_profiles', function (Blueprint $table) {
            $table->string('content_bucket')->nullable()->after('content_group');
        });

        Schema::table('content_topics', function (Blueprint $table) {
            $table->string('content_bucket')->nullable()->after('voice_tool');
            $table->string('shared_content_group')->nullable()->after('content_bucket');
        });

        Schema::table('monthly_plan_items', function (Blueprint $table) {
            $table->string('content_bucket')->nullable()->after('profile_name');
            $table->string('shared_content_group')->nullable()->after('content_bucket');
        });

        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->string('content_bucket')->nullable()->after('profile_name');
            $table->string('shared_content_group')->nullable()->after('content_bucket');
        });
    }

    public function down(): void
    {
        Schema::table('publishing_profiles', function (Blueprint $table) {
            $table->dropColumn('content_bucket');
        });

        Schema::table('content_topics', function (Blueprint $table) {
            $table->dropColumn(['content_bucket', 'shared_content_group']);
        });

        Schema::table('monthly_plan_items', function (Blueprint $table) {
            $table->dropColumn(['content_bucket', 'shared_content_group']);
        });

        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropColumn(['content_bucket', 'shared_content_group']);
        });
    }
};