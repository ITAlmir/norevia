<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}