<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        // list topics + latest posts
        $topics = Page::query()
            ->published()
            ->blog()
            ->whereNotNull('topic')
            ->select('topic')
            ->distinct()
            ->orderBy('topic')
            ->pluck('topic');

        $latest = Page::query()
            ->published()
            ->blog()
            ->orderByDesc('published_at')
            ->select('id','title','slug','topic','page_type','meta_description','featured_image','published_at')
            ->limit(12)
            ->get();

        return Inertia::render('Blog/Index', [
            'topics' => $topics,
            'latest' => $latest,
        ]);
    }

    public function topic(string $topic)
    {
        $posts = Page::query()
            ->published()
            ->blog()
            ->where('topic', $topic)
            ->orderByDesc('published_at')
            ->select('id','title','slug','topic','page_type','meta_description','featured_image','published_at')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Blog/Topic', [
            'topic' => $topic,
            'posts' => $posts,
        ]);
    }

    public function show(string $topic, string $slug)
    {
        $page = Page::query()
            ->published()
            ->blog()
            ->where('topic', $topic)
            ->where('slug', $slug)
            ->firstOrFail();

        // related posts for internal linking
        $related = Page::query()
            ->published()
            ->blog()
            ->where('topic', $topic)
            ->where('id', '!=', $page->id)
            ->orderByDesc('published_at')
            ->select('title','slug','topic')
            ->limit(8)
            ->get();

        $path = "/blog/{$page->topic}/{$page->slug}";
$canonical = rtrim(config('app.url'), '/') . $path;

$seo = [
  'title' => $page->meta_title ?: $page->title,
  'description' => $page->meta_description ?: '',
  'canonical' => $canonical,
  'og' => [
    'og:title' => $page->meta_title ?: $page->title,
    'og:description' => $page->meta_description ?: '',
    'og:type' => 'article',
    'og:url' => $canonical,
  ],
  'twitter' => [
    'twitter:card' => 'summary',
    'twitter:title' => $page->meta_title ?: $page->title,
    'twitter:description' => $page->meta_description ?: '',
  ],
  'schemaGraph' => [
    [
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>rtrim(config('app.url'),'/').'/'],
        ['@type'=>'ListItem','position'=>2,'name'=>'Blog','item'=>rtrim(config('app.url'),'/').'/blog'],
        ['@type'=>'ListItem','position'=>3,'name'=>$page->topic,'item'=>rtrim(config('app.url'),'/')."/blog/{$page->topic}"],
        ['@type'=>'ListItem','position'=>4,'name'=>$page->title,'item'=>$canonical],
      ],
    ],
    [
      '@type' => 'BlogPosting',
      'headline' => $page->meta_title ?: $page->title,
      'description' => $page->meta_description ?: '',
      'mainEntityOfPage' => $canonical,
      'url' => $canonical,
      'datePublished' => optional($page->published_at)->toAtomString(),
      'dateModified' => optional($page->updated_at)->toAtomString(),
      'author' => ['@type' => 'Organization', 'name' => 'Norevia'],
      'publisher' => ['@type' => 'Organization', 'name' => 'Norevia'],
    ],
  ],
];
    

        return Inertia::render('Blog/Show', [
  'page' => $page,
  'related' => $related,
  'seo' => $seo,
]);

    }
}
