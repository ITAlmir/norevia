<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->firstOrFail();

        // ✅ blog pages idu na /blog/{topic}/{slug}
        if ($page->type === 'blog' && $page->topic) {
            return redirect()->route('blog.show', [
                'topic' => $page->topic,
                'slug'  => $page->slug,
            ], 301);
        }

        // ✅ samo published stranice javno (ako želiš)
        // Ako hoćeš da draft radi lokalno za preview, možeš ovo usloviti sa auth/admin.
        if ($page->status !== 'published') {
            abort(404);
        }

        // RELATED: iste "kategorije" za page = page_type
        $related = Page::query()
            ->where('status', 'published')
            ->where('type', 'page')
            ->where('page_type', $page->page_type)
            ->where('id', '!=', $page->id)
            ->orderByDesc('published_at')
            ->take(6)
            ->get([
                'id','title','slug','page_type','layout','featured_image','published_at',
            ]);

        // LATEST: najnovije page stranice
        $latest = Page::query()
            ->where('status', 'published')
            ->where('type', 'page')
            ->orderByDesc('published_at')
            ->take(6)
            ->get([
                'id','title','slug','page_type','layout','featured_image','published_at',
            ]);

        // POPULAR: ako imaš "views" kolonu, super. Ako nemaš, promijeni na created_at ili skini popular sekciju.
        $popular = Page::query()
            ->where('status', 'published')
            ->where('type', 'page')
            ->orderByDesc('views')
            ->take(6)
            ->get([
                'id','title','slug','page_type','layout','featured_image','published_at','views',
            ]);

        $base = rtrim(config('app.url'), '/');
        $path = "/pages/{$page->slug}";
        $canonical = $base . $path;

        return Inertia::render('Pages/Show', [
            'page' => $page,
            'related' => $related,
            'latest' => $latest,
            'popular' => $popular,
            'seo' => [
                'title' => $page->meta_title ?: $page->title,
                'description' => $page->meta_description ?: '',
                'canonical' => $canonical,
                'robots' => 'index,follow',
                'og' => [
                    'og:title' => $page->meta_title ?: $page->title,
                    'og:description' => $page->meta_description ?: '',
                    'og:type' => 'website',
                    'og:url' => $canonical,
                ],
            ],
        ]);
    }
}