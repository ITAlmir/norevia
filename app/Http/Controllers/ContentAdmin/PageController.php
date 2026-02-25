<?php

namespace App\Http\Controllers\ContentAdmin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private function assertAccess(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->isContentAdmin() && !$user->isSuperAdmin())) {
            abort(403, 'Unauthorized');
        }
    }

    private function assertCanEdit(Page $page): void
    {
        $this->assertAccess();

        $user = auth()->user();
        if ($user->isContentAdmin() && $page->user_id !== $user->id) {
            abort(403, 'You can only edit your own pages.');
        }
    }

    private function blocksToContent(?array $blocks): string
    {
        if (!$blocks) return '';

        $out = [];
        foreach ($blocks as $b) {
            if (($b['type'] ?? null) === 'text') {
                $t = trim($b['content'] ?? '');
                if ($t !== '') $out[] = $t;
            }
        }

        return implode("\n\n", $out);
    }

   private function normalizeBlocks($blocks): array
{
    if (!is_array($blocks)) return [];

    $out = [];
    foreach ($blocks as $b) {
        $type = $b['type'] ?? null;

        // ✅ dodaj cta
        if (!in_array($type, ['text', 'image', 'cta'], true)) continue;

        if ($type === 'text') {
            $content = (string)($b['content'] ?? '');
            $content = trim($content);
            if ($content === '') continue;

            $out[] = [
                'type' => 'text',
                'content' => $content,
            ];
        }

        if ($type === 'image') {
            $src = (string)($b['src'] ?? '');
            $src = trim($src);

            // dozvoli i URL i base64 (data:image/...)
            $isDataImage = str_starts_with($src, 'data:image/');
            $isUrl = filter_var($src, FILTER_VALIDATE_URL);

            if (!$src || (!$isUrl && !$isDataImage)) continue;

            $caption = trim((string)($b['caption'] ?? ''));

            $out[] = [
                'type' => 'image',
                'src' => $src,
                'caption' => $caption,
            ];
        }

        // ✅ CTA block
        if ($type === 'cta') {
            $url = trim((string)($b['url'] ?? ''));
            if ($url === '') continue;

            // normalize URL (dodaj https ako fali)
            if (!preg_match('~^https?://~i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }

            $variant = (string)($b['variant'] ?? 'primary');
            if (!in_array($variant, ['primary','secondary','outline'], true)) {
                $variant = 'primary';
            }

            $out[] = [
                'type' => 'cta',
                'title'   => trim((string)($b['title'] ?? '')),
                'text'    => trim((string)($b['text'] ?? '')),
                'label'   => trim((string)($b['label'] ?? 'Open')),
                'url'     => $url,
                'variant' => $variant,
                'note'    => trim((string)($b['note'] ?? '')),
            ];
        }
    }

    return $out;
}

    private function uniqueSlug(string $title, ?string $slug = null, ?int $ignoreId = null): string
    {
        $base = $slug ? Str::slug($slug) : Str::slug($title);
        $candidate = $base;

        $q = Page::query();
        if ($ignoreId) $q->where('id', '!=', $ignoreId);

        $count = 1;
        while ($q->where('slug', $candidate)->exists()) {
            $candidate = $base . '-' . $count;
            $count++;
        }

        return $candidate;
    }

    public function index(Request $request)
{
    $this->assertAccess();
    $user = auth()->user();

    $q = Page::query()->with('author:id,name');

    // permissions
    if (!$user->isSuperAdmin()) {
        $q->where('user_id', $user->id);
    }

    // ✅ filters
    $q->when($request->filled('status'), fn($qq) => $qq->where('status', $request->string('status')));
    $q->when($request->filled('page_type'), fn($qq) => $qq->where('page_type', $request->string('page_type')));
    $q->when($request->filled('layout'), fn($qq) => $qq->where('layout', $request->string('layout')));
    $q->when($request->filled('type'), fn($qq) => $qq->where('type', $request->string('type')));

    // blog-only filter
    $q->when($request->filled('topic'), function($qq) use ($request) {
        $qq->where('type', 'blog')->where('topic', $request->string('topic'));
    });

    // search (title/slug/meta_description/content)
    $q->when($request->filled('search'), function ($qq) use ($request) {
        $s = trim((string) $request->input('search'));
        $qq->where(function ($w) use ($s) {
            $w->where('title', 'like', "%{$s}%")
              ->orWhere('slug', 'like', "%{$s}%")
              ->orWhere('meta_title', 'like', "%{$s}%")
              ->orWhere('meta_description', 'like', "%{$s}%")
              ->orWhere('content', 'like', "%{$s}%");
        });
    });

    // sort
    $sort = $request->input('sort', 'latest'); // latest|oldest|title
    if ($sort === 'title') $q->orderBy('title');
    elseif ($sort === 'oldest') $q->oldest();
    else $q->latest();

    $pages = $q->paginate(20)->withQueryString();

    return Inertia::render('ContentAdmin/Pages/Index', [
        'pages' => $pages,
        'isSuperAdmin' => $user->isSuperAdmin(),

        // ✅ vrati trenutno aktivne filtere nazad u UI
        'filters' => $request->only(['status','page_type','layout','type','topic','search','sort']),
    ]);
}


    public function show(Page $page)
{
    if ($page->type === 'blog') {
        // ako nema topic, bar da ne puca
        if (!$page->topic) abort(404);

        return redirect()->route('blog.show', [
            'topic' => $page->topic,
            'slug'  => $page->slug,
        ]);
    }

    return redirect()->route('pages.public.show', $page->slug);
}

    public function create()
    {
        $this->assertAccess();
        return Inertia::render('ContentAdmin/Pages/Create');
    }

    public function store(Request $request)
    {
        $this->assertAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',

            'status' => 'required|in:draft,published,archived',
            'page_type' => 'required|in:news,gaming,horoscope,post',
            'layout' => 'required|in:minimal,classic,magazine,hero',

            'featured_image' => 'nullable|url|max:500',
            'image_caption' => 'nullable|string|max:255',

            'blocks' => 'nullable|array',
            'blocks.*.type' => 'required_with:blocks|string|in:text,image,cta',
            'blocks.*.content' => 'nullable|string',
            'blocks.*.src' => 'nullable|string|max:500000',
            'blocks.*.caption' => 'nullable|string|max:255',
            'blocks.*.title'   => 'nullable|string|max:120',
            'blocks.*.text'    => 'nullable|string|max:500',
            'blocks.*.label'   => 'nullable|string|max:40',
            'blocks.*.url'     => 'nullable|string|max:2048',
            'blocks.*.variant' => 'nullable|string|in:primary,secondary,outline',
            'blocks.*.note'    => 'nullable|string|max:120',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'type' => ['nullable','string','in:blog,page'],
            'topic' => ['nullable','string','max:50'],  
                     
        ]);

        // Normalize type default
$validated['type'] = $validated['type'] ?? 'page';

// If NOT blog -> topic must be null
if ($validated['type'] !== 'blog') {
    $validated['topic'] = null;
}

// If blog -> topic is required
if ($validated['type'] === 'blog' && empty($validated['topic'])) {
    abort(422, 'Blog posts require a topic.');
}


        $validated['slug'] = $this->uniqueSlug($validated['title'], $validated['slug'] ?? null);

$blocks = $this->normalizeBlocks($request->input('blocks'));
$validated['blocks'] = $blocks;

// fallback content (za excerpt/search)
$validated['content'] = $this->blocksToContent($blocks);

        $validated['user_id'] = auth()->id();
        $validated['published_at'] = ($validated['status'] === 'published') ? now() : null;

        Page::create($validated);
        

        return redirect()->route('pages.index')
            ->with('success', 'Page created successfully!');
    }

    public function edit(Page $page)
    {
        $this->assertCanEdit($page);

        return Inertia::render('ContentAdmin/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $this->assertCanEdit($page);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,

            'status' => 'required|in:draft,published,archived',
            'page_type' => 'required|in:news,gaming,horoscope,post',
            'layout' => 'required|in:minimal,classic,magazine,hero',

            'featured_image' => 'nullable|string',
            'image_caption' => 'nullable|string|max:255',

            'blocks' => 'nullable|array',
            'blocks.*.type' => 'required_with:blocks|string|in:text,image,cta',
            'blocks.*.content' => 'nullable|string',
            'blocks.*.src' => 'nullable|string|max:500000',
            'blocks.*.caption' => 'nullable|string|max:255',
            'blocks.*.title'   => 'nullable|string|max:120',
            'blocks.*.text'    => 'nullable|string|max:500',
            'blocks.*.label'   => 'nullable|string|max:40',
            'blocks.*.url'     => 'nullable|string|max:2048',
            'blocks.*.variant' => 'nullable|string|in:primary,secondary,outline',
            'blocks.*.note'    => 'nullable|string|max:120',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',

            'type' => ['nullable','string','in:blog,page'],
            'topic' => ['nullable','string','max:50'],
        ]);

        $errors = [];
foreach (($request->input('blocks') ?? []) as $i => $b) {
    if (($b['type'] ?? null) === 'cta' && empty($b['url'])) {
        $errors["blocks.$i.url"] = "URL is required for CTA blocks.";
    }
}
if ($errors) {
    throw \Illuminate\Validation\ValidationException::withMessages($errors);
}
        $validated['type'] = $validated['type'] ?? ($page->type ?? 'page');

if ($validated['type'] !== 'blog') {
    $validated['topic'] = null;
}

if ($validated['type'] === 'blog' && empty($validated['topic'])) {
    abort(422, 'Blog posts require a topic.');
}


        // ensure unique slug (sa ignore id)
        $validated['slug'] = $this->uniqueSlug($validated['title'], $validated['slug'], $page->id);

        $incomingBlocks = $request->input('blocks', null);

// Ako Edit ne šalje blocks, ne diraj postojeće
if ($incomingBlocks === null) {
    unset($validated['blocks']);
} else {
    $blocks = $this->normalizeBlocks($incomingBlocks);
    $validated['blocks'] = $blocks;
    $validated['content'] = $this->blocksToContent($blocks);
}
        // published_at logic
        if ($page->status !== 'published' && $validated['status'] === 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] !== 'published') {
            $validated['published_at'] = null;
        }

        $page->update($validated);

        return redirect()->back()->with('success', 'Page updated successfully!');
    }

    public function destroy(Page $page)
    {
        $this->assertCanEdit($page);

        $page->delete();

        return redirect()->route('pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
