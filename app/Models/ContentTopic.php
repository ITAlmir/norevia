<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'platform',
        'series',
        'topic_code',
        'title',
        'voice_tool',
        'content_bucket',
        'shared_content_group',
        'caption',
        'description',
        'hashtags',
        'script_notes',
        'status',
    ];
}