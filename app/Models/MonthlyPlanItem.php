<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'monthly_plan_id',
    'user_id',
    'content_topic_id',
    'plan_date',
    'task_title',
    'platform',
    'series',
    'voice_tool',
    'publish_time',
    'status',
];

    protected $casts = [
        'plan_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monthlyPlan()
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function contentTopic()
    {
        return $this->belongsTo(ContentTopic::class);
    }
}