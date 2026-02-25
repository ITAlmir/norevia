<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();

            // veza na postojeći items.id
            $table->foreignId('item_id')
                ->constrained('items')
                ->onDelete('cascade');

            // metadata za download
            $table->string('slug')->unique();              // npr fps-optimizer
            $table->string('title');                       // display naziv
            $table->text('description')->nullable();

            $table->string('version')->default('1.0.0');
            $table->string('original_filename')->nullable(); // npr Setup.zip
            $table->string('stored_path');                 // npr downloads/fps-optimizer/Setup.zip
            $table->unsignedBigInteger('file_size')->default(0); // bytes

            $table->string('sha256', 64)->nullable();       // hash fajla
            $table->string('scan_status')->default('unknown'); // unknown|safe|warning|malicious

            // cache counter (brzo listanje bez COUNT na logs)
            $table->unsignedBigInteger('download_count')->default(0);

            $table->boolean('is_published')->default(true);

            $table->timestamps();

            $table->unique('item_id'); // 1 item -> 1 download (sigurno i jasno)
            $table->index(['is_published', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
