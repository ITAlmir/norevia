<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) ugasi FK privremeno (SQLite + postojeće tabele koje referenciraju items)
        Schema::disableForeignKeyConstraints();

        // 2) napravi novu tabelu items_new sa TAČNOM strukturom koju želiš
        Schema::create('items_new', function (Blueprint $table) {
            $table->id();

            // Osnovno
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Kategorija / tip
            $table->string('category')->index();

            // Verzija / meta
            $table->string('version')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('os')->nullable();

            // Fajl
            $table->string('file_path');
            $table->string('original_filename')->nullable();

            // Thumbnail
            $table->string('thumbnail_path')->nullable();

            // Sigurnost / status
            $table->string('scan_status')->default('unknown');
            $table->string('sha256')->nullable();

            // Brojači
            $table->unsignedBigInteger('download_count')->default(0);

            // Publish
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['category', 'is_published']);
        });

        // 3) prekopiraj postojeće podatke iz items u items_new (ako ih ima)
        // Pošto stara tabela ima samo id/created_at/updated_at, kopiramo to,
        // a ostalo popunjavamo default/placeholder vrijednostima da NOT NULL polja prođu.
        // (title, slug, category, file_path su NOT NULL)
        DB::statement("
            INSERT INTO items_new (
                id, title, slug, category, file_path,
                created_at, updated_at
            )
            SELECT
                id,
                'Recovered Item #' || id,
                'recovered-item-' || id,
                'tools',
                'downloads/missing.bin',
                created_at,
                updated_at
            FROM items
        ");

        // 4) drop stari items i zamijeni novim
        DB::statement("DROP TABLE items");
        DB::statement("ALTER TABLE items_new RENAME TO items");

        // 5) upali FK nazad
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // namjerno prazno - rollback ovog bi bio rizičan bez backup-a
    }
};
