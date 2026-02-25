<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) napravi novu tabelu sa istim kolonama ali content nullable
        Schema::create('pages_new', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            // ✅ nullable content
            $table->text('content')->nullable();

            $table->string('featured_image')->nullable();
            $table->string('image_caption')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->timestamp('published_at')->nullable();

            $table->string('page_type')->default('post');
            $table->string('layout')->default('minimal');
            $table->json('blocks')->nullable(); // ok iako SQLite piše TEXT
        });

        // 2) kopiraj podatke
        DB::statement("
            INSERT INTO pages_new (
                id, title, slug, content, featured_image, image_caption, meta_title, meta_description,
                status, user_id, views, created_at, updated_at, published_at, page_type, layout, blocks
            )
            SELECT
                id, title, slug, content, featured_image, image_caption, meta_title, meta_description,
                status, user_id, views, created_at, updated_at, published_at, page_type, layout, blocks
            FROM pages
        ");

        // 3) obriši staru i preimenuj novu
        Schema::drop('pages');
        Schema::rename('pages_new', 'pages');
    }

    public function down(): void
    {
        // rollback: rebuild nazad na NOT NULL content
        Schema::create('pages_old', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            // ❌ not null content (staro stanje)
            $table->text('content');

            $table->string('featured_image')->nullable();
            $table->string('image_caption')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->timestamp('published_at')->nullable();

            $table->string('page_type')->default('post');
            $table->string('layout')->default('minimal');
            $table->json('blocks')->nullable();
        });

        // ako content null, postavi '' da rollback ne pukne
        DB::statement("
            INSERT INTO pages_old (
                id, title, slug, content, featured_image, image_caption, meta_title, meta_description,
                status, user_id, views, created_at, updated_at, published_at, page_type, layout, blocks
            )
            SELECT
                id, title, slug, COALESCE(content, ''), featured_image, image_caption, meta_title, meta_description,
                status, user_id, views, created_at, updated_at, published_at, page_type, layout, blocks
            FROM pages
        ");

        Schema::drop('pages');
        Schema::rename('pages_old', 'pages');
    }
};
