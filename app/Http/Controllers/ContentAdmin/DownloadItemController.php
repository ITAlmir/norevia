<?php

namespace App\Http\Controllers\ContentAdmin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;



class DownloadItemController extends Controller
{
    private function assertAccess(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->isContentAdmin() && !$user->isSuperAdmin())) {
            abort(403, 'Unauthorized');
        }
    }

    private function assertCanEdit(Item $item): void
    {
        $this->assertAccess();

        $user = auth()->user();
        if ($user->isContentAdmin() && $item->user_id !== $user->id) {
            abort(403, 'You can only edit your own downloads.');
        }
    }
    
    public function index()
{
    $this->assertAccess();
    $user = auth()->user();

    $items = Item::query()
        ->orderByDesc('created_at')
        ->paginate(20)
        ->through(function ($it) use ($user) {
            return [
                'id' => $it->id,
                'title' => $it->title,
                'slug' => $it->slug,
                'category' => $it->category,
                'download_count' => $it->download_count,
                'user_id' => $it->user_id,

                'thumbnail_url' => $it->thumbnail_path ? Storage::disk('public')->url($it->thumbnail_path) : null,

                'can_edit' => $user->isSuperAdmin() || $it->user_id === $user->id,
            ];
        });

    return Inertia::render('ContentAdmin/Downloads/Index', [
        'items' => $items,
        'isSuperAdmin' => $user->isSuperAdmin(),
        'authUserId' => $user->id,
    ]);
}

    public function create()
    {
        $this->assertAccess();
        return Inertia::render('ContentAdmin/Downloads/Create');

    }

    public function store(Request $request)
    {
        $this->assertAccess();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'os' => ['nullable', 'string', 'max:50'],
            'is_published' => ['nullable', 'boolean'],

            'file' => ['required', 'file', 'max:262144'], // 50MB
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:1'],

        ]);

        $slugBase = Str::slug($data['title']);
        $slug = $this->uniqueSlug($slugBase);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $sizeBytes = $file->getSize();

        $storedPath = $file->storeAs("downloads/{$slug}", $originalFilename, 'public');

        $absolute = Storage::disk('public')->path($storedPath);
        $sha256 = hash_file('sha256', $absolute);

        $thumbPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbName = 'thumb.' . $thumb->getClientOriginalExtension();
            $thumbPath = $thumb->storeAs("downloads/{$slug}", $thumbName, 'public');
        }

        $isPublished = (bool)($data['is_published'] ?? true);

        Item::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'version' => $data['version'] ?? null,
            'size_bytes' => $sizeBytes,
            'os' => $data['os'] ?? null,
            'file_path' => $storedPath,
            'original_filename' => $originalFilename,
            'thumbnail_path' => $thumbPath,
            'scan_status' => 'unknown',
            'sha256' => $sha256,
            'download_count' => 0,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
            'is_featured' => (bool)($data['is_featured'] ?? false),
            'featured_order' => $data['featured_order'] ?? null,
        ]);

        return redirect()->route('contentAdmin.downloads.index')
            ->with('success', 'Download item created.');
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 2;

        while (Item::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function edit(Item $item)
{
    $this->assertAccess();
    $this->assertCanEdit($item);
    return inertia('ContentAdmin/Downloads/Edit', [
        'item' => $item,
    ]);
}

    public function update(Request $request, Item $item)
{
    $this->assertAccess();
    $this->assertCanEdit($item);
    // TEMP DEBUG
\Log::info('UPDATE PAYLOAD', $request->all());

    $data = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'category' => ['required', 'string', 'max:50'],
        'description' => ['nullable', 'string'],
        'version' => ['nullable', 'string', 'max:50'],
        'os' => ['nullable', 'string', 'max:50'],
        'is_published' => ['nullable', 'boolean'],

        'is_featured' => ['nullable', 'boolean'],
        'featured_order' => ['nullable', 'integer', 'min:1'],

        'file' => ['nullable', 'file', 'max:51200'],
        'thumbnail' => ['nullable', 'image', 'max:4096'],
    ]);

    $item->title = $data['title'];
    $item->category = $data['category'];
    $item->description = $data['description'] ?? null;
    $item->version = $data['version'] ?? null;
    $item->os = $data['os'] ?? null;

    $isPublished = (bool)($data['is_published'] ?? $item->is_published);
    $item->is_published = $isPublished;
    $item->published_at = $isPublished ? ($item->published_at ?? now()) : null;

    $item->is_featured = (bool)($data['is_featured'] ?? false);
    $item->featured_order = $data['featured_order'] ?? null;

    // replace main file
    if ($request->hasFile('file')) {
        $oldPath = $item->file_path;

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $sizeBytes = $file->getSize();

        $storedPath = $file->storeAs("downloads/{$item->slug}", $originalFilename, 'public');

        $absolute = Storage::disk('public')->path($storedPath);
        $sha256 = hash_file('sha256', $absolute);

        $item->file_path = $storedPath;
        $item->original_filename = $originalFilename;
        $item->size_bytes = $sizeBytes;
        $item->sha256 = $sha256;

        if ($oldPath && $oldPath !== $storedPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    // replace thumbnail
    if ($request->hasFile('thumbnail')) {
        $oldThumb = $item->thumbnail_path;

        $thumb = $request->file('thumbnail');
        $thumbName = 'thumb.' . $thumb->getClientOriginalExtension();
        $thumbPath = $thumb->storeAs("downloads/{$item->slug}", $thumbName, 'public');

        $item->thumbnail_path = $thumbPath;

if ($oldThumb && $oldThumb !== $thumbPath && Storage::disk('public')->exists($oldThumb)) {
    Storage::disk('public')->delete($oldThumb);
}
    }

    $item->save();

    return redirect()->route('contentAdmin.downloads.index')->with('success', 'Updated.');
}

    public function destroy(Item $item)
{
    $this->assertAccess();
    $this->assertCanEdit($item);
    // Brišemo cijeli folder downloads/{slug} (sigurno jer mi tu spremamo fajlove)
    $folder = "downloads/{$item->slug}";

    if (Storage::disk('public')->exists($folder)) {
    Storage::disk('public')->deleteDirectory($folder);
}

    // Obriši DB record
    $item->delete();

    return redirect()->route('contentAdmin.downloads.index')
        ->with('success', 'Download deleted.');
}

}
