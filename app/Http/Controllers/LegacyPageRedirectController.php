<?php

namespace App\Http\Controllers;

use App\Models\Page;

class LegacyPageRedirectController extends Controller
{
    public function __invoke(Page $page)
    {
        // ako page ima topic i type=blog, canonical je /blog/{topic}/{slug}
        if ($page->type === 'blog' && $page->topic) {
            return redirect()->route('blog.show', [
                'topic' => $page->topic,
                'slug' => $page->slug,
            ], 301);
        }

        // fallback: ostavi kako jeste (možeš kasnije napraviti /pages/ kao “landing”/legal)
        return redirect("/pages/{$page->slug}", 302);
    }
}
