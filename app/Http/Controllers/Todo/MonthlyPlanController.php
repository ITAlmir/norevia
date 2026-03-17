<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\ContentTopic;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanItem;
use App\Models\PublishingProfile;
use App\Models\TodoTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonthlyPlanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $month = now()->month;
        $year = now()->year;

        $plan = MonthlyPlan::firstOrCreate([
            'user_id' => $user->id,
            'plan_month' => $month,
            'plan_year' => $year,
        ]);

        $items = MonthlyPlanItem::query()
            ->where('monthly_plan_id', $plan->id)
            ->orderBy('plan_date')
            ->orderBy('publish_time')
            ->get();

        return Inertia::render('Todo/MonthlyPlan/Index', [
            'plan' => $plan,
            'items' => $items,
            'defaultEndDate' => now()->addMonthNoOverflow()->endOfMonth()->toDateString(),
        ]);
    }

    public function generate(Request $request)
{
    $user = auth()->user();

    $validated = $request->validate([
        'end_date' => ['required', 'date', 'after_or_equal:today'],
    ]);

    $startDate = now()->startOfDay();
    $endDate = Carbon::parse($validated['end_date'])->endOfDay();

    $month = now()->month;
    $year = now()->year;

    $plan = MonthlyPlan::firstOrCreate([
        'user_id' => $user->id,
        'plan_month' => $month,
        'plan_year' => $year,
    ]);

    $futurePlanItemIds = MonthlyPlanItem::query()
        ->where('monthly_plan_id', $plan->id)
        ->whereDate('plan_date', '>=', $startDate->toDateString())
        ->pluck('id');

    if ($futurePlanItemIds->isNotEmpty()) {
        TodoTask::query()
            ->where('user_id', $user->id)
            ->whereIn('plan_item_id', $futurePlanItemIds)
            ->whereNull('cleared_at')
            ->delete();
    }

    MonthlyPlanItem::query()
        ->where('monthly_plan_id', $plan->id)
        ->whereDate('plan_date', '>=', $startDate->toDateString())
        ->delete();

    $profiles = PublishingProfile::query()
        ->where('user_id', $user->id)
        ->where('is_active', true)
        ->get();

    if ($profiles->isEmpty()) {
        return redirect()->route('todo.monthly-plan.index');
    }

    $profileGroups = $this->buildProfileGroups($profiles);

    $allTopics = ContentTopic::query()
        ->where('user_id', $user->id)
        ->where('status', 'available')
        ->orderBy('id')
        ->get();

    if ($allTopics->isEmpty()) {
        return redirect()->route('todo.monthly-plan.index');
    }

    $usedTopicIds = [];
    $cursor = $startDate->copy();

    while ($cursor->lte($endDate)) {
        foreach ($profileGroups as $group) {
            $activeProfilesForDate = $group['profiles']
                ->filter(fn ($profile) => $profile->matchesDate($cursor))
                ->values();

            if ($activeProfilesForDate->isEmpty()) {
                continue;
            }

            $groupBucket = $this->resolveGroupBucket($activeProfilesForDate);

            if (empty($groupBucket)) {
                continue;
            }

            $slotLimit = $activeProfilesForDate->max(function ($profile) {
                return $this->profileSlotCount($profile);
            });

            for ($slotIndex = 0; $slotIndex < $slotLimit; $slotIndex++) {
                $topic = $this->pickNextTopicForBucket(
                    $allTopics,
                    $usedTopicIds,
                    $groupBucket
                );

                if (!$topic) {
                    break;
                }

                foreach ($activeProfilesForDate as $profile) {
                    $profileSlotCount = $this->profileSlotCount($profile);

                    if ($slotIndex >= $profileSlotCount) {
                        continue;
                    }

                    $profileTimes = $profile->resolvedPublishTimes();
                    $publishTime = $profileTimes[$slotIndex]
                        ?? $profileTimes[0]
                        ?? $profile->default_publish_time
                        ?? null;

                    MonthlyPlanItem::create([
                        'monthly_plan_id' => $plan->id,
                        'user_id' => $user->id,
                        'content_topic_id' => $topic->id,
                        'plan_date' => $cursor->toDateString(),
                        'task_title' => $topic->title,
                        'profile_name' => $profile->name,
                        'content_bucket' => $topic->content_bucket,
                        'shared_content_group' => $profile->content_group ?: $topic->shared_content_group,
                        'platform' => $profile->platform,
                        'series' => $topic->series,
                        'voice_tool' => $profile->default_voice_tool ?: $topic->voice_tool,
                        'publish_time' => $publishTime,
                        'status' => 'planned',
                    ]);
                }

                $usedTopicIds[] = $topic->id;
            }
        }

        $cursor->addDay();
}

$cursor = $startDate->copy();

while ($cursor->lte($endDate)) {
    $this->syncTasksForDate($user->id, $cursor->toDateString());
    $cursor->addDay();
}

return redirect()->route('todo.monthly-plan.index');
}
    

    public function toggleItemStatus(MonthlyPlanItem $item, Request $request)
{
    abort_if($item->user_id !== auth()->id(), 403);

    $data = $request->validate([
        'status' => ['required', 'in:planned,done,used,skipped'],
    ]);

    $requestedStatus = $data['status'];

    $task = TodoTask::query()
        ->where('user_id', auth()->id())
        ->where('plan_item_id', $item->id)
        ->first();

    // USED = published / archived
    if ($requestedStatus === 'used') {
        $item->update([
            'status' => 'done',
        ]);

        if ($item->contentTopic) {
            $item->contentTopic->update([
                'status' => 'used',
            ]);
        }

        if ($task) {
            $task->update([
                'status' => 'done',
                'cleared_at' => now(),
            ]);
        }

        return back();
    }

    // DONE = prepared, still active
    if ($requestedStatus === 'done') {
        $item->update([
            'status' => 'done',
        ]);

        if ($item->contentTopic) {
            $item->contentTopic->update([
                'status' => 'available',
            ]);
        }

        if ($task && !$task->cleared_at) {
            $task->update([
                'status' => 'done',
            ]);
        }

        return back();
    }

    // SKIPPED = stays active, yellow state
    if ($requestedStatus === 'skipped') {
        $item->update([
            'status' => 'skipped',
        ]);

        if ($item->contentTopic) {
            $item->contentTopic->update([
                'status' => 'available',
            ]);
        }

        if ($task && !$task->cleared_at) {
            $task->update([
                'status' => 'late',
            ]);
        }

        return back();
    }

    // PLANNED = reset back to active planned
    $item->update([
        'status' => 'planned',
    ]);

    if ($item->contentTopic) {
        $item->contentTopic->update([
            'status' => 'available',
        ]);
    }

    if ($task) {
        $task->update([
            'status' => 'pending',
            'cleared_at' => null,
        ]);
    }

    return back();
}

    public function syncTodayTasks()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $this->syncTasksForDate($user->id, $today);

        return redirect()->route('todo.dashboard');
    }

    private function buildProfileGroups($profiles): array
{
    $groups = [];
    $handledShared = [];

    foreach ($profiles as $profile) {
        if ($profile->use_shared_content && $profile->content_group) {
            $groupKey = strtolower(trim((string) $profile->content_group));

            if (in_array($groupKey, $handledShared, true)) {
                continue;
            }

            $sharedProfiles = $profiles
                ->filter(function ($item) use ($groupKey) {
                    return $item->use_shared_content
                        && strtolower(trim((string) $item->content_group)) === $groupKey;
                })
                ->values();

            $handledShared[] = $groupKey;

            $groups[] = [
                'key' => 'shared:' . $groupKey,
                'shared' => true,
                'profiles' => $sharedProfiles,
            ];

            continue;
        }

        $groups[] = [
            'key' => 'single:' . $profile->id,
            'shared' => false,
            'profiles' => collect([$profile]),
        ];
    }

    return $groups;
}

private function profileSlotCount(PublishingProfile $profile): int
{
    $times = $profile->resolvedPublishTimes();

    return max(
        (int) $profile->daily_target,
        count($times),
        1
    );
}

private function resolveGroupBucket($profiles): ?string
{
    $bucket = $profiles
        ->pluck('content_bucket')
        ->filter(fn ($value) => !empty(trim((string) $value)))
        ->map(fn ($value) => trim((string) $value))
        ->first();

    return $bucket ?: null;
}

private function pickNextTopicForBucket($allTopics, array $usedTopicIds, string $bucket): ?ContentTopic
{
    return $allTopics->first(function ($item) use ($usedTopicIds, $bucket) {
        if (in_array($item->id, $usedTopicIds, true)) {
            return false;
        }

        if (strtolower((string) $item->status) !== 'available') {
            return false;
        }

        return !empty($item->content_bucket)
            && strtolower(trim((string) $item->content_bucket)) === strtolower(trim((string) $bucket));
    });
}    

    private function syncTasksForDate(int $userId, string $date): void
    {
        $planItems = MonthlyPlanItem::query()
            ->where('user_id', $userId)
            ->whereDate('plan_date', $date)
            ->orderBy('publish_time')
            ->get();

        foreach ($planItems as $item) {
    if ($item->status === 'used') {
        continue;
    }

    $exists = TodoTask::query()
                ->where('user_id', $userId)
                ->where('plan_item_id', $item->id)
                ->exists();

            if (!$exists) {
    TodoTask::create([
        'user_id' => $userId,
        'plan_item_id' => $item->id,
        'title' => $item->task_title,
        'profile_name' => $item->profile_name,
        'content_bucket' => $item->content_bucket,
        'shared_content_group' => $item->shared_content_group,
        'platform' => $item->platform,
        'series' => $item->series,
        'voice_tool' => $item->voice_tool,
        'scheduled_for' => $date,
        'publish_time' => $item->publish_time,
        'status' => $item->status === 'done' ? 'done' : 'pending',
    ]);
            }
        }
    }
}