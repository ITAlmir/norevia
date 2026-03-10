<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_topics', function (Blueprint $table) {

            $table->text('caption')->nullable()->after('title');
            $table->text('description')->nullable()->after('caption');
            $table->text('hashtags')->nullable()->after('description');
            $table->text('script_notes')->nullable()->after('hashtags');

        });
    }

    public function down(): void
    {
        Schema::table('content_topics', function (Blueprint $table) {

            $table->dropColumn([
                'caption',
                'description',
                'hashtags',
                'script_notes'
            ]);

        });
    }
};
