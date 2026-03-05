<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
    'title',
    'slug',
    'description',
    'category',
    'version',
    'size_bytes',
    'os',
    'file_path',
    'original_filename',
    'thumbnail_path',
    'scan_status',
    'sha256',
    'download_count',
    'is_published',
    'published_at',
    'is_featured',
    'featured_order',

];


    protected $casts = [
        'is_free' => 'boolean',
        'rating' => 'decimal:1',
        'price' => 'decimal:2',
        'downloads' => 'integer'
    ];

    public function downloadLogs()
    {
        return $this->hasMany(DownloadLog::class);
    }

    protected static function booted()
{
    static::deleting(function (self $item) {

        // Briši cijeli folder gdje čuvaš fajlove: downloads/{slug}
        if (!empty($item->slug)) {
            $folder = "downloads/{$item->slug}";
            if (Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->deleteDirectory($folder);
            }
        } else {
            // fallback: ako nema slug iz nekog razloga, briši pojedinačne putanje
            if (!empty($item->thumbnail_path)) {
                Storage::disk('public')->delete($item->thumbnail_path);
            }
            if (!empty($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
        }
    });
}
}