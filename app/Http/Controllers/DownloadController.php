<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\DownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class DownloadController extends Controller
{
    public function publicIndex(Request $request)
{
    $search = $request->get('search');
    $category = $request->get('category');
    $sort = $request->get('sort', 'new');

    $query = Item::query()
        ->where('is_published', true);

    // SEARCH
    if ($search) {
        $query->where('title', 'like', "%{$search}%");
    }

    // CATEGORY
    if ($category) {
        $query->where('category', $category);
    }

    // SORT
    if ($sort === 'top') {
        $query->orderByDesc('download_count');
    } else {
        $query->latest();
    }

    $items = $query->paginate(9)->withQueryString();

    $featured = Item::where('is_featured', true)
        ->where('is_published', true)
        ->latest()
        ->take(6)
        ->get();

    return Inertia::render('Downloads/Index', [
    'items' => $items->through(fn($i) => [
        'id' => $i->id,
        'title' => $i->title,
        'slug' => $i->slug,
        'category' => $i->category,
        'version' => $i->version,
        'download_count' => (int)$i->download_count,
        'scan_status' => $i->scan_status,
        'is_featured' => (bool)$i->is_featured,
    ]),
    'featured' => $featured->map(fn($i) => [
        'id' => $i->id,
        'title' => $i->title,
        'slug' => $i->slug,
        'category' => $i->category,
        'version' => $i->version,
        'download_count' => (int)$i->download_count,
        'scan_status' => $i->scan_status,
        'is_featured' => (bool)$i->is_featured,
    ])->values(),
    'filters' => [
        'search' => $search,
        'category' => $category,
        'sort' => $sort,
    ],
]);

}




    public function index()
    {
        // Ako želiš da index radi i za goste, makni where('user_id', ...)
        $downloads = DownloadLog::where('user_id', auth()->id())
            ->with('item')
            ->latest()
            ->paginate(20);

        return inertia('Downloads/Index', [
            'downloads' => $downloads,
        ]);
    }

    public function downloadFree(Item $item, Request $request)
{
    if (!$item->is_free) {
        abort(403, 'This item is not free.');
    }

    if (empty($item->file_path) || !Storage::disk('local')->exists($item->file_path)) {
        abort(404, 'File not found.');
    }

    DownloadLog::create([
        'user_id' => auth()->id(),
        'item_id' => $item->id,
        'ip_address' => $request->ip(),
        'user_agent' => (string) $request->userAgent(),
        'is_guest' => auth()->check() ? 0 : 1,
    ]);

    $item->increment('download_count');

    return Storage::disk('local')->download($item->file_path);
}


    public function download(Item $item, Request $request)
{
    if (!$item->is_free && !auth()->check()) {
        return redirect()->route('login');
    }

    if (empty($item->file_path) || !Storage::disk('local')->exists($item->file_path)) {
        abort(404, 'File not found.');
    }

    DownloadLog::create([
        'user_id' => auth()->id(),
        'item_id' => $item->id,
        'ip_address' => $request->ip(),
        'user_agent' => (string) $request->userAgent(),
        'is_guest' => auth()->check() ? 0 : 1,
    ]);

    $item->increment('download_count');

    return Storage::disk('local')->download($item->file_path);
}

public function showBySlug(string $slug)
{
    \Log::info('HIT showBySlug', ['slug' => $slug]);

    $item = Item::where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    $related = Item::query()
        ->where('is_published', true)
        ->where('category', $item->category)
        ->where('id', '!=', $item->id)
        ->orderByDesc('download_count')
        ->take(6)
        ->get(['id','title','slug','category','version','download_count']);

    // SEO payload
    $path = "/downloads/{$item->slug}";
    $base = rtrim(config('app.url'), '/'); // APP_URL iz .env
    $canonical = $base . $path;

    $seo = [
        'title' => "{$item->title} | DigitalLab Downloads",
        'description' => $item->description ?: 'Free utility tool download.',
        'canonical' => $canonical,
        'og' => [
            'og:title' => "{$item->title} | DigitalLab Downloads",
            'og:description' => $item->description ?: '',
            'og:type' => 'website',
            'og:url' => $canonical,
        ],
        'twitter' => [
            'twitter:card' => 'summary',
            'twitter:title' => "{$item->title} | DigitalLab Downloads",
            'twitter:description' => $item->description ?: '',
        ],
        'schemaGraph' => [
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>$base.'/'],
                    ['@type'=>'ListItem','position'=>2,'name'=>'Downloads','item'=>$base.'/downloads'],
                    ['@type'=>'ListItem','position'=>3,'name'=>$item->title,'item'=>$canonical],
                ],
            ],
            [
                '@type' => 'SoftwareApplication',
                'name' => $item->title,
                'applicationCategory' => 'UtilitiesApplication',
                'operatingSystem' => $item->os ?: 'Windows',
                'softwareVersion' => $item->version ?: null,
                'description' => $item->description ?: '',
                'url' => $canonical,
            ],
        ],
    ];

    return Inertia::render('Downloads/Show', [
        'item' => [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'description' => $item->description,
            'category' => $item->category,
            'version' => $item->version,
            'os' => $item->os,
            'download_count' => (int)$item->download_count,
            'size_bytes' => $item->size_bytes,
            'thumbnail_path' => $item->thumbnail_path,
            'original_filename' => $item->original_filename,
        ],
        'related' => $related->map(fn($r) => [
            'id' => $r->id,
            'title' => $r->title,
            'slug' => $r->slug,
            'category' => $r->category,
            'version' => $r->version,
            'download_count' => (int)$r->download_count,
        ])->values(),
        'seo' => $seo,
    ]);
}



public function downloadBySlug(string $slug)
{
    $item = Item::where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

    // ✅ Ako želiš log i za goste, pazi na user_id null (vidi napomenu ispod)
    DownloadLog::create([
        'user_id' => auth()->check() ? auth()->id() : null,
        'item_id' => $item->id,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'is_guest' => !auth()->check(),
    ]);

    $item->increment('download_count');

    // normalizuj path (za svaki slučaj)
    $path = ltrim($item->file_path ?? '', '/');
    $path = preg_replace('#^public/#', '', $path);

    \Log::info('DOWNLOAD DEBUG', [
        'slug' => $slug,
        'file_path_db' => $item->file_path,
        'path_used' => $path,
        'original_filename' => $item->original_filename,
        'exists_public' => Storage::disk('public')->exists($path),
        'public_abs' => Storage::disk('public')->path($path),
    ]);

    abort_unless(Storage::disk('public')->exists($path), 404, 'File missing on server.');

    return Storage::disk('public')->download(
        $path,
        $item->original_filename ?: basename($path)
    );
}

public function latestWidget(Request $request)
{
    $items = Item::query()
        ->where('is_published', true)
        ->orderByDesc('created_at')
        ->take(6)
        ->get(['id','title','slug','category','version','os','download_count','scan_status','is_featured']);

    return response()->json([
        'items' => $items->map(fn($i) => [
            'id' => $i->id,
            'title' => $i->title,
            'slug' => $i->slug,
            'category' => $i->category,
            'version' => $i->version,
            'download_count' => (int)$i->download_count,
            'scan_status' => $i->scan_status,
            'is_featured' => (bool)$i->is_featured,
            'os' => $i->os,
        ])->values(),
    ]);
}



}
