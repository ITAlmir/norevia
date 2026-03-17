<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\ContentTopic;
use App\Models\MonthlyPlanItem;
use App\Models\PublishingProfile;
use App\Models\TodoTask;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->startOfDay();
        $startOfMonth = $today->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $today->copy()->endOfMonth()->startOfDay();
        $daysRemainingInMonth = (int) $today->diffInDays($endOfMonth) + 1;

        $activeProfiles = PublishingProfile::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $dailyCapacity = $activeProfiles->sum(function ($profile) {
            return match ($profile->schedule_type) {
                'monthly' => 0,
                'weekly' => 0,
                default => (int) $profile->daily_target,
            };
        });

        $todayPlan = MonthlyPlanItem::query()
            ->with('contentTopic')
            ->whereDate('plan_date', $today->toDateString())
            ->orderBy('publish_time')
            ->orderBy('id')
            ->get()
            ->map(fn ($item) => $this->mapPlanItem($item));

        // koristimo isti prop "upcomingPlan" i za prošle i za buduće dane u okviru tekućeg mjeseca
        $upcomingPlan = MonthlyPlanItem::query()
            ->with('contentTopic')
            ->whereDate('plan_date', '>=', $startOfMonth->toDateString())
            ->whereDate('plan_date', '<=', $endOfMonth->toDateString())
            ->orderBy('plan_date')
            ->orderBy('publish_time')
            ->orderBy('id')
            ->get()
            ->map(fn ($item) => $this->mapPlanItem($item));

        $lateTasks = TodoTask::query()
            ->where('user_id', auth()->id())
            ->whereNull('cleared_at')
            ->whereDate('scheduled_for', '<', $today->toDateString())
            ->with(['planItem.contentTopic'])
            ->orderBy('scheduled_for')
            ->orderBy('publish_time')
            ->orderBy('id')
            ->get()
            ->filter(function ($task) {
                if ($task->status === 'done') {
                    return false;
                }

                if ($task->planItem && $task->planItem->status === 'done') {
                    return false;
                }

                return true;
            })
            ->map(function ($task) {
                $topic = $task->planItem?->contentTopic;

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'profile_name' => $task->profile_name,
                    'content_bucket' => $task->content_bucket,
                    'shared_content_group' => $task->shared_content_group,
                    'platform' => $task->platform,
                    'series' => $task->series,
                    'voice_tool' => $task->voice_tool,
                    'scheduled_for' => $task->scheduled_for,
                    'publish_time' => $task->publish_time,
                    'status' => $task->status,
                    'plan_item_status' => $task->planItem?->status,
                    'notes' => $task->notes,
                    'caption' => $topic?->caption,
                    'description' => $topic?->description,
                    'hashtags' => $topic?->hashtags,
                    'cleared_at' => $task->cleared_at,
                ];
            })
            ->values();

        $plannedRemaining = MonthlyPlanItem::query()
            ->whereDate('plan_date', '>=', $today->toDateString())
            ->whereDate('plan_date', '<=', $endOfMonth->toDateString())
            ->whereIn('status', ['planned', 'skipped'])
            ->count();

        $requiredRemaining = (int) $activeProfiles->sum(function ($profile) use ($daysRemainingInMonth) {
            return match ($profile->schedule_type) {
                'monthly' => (int) $profile->daily_target,
                'weekly' => (int) ceil(($daysRemainingInMonth / 7) * (int) $profile->daily_target),
                default => (int) $daysRemainingInMonth * (int) $profile->daily_target,
            };
        });

        $missingTopics = max($requiredRemaining - $plannedRemaining, 0);
        $coverageDays = $dailyCapacity > 0 ? (int) floor($plannedRemaining / $dailyCapacity) : 0;
        $monthProgress = (int) round(($today->day / $today->daysInMonth) * 100);

        $bucketCoverage = $this->buildBucketCoverage($activeProfiles, $today, $endOfMonth);

        $libraryOverview = [
            'available' => ContentTopic::query()->where('status', 'available')->count(),
            'planned' => MonthlyPlanItem::query()
                ->whereDate('plan_date', '>=', $startOfMonth->toDateString())
                ->whereDate('plan_date', '<=', $endOfMonth->toDateString())
                ->whereIn('status', ['planned', 'skipped'])
                ->count(),
            'completed' => MonthlyPlanItem::query()
                ->whereDate('plan_date', '>=', $startOfMonth->toDateString())
                ->whereDate('plan_date', '<=', $endOfMonth->toDateString())
                ->where('status', 'done')
                ->count(),
            'archived' => ContentTopic::query()->where('status', 'archived')->count(),
        ];

        return Inertia::render('Todo/Dashboard', [
            'monthProgress' => $monthProgress,
            'todayPlan' => $todayPlan,
            'upcomingPlan' => $upcomingPlan,
            'lateTasks' => $lateTasks,

            'activeProfiles' => $activeProfiles->map(function ($profile) {
                return [
                    'id' => $profile->id,
                    'name' => $profile->name,
                    'profile_name' => $profile->name,
                    'platform' => $profile->platform,
                    'schedule_type' => $profile->schedule_type,
                    'daily_target' => (int) $profile->daily_target,
                    'default_voice_tool' => $profile->default_voice_tool,
                    'default_publish_time' => $profile->default_publish_time,
                    'publish_times' => $this->normalizePublishTimes($profile->publish_times ?? null),
                ];
            })->values(),

            'dailyCapacity' => $dailyCapacity,
            'plannedRemaining' => $plannedRemaining,
            'coverageDays' => $coverageDays,
            'missingTopics' => $missingTopics,
            'daysRemainingInMonth' => $daysRemainingInMonth,
            'bucketCoverage' => $bucketCoverage,
            'libraryOverview' => $libraryOverview,
        ]);
    }

    protected function buildBucketCoverage(Collection $profiles, Carbon $today, Carbon $endOfMonth): array
    {
        $daysRemainingInMonth = (int) $today->copy()->startOfDay()->diffInDays($endOfMonth->copy()->startOfDay()) + 1;

        $hasProfileBucket = Schema::hasColumn('publishing_profiles', 'content_bucket');
        $hasPlanBucket = Schema::hasColumn('monthly_plan_items', 'content_bucket');
        $hasTopicBucket = Schema::hasColumn('content_topics', 'content_bucket');

        if (! $hasProfileBucket) {
            return [];
        }

        $grouped = $profiles
            ->filter(fn ($profile) => filled($profile->content_bucket))
            ->groupBy('content_bucket');

        return $grouped->map(function (Collection $group, string $bucket) use ($today, $endOfMonth, $daysRemainingInMonth, $hasPlanBucket, $hasTopicBucket) {
            $representative = $group->first();

            $required = (int) $group->sum(function ($profile) use ($daysRemainingInMonth) {
                return match ($profile->schedule_type) {
                    'monthly' => (int) $profile->daily_target,
                    'weekly' => (int) ceil(($daysRemainingInMonth / 7) * (int) $profile->daily_target),
                    default => (int) $daysRemainingInMonth * (int) $profile->daily_target,
                };
            });

            $planned = 0;
            if ($hasPlanBucket) {
                $planned = MonthlyPlanItem::query()
                    ->where('content_bucket', $bucket)
                    ->whereDate('plan_date', '>=', $today->toDateString())
                    ->whereDate('plan_date', '<=', $endOfMonth->toDateString())
                    ->whereIn('status', ['planned', 'skipped'])
                    ->count();
            }

            $available = 0;
            if ($hasTopicBucket) {
                $available = ContentTopic::query()
                    ->where('content_bucket', $bucket)
                    ->where('status', 'available')
                    ->count();
            }

            $planned = (int) $planned;
            $available = (int) $available;
            $missing = (int) max($required - $planned, 0);

            return [
                'content_bucket' => $bucket,
                'label' => $representative->name,
                'required' => $required,
                'planned' => $planned,
                'available' => $available,
                'missing' => $missing,
            ];
        })->values()->all();
    }

    protected function mapPlanItem(MonthlyPlanItem $item): array
    {
        $topic = $item->contentTopic;

        return [
            'id' => $item->id,
            'task_title' => $item->task_title ?? $topic?->title ?? 'Untitled item',
            'profile_name' => $item->profile_name ?? null,
            'platform' => $item->platform ?? $topic?->platform,
            'series' => $item->series ?? $topic?->series,
            'voice_tool' => $item->voice_tool ?? $topic?->voice_tool,
            'publish_time' => $item->publish_time ?? null,
            'plan_date' => $item->plan_date,
            'status' => $item->status,
            'task_status' => $item->status,
            'caption' => $topic?->caption,
            'description' => $topic?->description,
            'hashtags' => $topic?->hashtags,
            'cleared_at' => null,
            'content_bucket' => Schema::hasColumn('monthly_plan_items', 'content_bucket')
                ? $item->content_bucket
                : null,
        ];
    }

    protected function normalizePublishTimes($publishTimes): array
    {
        if (is_array($publishTimes)) {
            return array_values(array_filter($publishTimes));
        }

        if (blank($publishTimes)) {
            return [];
        }

        if (is_string($publishTimes)) {
            $decoded = json_decode($publishTimes, true);

            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }
        }

        return [];
    }

    public function analytics()
    {
        $totalTasks = MonthlyPlanItem::count();

        $doneTasks = MonthlyPlanItem::where('status', 'done')->count();
        $plannedTasks = MonthlyPlanItem::where('status', 'planned')->count();
        $skippedTasks = MonthlyPlanItem::where('status', 'skipped')->count();

        $platformStats = MonthlyPlanItem::query()
            ->selectRaw('platform, COUNT(*) as total')
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->platform ?: 'Unknown',
                'total' => (int) $row->total,
            ])
            ->values();

        $seriesStats = MonthlyPlanItem::query()
            ->selectRaw('series, COUNT(*) as total')
            ->whereNotNull('series')
            ->groupBy('series')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->series ?: 'Unknown',
                'total' => (int) $row->total,
            ])
            ->values();

        $voiceToolStats = MonthlyPlanItem::query()
            ->selectRaw('voice_tool, COUNT(*) as total')
            ->whereNotNull('voice_tool')
            ->groupBy('voice_tool')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->voice_tool ?: 'Unknown',
                'total' => (int) $row->total,
            ])
            ->values();

        return Inertia::render('Todo/Analytics/Index', [
            'stats' => [
                'totalTasks' => $totalTasks,
                'doneTasks' => $doneTasks,
                'plannedTasks' => $plannedTasks,
                'skippedTasks' => $skippedTasks,
            ],
            'platformStats' => $platformStats,
            'seriesStats' => $seriesStats,
            'voiceToolStats' => $voiceToolStats,
        ]);
    }
}