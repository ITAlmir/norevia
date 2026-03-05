<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Page extends Model
{
    protected $fillable = [
    'title',
    'slug',

    // public content
    'content',
    'blocks',

    // layout/type
    'page_type',
    'layout',

    // media
    'featured_image',
    'image_caption',

    // seo
    'meta_title',
    'meta_description',
    'canonical_url',
    'topic',
    'type',

    // status/author/time
    'status',
    'user_id',
    'published_at',

    // metrics (ako imaš kolonu)
    'views',
];




    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
        'blocks' => 'array',

    ];

    protected $appends = ['full_url', 'status_badge', 'excerpt'];

    // Automatski generiši slug ako nije unesen
    public static function boot()
{
    parent::boot();

    static::creating(function ($page) {
        if (empty($page->slug)) {
            $page->slug = Str::slug($page->title);

            $count = 1;
            $originalSlug = $page->slug;
            while (self::where('slug', $page->slug)->exists()) {
                $page->slug = $originalSlug . '-' . $count;
                $count++;
            }
        }

        if (empty($page->user_id) && auth()->check()) {
            $page->user_id = auth()->id();
        }
    });

    static::deleting(function (self $page) {

    // featured_image URL -> uploads/...
    if (!empty($page->featured_image)) {
        $path = self::publicPathFromUrl($page->featured_image);
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    // blocks images
    $blocks = is_array($page->blocks) ? $page->blocks : [];
    foreach ($blocks as $b) {
        if (($b['type'] ?? null) !== 'image') continue;

        $src = (string)($b['src'] ?? '');
        if (!$src) continue;

        $path = self::publicPathFromUrl($src);
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
});
}
// ✅ helper metode u modelu:
private static function isPublicDiskPath(string $value): bool
{
    $v = trim($value);

    if ($v === '') return false;
    if (str_starts_with($v, 'data:image/')) return false;

    // Ako je full URL, dozvoli samo ako je naš /storage/...
    if (preg_match('~^https?://~i', $v)) {
        return str_contains($v, '/storage/');
    }

    // relative path (npr "uploads/.." ili "pages/..")
    return true;
}

private static function stripPublicUrlToPath(string $value): string
{
    $v = trim($value);

    // full url: https://tvoj-sajt.com/storage/abc.jpg -> abc.jpg
    $pos = strpos($v, '/storage/');
    if ($pos !== false) {
        return ltrim(substr($v, $pos + strlen('/storage/')), '/');
    }

    // relative path ostaje isti
    return ltrim($v, '/');
}

    // Relationship sa autorom
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor za puni URL
    public function getFullUrlAttribute()
{
    return url('/pages/' . $this->slug);
}

    // Accessor za excerpt (prvih 150 karaktera)
    public function getExcerptAttribute()
{
    $text = $this->content ?? '';
    return Str::limit(strip_tags($text), 150);
}

    // Accessor za status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'published' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Published'],
            'draft' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Draft'],
            'archived' => ['class' => 'bg-gray-100 text-gray-800', 'text' => 'Archived'],
            default => ['class' => 'bg-gray-100 text-gray-800', 'text' => $this->status],
        };
    }

    // Scope za published stranice
    public function scopePublished($q)
{
    return $q->where('status', 'published');
}

public function scopeBlog($q)
{
    return $q->where('type', 'blog');
}


    // Scope za draft stranice
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Scope za moje stranice (za content admina)
    public function scopeMine($query)
    {
        return $query->where('user_id', auth()->id());
    }
    
    private static function publicPathFromUrl(string $value): ?string
{
    $v = trim($value);
    if ($v === '') return null;

    // ne diramo base64
    if (str_starts_with($v, 'data:image/')) return null;

    // Ako je full URL i ima /storage/, izvući dio poslije /storage/
    if (preg_match('~^https?://~i', $v)) {
        $pos = strpos($v, '/storage/');
        if ($pos === false) return null;

        $path = substr($v, $pos + strlen('/storage/')); // npr uploads/abc.jpg
        return ltrim($path, '/');
    }

    // Ako je već relative: uploads/abc.jpg
    return ltrim($v, '/');
}

}