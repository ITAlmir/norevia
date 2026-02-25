<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // Osnovno
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Kategorija / tip
            $table->string('category')->index(); // gaming, tools, apps (ili enum kasnije)

            // Verzija / meta
            $table->string('version')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('os')->nullable(); // windows/mac/linux ili null

            // Fajl
            $table->string('file_path'); // npr: downloads/tools/ping-monitor.zip
            $table->string('original_filename')->nullable();

            // Thumbnail
            $table->string('thumbnail_path')->nullable();

            // Sigurnost / status
            $table->string('scan_status')->default('unknown'); // unknown|clean|flagged
            $table->string('sha256')->nullable();

            // Brojači (brzo za listanje bez groupBy)
            $table->unsignedBigInteger('download_count')->default(0);

            // Publish
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['category', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
