<?php

namespace App\Support;

use App\Models\Collaboration;
use App\Models\User;

class FollowUpReminder
{
    public static function snapshotFor(User $user, int $limit = 5): array
    {

        $base = Collaboration::query()
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['finished','lost'])
            ->whereNotNull('follow_up_date')
            ->with('sponsor:id,name,email')
            ->get(['id','title','status','follow_up_date','sponsor_id']);

        $today = now()->startOfDay();

$overdue = $base->filter(function ($c) use ($today) {
    $d = \Illuminate\Support\Carbon::parse($c->follow_up_date)->startOfDay();
    return $d->lt($today);
});

$todayItems = $base->filter(function ($c) use ($today) {
    $d = \Illuminate\Support\Carbon::parse($c->follow_up_date)->startOfDay();
    return $d->eq($today);
});

        $map = fn ($c) => [
            'id' => $c->id,
            'title' => $c->title,
            'status' => $c->status,
            'follow_up_date' => \Illuminate\Support\Carbon::parse($c->follow_up_date)->toDateString(),
            'sponsor' => [
                'name' => $c->sponsor?->name,
                'email' => $c->sponsor?->email,
            ],
        ];

        return [
    'today' => $today->toDateString(),
    'overdueCount' => $overdue->count(),
    'todayCount' => $todayItems->count(),
    'overdueTop' => $overdue->take($limit)->map($map)->values()->all(),
    'todayTop' => $todayItems->take($limit)->map($map)->values()->all(),
    'emailItems' => $overdue->concat($todayItems)->sortBy('follow_up_date')->take(20)->map($map)->values()->all(),
];

    }
}
