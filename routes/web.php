<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ContentAdmin\PageController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\CollaborationController;
use App\Http\Controllers\SponsorController;

use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ReminderSettingsController;

use App\Models\Page;

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ContentAdmin\DownloadItemController;
use App\Http\Controllers\BlogController;

use App\Http\Controllers\LegacyPageRedirectController;
use App\Http\Controllers\PublicPageController;




// --------------------
// Public routes... blog rute moraju biti na vrhu
// --------------------
   Route::get('/demo', fn () => Inertia::render('Demo/Index'))->name('demo');

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{topic}', [BlogController::class, 'topic'])->name('blog.topic');
    Route::get('/blog/{topic}/{slug}', [BlogController::class, 'show'])->name('blog.show');



// --------------------
// Download routes
// --------------------

    Route::get('/downloads', [\App\Http\Controllers\DownloadController::class, 'publicIndex'])
    ->name('downloads.index');

    Route::get('/downloads/{slug}', [\App\Http\Controllers\DownloadController::class, 'showBySlug'])
    ->name('downloads.show');

    Route::get('/downloads/{slug}/download', [\App\Http\Controllers\DownloadController::class, 'downloadBySlug'])
    ->name('downloads.download');
    
    Route::get('/partials/latest-downloads', [\App\Http\Controllers\DownloadController::class, 'latestWidget'])
    ->name('downloads.latestWidget');
//----------------------------------
// 3) Legacy / pages / fallback (šire rute)
//-----------------------------------



// --------------------
// CONTENT ADMIN GROUP
// --------------------
Route::middleware(['auth'])->prefix('content-admin')->group(function () {

    // Dashboard
    Route::get('/', function () {
        $user = auth()->user();
        if (!$user->isContentAdmin() && !$user->isSuperAdmin()) abort(403, 'Unauthorized');

        $stats = [
            'totalPages' => Page::where('user_id', $user->id)->count(),
            'publishedPages' => Page::where('user_id', $user->id)->where('status', 'published')->count(),
            'totalItems' => \App\Models\Item::count(),
            'totalCategories' => \App\Models\Category::count(),
        ];

        $recentPages = Page::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($page) => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'excerpt' => $page->excerpt,
                'status' => $page->status,
                'created_at' => $page->created_at,
            ]);

        return Inertia::render('ContentAdmin/Dashboard', [
            'stats' => $stats,
            'recentPages' => $recentPages,
            'url' => url('/'),
        ]);
    })->name('content.dashboard');

    Route::get('/pages/create-blog', function () {
    $user = auth()->user();
    if (!$user->isContentAdmin() && !$user->isSuperAdmin()) abort(403);

    return Inertia::render('ContentAdmin/Pages/Create', [
        'prefill' => [
            'type' => 'blog',
            'topic' => 'cs2',
            'status' => 'draft',
        ],
    ]);
})->name('content.pages.createBlog');


    // --------------------
    // DOWNLOAD ADMIN ROUTES
    // --------------------
    Route::get('/downloads', [DownloadItemController::class, 'index'])
        ->name('contentAdmin.downloads.index');

    Route::get('/downloads/create', [DownloadItemController::class, 'create'])
        ->name('contentAdmin.downloads.create');

    Route::post('/downloads', [DownloadItemController::class, 'store'])
        ->name('contentAdmin.downloads.store');

    Route::get('/downloads/{item}/edit', [DownloadItemController::class, 'edit'])
    ->name('contentAdmin.downloads.edit');

     Route::put('/downloads/{item}', [DownloadItemController::class, 'update'])
    ->name('contentAdmin.downloads.update');

     Route::delete('/downloads/{item}', [DownloadItemController::class, 'destroy'])
    ->name('contentAdmin.downloads.destroy');

        Route::get('/downloads/analytics', [\App\Http\Controllers\ContentAdmin\DownloadAnalyticsController::class, 'index'])
        ->name('contentAdmin.downloads.analytics');



});

// --------------------
// Public routes
// --------------------
Route::get('/', fn () => Inertia::render('Welcome', [
    'seo' => [
        'title' => 'Home',
        'description' => 'Utility tools, CS2 performance guides, PC optimization and creator resources.',
        'canonical' => rtrim(config('app.url'), '/').'/',
        'og' => [
            'og:title' => 'Home',
            'og:description' => 'Utility tools, CS2 performance guides, PC optimization and creator resources.',
            'og:type' => 'website',
            'og:url' => rtrim(config('app.url'), '/').'/',
        ],
    ],
]))->name('home');


Route::get('/cs2', fn () =>
    Inertia::render('Hubs/CS2')
)->name('hub.cs2');

Route::get('/pc-optimization', fn () =>
    Inertia::render('Hubs/PCOptimization')
)->name('hub.pc');

Route::get('/creator-tools', fn () =>
    Inertia::render('Hubs/CreatorTools')
)->name('hub.creator');


Route::get('/gaming', fn () => Inertia::render('Gaming/Index'))->name('gaming');
Route::get('/tools', fn () => Inertia::render('Tools/Index'))->name('tools');
Route::get('/apps', fn () => Inertia::render('Apps/Index'))->name('apps');
Route::redirect('/gaming', '/blog/cs2', 301);
Route::redirect('/tools', '/blog/pc-optimization', 301);
Route::redirect('/apps', '/blog/creator-tools', 301);


Route::get('/search', function (\Illuminate\Http\Request $request) {
    return Inertia::render('Search/Index', [
        'q' => $request->get('q', ''),
        'seo' => [
  'title' => 'Search',
  'description' => 'Search results.',
  'canonical' => rtrim(config('app.url'), '/').'/search',
  'robots' => 'noindex,follow',
],
    ]);
})->name('search');


Route::get('/pages/{page:slug}', function (Page $page) {
    if ($page->status !== 'published' && !auth()->check()) abort(404);

    $page->increment('views');
    $page->load('author');

    // ✅ gost vidi samo published, ulogovan vidi i draft
    $allowedStatuses = auth()->check() ? ['published', 'draft'] : ['published'];

    $related = Page::query()
        ->whereIn('status', $allowedStatuses)
        ->where('page_type', $page->page_type)
        ->where('id', '!=', $page->id)
        ->latest('published_at')
        ->take(6)
        ->get(['id','title','slug','published_at','featured_image','page_type','blocks']);

    $latest = Page::query()
        ->whereIn('status', $allowedStatuses)
        ->latest('published_at')
        ->take(6)
        ->get(['id','title','slug','published_at','featured_image','page_type','blocks']);

    $popular = Page::query()
        ->whereIn('status', $allowedStatuses)
        ->orderByDesc('views')
        ->take(6)
        ->get(['id','title','slug','published_at','featured_image','page_type','views','blocks']);

    // -------------------------
    // ✅ SEO payload for MainLayout SeoHead
    // -------------------------
    $base = rtrim(config('app.url'), '/');
    $path = "/pages/{$page->slug}";
    $canonical = $page->canonical_url ?: ($base . $path);

    $title = $page->meta_title ?: $page->title;

    // Description priority:
    // 1) meta_description
    // 2) excerpt (from content accessor)
    // 3) first text block (if excerpt empty)
    $desc = $page->meta_description ?: ($page->excerpt ?? '');
    if (!$desc && is_array($page->blocks)) {
        foreach ($page->blocks as $b) {
            if (($b['type'] ?? null) === 'text' && !empty($b['content'])) {
                $desc = trim(strip_tags($b['content']));
                break;
            }
        }
    }
    $desc = mb_substr($desc, 0, 160);

    $seo = [
        'title' => $title,
        'description' => $desc,
        'canonical' => $canonical,
        'og' => [
            'og:title' => $title,
            'og:description' => $desc,
            'og:type' => 'article',
            'og:url' => $canonical,
            // (opciono) og:image - kad uvedemo featured_image URL normalizaciju
        ],
        'twitter' => [
            'twitter:card' => 'summary',
            'twitter:title' => $title,
            'twitter:description' => $desc,
        ],
        // JSON-LD za pages nije obavezan — dodaćemo kasnije za blog posts
    ];

    return inertia('Pages/Pages/Show', [
        'page' => $page,
        'related' => $related,
        'latest' => $latest,
        'popular' => $popular,
        'seo' => $seo,
    ]);
})->name('pages.public.show');

//----------------------
//SEO SITEMAPS
//-----------------------v

Route::get('/sitemap.xml', function () {

    $base = rtrim(config('app.url'), '/');

    // 1) Static core URLs (canonical entry points)
    $static = [
        ['loc' => $base.'/', 'priority' => '1.0'],
        ['loc' => $base.'/downloads', 'priority' => '0.9'],
        ['loc' => $base.'/blog', 'priority' => '0.9'],

        // Canonical hubs (SEO pillars)
        ['loc' => $base.'/blog/cs2', 'priority' => '0.85'],
        ['loc' => $base.'/blog/pc-optimization', 'priority' => '0.85'],
        ['loc' => $base.'/blog/creator-tools', 'priority' => '0.85'],

        // Optional: gaming hub (ONLY if you want it as hub)
        // ['loc' => $base.'/blog/gaming', 'priority' => '0.75'],
    ];

    // 2) Generator pages (NON-blog)
    $pages = \App\Models\Page::query()
        ->where('status', 'published')
        ->where(function ($q) {
            $q->whereNull('type')->orWhere('type', 'page'); // generator pages
        })
        ->get(['slug', 'updated_at']);

    // 3) Blog posts
    $blogPosts = \App\Models\Page::query()
        ->where('status', 'published')
        ->where('type', 'blog')
        ->whereNotNull('topic')
        ->get(['slug', 'topic', 'updated_at', 'published_at']);

    // 4) Downloads
    $downloads = \App\Models\Item::query()
        ->where('is_published', true)
        ->get(['slug', 'updated_at']);

    // XML
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Static
    foreach ($static as $u) {
        $xml .= '<url>';
        $xml .= '<loc>'.$u['loc'].'</loc>';
        $xml .= '<priority>'.$u['priority'].'</priority>';
        $xml .= '</url>';
    }

    // Pages (non-blog)
    foreach ($pages as $p) {
        $xml .= '<url>';
        $xml .= '<loc>'.$base.'/pages/'.$p->slug.'</loc>';
        if ($p->updated_at) {
            $xml .= '<lastmod>'.$p->updated_at->toAtomString().'</lastmod>';
        }
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
    }

    // Blog posts
    foreach ($blogPosts as $bp) {
        $xml .= '<url>';
        $xml .= '<loc>'.$base.'/blog/'.$bp->topic.'/'.$bp->slug.'</loc>';

        // Prefer updated_at, fallback published_at if needed
        $last = $bp->updated_at ?: $bp->published_at;
        if ($last) {
            $xml .= '<lastmod>'.$last->toAtomString().'</lastmod>';
        }

        $xml .= '<priority>0.75</priority>';
        $xml .= '</url>';
    }

    // Downloads
    foreach ($downloads as $d) {
        $xml .= '<url>';
        $xml .= '<loc>'.$base.'/downloads/'.$d->slug.'</loc>';
        if ($d->updated_at) {
            $xml .= '<lastmod>'.$d->updated_at->toAtomString().'</lastmod>';
        }
        $xml .= '<priority>0.7</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)
        ->header('Content-Type', 'application/xml');
});



// --------------------
// Dashboard (auth)
// --------------------
Route::get('/dashboard', function () {
    $snap = \App\Support\FollowUpReminder::snapshotFor(auth()->user(), 5);

    return Inertia::render('Dashboard', [
        'reminders' => $snap,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// --------------------
// Authenticated routes (SESSION auth)
// --------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Super admin
    Route::get('/super-admin', [SuperAdminController::class, 'dashboard'])->name('super.dashboard');
    Route::get('/super-admin/users', [SuperAdminController::class, 'users'])->name('super.users');
    Route::post('/super-admin/users/{user}/role', [SuperAdminController::class, 'updateRole']);

    // Upload image (auth already applied by group)
    Route::post('/upload-image', [ImageUploadController::class, 'store']);

    // Page CRUD
    Route::resource('/content-admin/pages', PageController::class)->names('pages');

    // Items/Categories screens
    Route::get('/admin/items/create', function () {
        $user = auth()->user();
        if (!$user->isContentAdmin() && !$user->isSuperAdmin()) abort(403, 'Unauthorized');
        return Inertia::render('Profile/Items/Create');
    })->name('items.create');

    Route::get('/admin/categories', function () {
        $user = auth()->user();
        if (!$user->isContentAdmin() && !$user->isSuperAdmin()) abort(403, 'Unauthorized');
        return Inertia::render('Categories/Index');
    })->name('categories.index');

    // --------------------
    // Sponsors + Collaborations (SESSION auth, NOT sanctum)
    // --------------------
    Route::get('/sponsors', [SponsorController::class, 'index']);
    Route::post('/sponsors', [SponsorController::class, 'store']);
    Route::delete('/sponsors/{sponsor}', [SponsorController::class, 'destroy']);

    Route::get('/collaborations', [CollaborationController::class, 'index']);
    Route::post('/collaborations', [CollaborationController::class, 'store']);
    Route::patch('/collaborations/{id}/status', [CollaborationController::class, 'updateStatus']);
    Route::patch('/collaborations/{id}/follow-up', [CollaborationController::class, 'updateFollowUp']);
    Route::delete('/collaborations/{collaboration}', [CollaborationController::class, 'destroy']);

    // --------------------
    // Reminders (SESSION auth)
    // --------------------
    Route::patch('/settings/reminders', [ReminderSettingsController::class, 'update']);
    Route::get('/reminders/upcoming', [ReminderController::class, 'upcoming']);
    Route::post('/reminders/rebuild', [ReminderController::class, 'rebuild']);
    Route::post('/reminders/{reminder}/toggle', [ReminderController::class, 'toggle'])->middleware('auth');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->middleware('auth');



});

// Breeze auth routes
require __DIR__ . '/auth.php';
