<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'item_id',
        'slug',
        'title',
        'description',
        'version',
        'original_filename',
        'stored_path',
        'file_size',
        'sha256',
        'scan_status',
        'download_count',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }
}
