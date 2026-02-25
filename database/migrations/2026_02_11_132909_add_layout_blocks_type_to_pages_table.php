<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('page_type')->default('post')->after('slug'); // news,gaming,horoscope,post
            $table->string('layout')->default('minimal')->after('page_type'); // minimal,classic,magazine,hero
            $table->json('blocks')->nullable()->after('content'); // glavni editor (blokovi)
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['page_type', 'layout', 'blocks']);
        });
    }
};
