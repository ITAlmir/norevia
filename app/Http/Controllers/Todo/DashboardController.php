<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\ContentTopic;
use App\Models\MonthlyPlanItem;
use App\Models\PublishingProfile;
use App\Models\TodoTask;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $this->syncTasksForToday($user->id, $today->toDateString());

        $todayTasks = TodoTask::query()
            ->where('user_id', $user->id)
            ->whereDate('scheduled_for', $today)
            ->orderBy('publish_time')
            ->latest('id')
            ->get([
                'id',
                'title',
                'platform',
                'series',
                'voice_tool',
                'scheduled_for',
                'publish_time',
                'status',
            ]);

        $lateTasks = TodoTask::query()
            ->where('user_id', $user->id)
            ->where('status', '!=', 'done')
            ->whereDate('scheduled_for', '<', $today)
            ->orderBy('scheduled_for')
            ->limit(8)
            ->get([
                'id',
                'title',
                'platform',
                'series',
                'voice_tool',
                'scheduled_for',
                'publish_time',
                'status',
            ]);

        $scheduleToday = $todayTasks
            ->filter(fn ($task) => !empty($task->publish_time))
            ->values();

        $libraryStats = [
            'cs2' => ContentTopic::where('user_id', $user->id)->where('category', 'cs2')->where('status', 'available')->count(),
            'fb' => ContentTopic::where('user_id', $user->id)->where('category', 'fb')->where('status', 'available')->count(),
            'world' => ContentTopic::where('user_id', $user->id)->where('category', 'world')->where('status', 'available')->count(),
            'general' => ContentTopic::where('user_id', $user->id)->where('category', 'general')->where('status', 'available')->count(),
        ];

        $bufferStats = [
            'tiktok_ready' => TodoTask::where('user_id', $user->id)->where('status', 'pending')->where('platform', 'tiktok')->whereDate('scheduled_for', '>=', $today)->count(),
            'fb_ready' => TodoTask::where('user_id', $user->id)->where('status', 'pending')->where('platform', 'fb')->whereDate('scheduled_for', '>=', $today)->count(),
            'world_ready' => TodoTask::where('user_id', $user->id)->where('status', 'pending')->where('platform', 'world')->whereDate('scheduled_for', '>=', $today)->count(),
        ];

        $daysInMonth = $today->daysInMonth;
        $monthProgress = (int) round(($today->day / $daysInMonth) * 100);

        $activeProfiles = PublishingProfile::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get([
                'id',
                'name',
                'platform',
                'daily_target',
                'default_voice_tool',
                'is_active',
            ]);

        $platformCoverage = $activeProfiles->map(function ($profile) use ($user, $today, $daysInMonth) {
            $platform = strtolower((string) $profile->platform);

            $availableForPlatform = ContentTopic::query()
                ->where('user_id', $user->id)
                ->where('status', 'available')
                ->where(function ($query) use ($platform) {
                    $query->whereRaw('LOWER(platform) = ?', [$platform])
                        ->orWhereRaw('LOWER(category) = ?', ['general']);
                })
                ->count();

            $daysRemainingInMonth = max(0, $daysInMonth - $today->day + 1);
            $neededForPlatform = $profile->daily_target * $daysRemainingInMonth;
            $missingForPlatform = max(0, $neededForPlatform - $availableForPlatform);

            $coverageDaysForPlatform = $profile->daily_target > 0
                ? (int) floor($availableForPlatform / $profile->daily_target)
                : 0;

            return [
                'profile_id' => $profile->id,
                'name' => $profile->name,
                'platform' => $profile->platform,
                'daily_target' => $profile->daily_target,
                'available_topics' => $availableForPlatform,
                'coverage_days' => $coverageDaysForPlatform,
                'missing_topics' => $missingForPlatform,
            ];
        })->values();

        $dailyCapacity = (int) $activeProfiles->sum('daily_target');

        $availableTopics = ContentTopic::query()
            ->where('user_id', $user->id)
            ->where('status', 'available')
            ->count();

        $coverageDays = $dailyCapacity > 0
            ? (int) floor($availableTopics / $dailyCapacity)
            : 0;

        $daysRemainingInMonth = max(0, $daysInMonth - $today->day + 1);
        $neededForMonth = $dailyCapacity * $daysRemainingInMonth;
        $missingTopics = max(0, $neededForMonth - $availableTopics);

        $todayPlan = MonthlyPlanItem::query()
    ->where('user_id', $user->id)
    ->whereDate('plan_date', $today)
    ->orderBy('id')
    ->get([
        'id',
        'task_title',
        'platform',
        'series',
        'voice_tool',
        'publish_time',
        'plan_date',
        'status',
    ])
    ->map(function ($item) use ($user) {
        $task = TodoTask::query()
            ->where('user_id', $user->id)
            ->where('plan_item_id', $item->id)
            ->first(['id', 'status']);

        return [
            'id' => $item->id,
            'task_id' => $task?->id,
            'task_title' => $item->task_title,
            'platform' => $item->platform,
            'series' => $item->series,
            'voice_tool' => $item->voice_tool,
            'publish_time' => $item->publish_time,
            'plan_date' => $item->plan_date,
            'status' => $item->status,
            'task_status' => $task?->status,
        ];
    })
    ->values();

        $upcomingPlan = MonthlyPlanItem::query()
            ->where('user_id', $user->id)
            ->whereDate('plan_date', '>', $today)
            ->orderBy('plan_date')
            ->limit(10)
            ->get([
                'id',
                'task_title',
                'platform',
                'series',
                'voice_tool',
                'plan_date',
                'publish_time',
                'status',
            ]);

        return Inertia::render('Todo/Dashboard', [
            'monthProgress' => $monthProgress,
            'todayTasks' => $todayTasks,
            'scheduleToday' => $scheduleToday,
            'lateTasks' => $lateTasks,
            'libraryStats' => $libraryStats,
            'bufferStats' => $bufferStats,
            'activeProfiles' => $activeProfiles,
            'dailyCapacity' => $dailyCapacity,
            'availableTopics' => $availableTopics,
            'coverageDays' => $coverageDays,
            'missingTopics' => $missingTopics,
            'daysRemainingInMonth' => $daysRemainingInMonth,
            'todayPlan' => $todayPlan,
            'upcomingPlan' => $upcomingPlan,
            'platformCoverage' => $platformCoverage,
        ]);
    }

    private function syncTasksForToday(int $userId, string $today): void
{
    $planItems = \App\Models\MonthlyPlanItem::where('user_id', $userId)
        ->whereDate('plan_date', $today)
        ->get();

    foreach ($planItems as $item) {
        $exists = \App\Models\TodoTask::where('user_id', $userId)
            ->where('plan_item_id', $item->id)
            ->exists();

        if (!$exists) {
            \App\Models\TodoTask::create([
                'user_id' => $userId,
                'plan_item_id' => $item->id,
                'title' => $item->task_title,
                'platform' => $item->platform,
                'series' => $item->series,
                'voice_tool' => $item->voice_tool,
                'scheduled_for' => $today,
                'status' => 'pending',
            ]);
        }
    }
}

public function analytics()
{
    $user = auth()->user();

    $start = now()->startOfMonth();
    $end = now()->endOfMonth();

    $tasks = TodoTask::query()
        ->where('user_id', $user->id)
        ->whereBetween('scheduled_for', [$start, $end])
        ->get();

    $total = $tasks->count();
    $done = $tasks->where('status', 'done')->count();
    $pending = $tasks->where('status', 'pending')->count();

    $platformStats = $tasks
        ->groupBy('platform')
        ->map(fn ($g) => $g->count())
        ->sortDesc()
        ->map(fn ($count, $platform) => [
            'platform' => $platform,
            'count' => $count,
        ])
        ->values();

    $seriesStats = $tasks
        ->groupBy('series')
        ->map(fn ($g) => $g->count())
        ->sortDesc()
        ->take(10)
        ->map(fn ($count, $series) => [
            'series' => $series,
            'count' => $count,
        ])
        ->values();

    $voiceStats = $tasks
        ->groupBy('voice_tool')
        ->map(fn ($g) => $g->count())
        ->sortDesc()
        ->map(fn ($count, $voice) => [
            'voice_tool' => $voice,
            'count' => $count,
        ])
        ->values();

    return inertia('Todo/Analytics/Index', [
        'totalTasks' => $total,
        'doneTasks' => $done,
        'pendingTasks' => $pending,
        'platformStats' => $platformStats,
        'seriesStats' => $seriesStats,
        'voiceStats' => $voiceStats,
    ]);
}
}