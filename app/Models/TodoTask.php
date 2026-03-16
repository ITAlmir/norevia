<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TodoTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_item_id',
        'title',
        'profile_name',
        'content_bucket',
        'shared_content_group',
        'platform',
        'series',
        'voice_tool',
        'scheduled_for',
        'publish_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'cleared_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planItem()
    {
        return $this->belongsTo(MonthlyPlanItem::class, 'plan_item_id');
    }
}