<?php

namespace App\Models;

use Carbon\Carbon;
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
        'publish_times',
        'schedule_type',
        'schedule_days',
        'description',
        'use_shared_content',
        'content_group',
        'content_bucket',
        'is_active',
    ];

    protected $casts = [
        'publish_times' => 'array',
        'schedule_days' => 'array',
        'use_shared_content' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function resolvedPublishTimes(): array
    {
        $times = collect($this->publish_times ?? [])
            ->filter()
            ->map(fn ($time) => trim((string) $time))
            ->filter()
            ->values()
            ->all();

        if (!empty($times)) {
            return $times;
        }

        if (!empty($this->default_publish_time)) {
            return [$this->default_publish_time];
        }

        return [];
    }

    public function matchesDate(Carbon $date): bool
    {
        $scheduleType = $this->schedule_type ?: 'daily';
        $scheduleDays = collect($this->schedule_days ?? [])->map(fn ($day) => (int) $day)->all();

        if ($scheduleType === 'daily') {
            return true;
        }

        if ($scheduleType === 'weekly') {
            if (empty($scheduleDays)) {
                return true;
            }

            return in_array($date->dayOfWeekIso, $scheduleDays, true);
        }

        if ($scheduleType === 'monthly') {
            if (empty($scheduleDays)) {
                return true;
            }

            return in_array($date->day, $scheduleDays, true);
        }

        return true;
    }
}