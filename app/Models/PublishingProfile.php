<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PublishingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'name',
    'platform',
    'daily_target',
    'default_voice_tool',
    'default_publish_time',
    'is_active',
];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}