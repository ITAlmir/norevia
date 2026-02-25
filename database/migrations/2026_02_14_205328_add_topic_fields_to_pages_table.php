<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // SEO “folder” (cs2, pc-optimization, youtube-growth...)
            $table->string('topic')->nullable()->index();

            // tip sadržaja (blog, landing, legal, etc.)
            $table->string('type')->default('blog')->index();

            // opciono: canonical override (ako nekad treba)
            $table->string('canonical_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['topic', 'type', 'canonical_url']);
        });
    }
};
