<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\ContentTopic;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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

        $items = MonthlyPlanItem::where('monthly_plan_id', $plan->id)
            ->orderBy('plan_date')
            ->get();

        return Inertia::render('Todo/MonthlyPlan/Index', [
            'plan' => $plan,
            'items' => $items,
        ]);
    }

    public function generate()
{
    $user = auth()->user();

    $month = now()->month;
    $year = now()->year;

    $plan = MonthlyPlan::firstOrCreate([
        'user_id' => $user->id,
        'plan_month' => $month,
        'plan_year' => $year,
    ]);

    MonthlyPlanItem::where('monthly_plan_id', $plan->id)->delete();

    $profiles = \App\Models\PublishingProfile::where('user_id', $user->id)
        ->where('is_active', true)
        ->get();

    if ($profiles->isEmpty()) {
        return redirect()->route('todo.monthly-plan.index');
    }

    $allTopics = \App\Models\ContentTopic::where('user_id', $user->id)
        ->where('status', 'available')
        ->get();

    if ($allTopics->isEmpty()) {
        return redirect()->route('todo.monthly-plan.index');
    }

    $usedTopicIds = [];
    $startDate = now();

    for ($day = 0; $day < 90; $day++) {
        $date = $startDate->copy()->addDays($day);

        foreach ($profiles as $profile) {
            for ($i = 0; $i < $profile->daily_target; $i++) {
                $topic = $allTopics
                    ->first(function ($item) use ($profile, $usedTopicIds) {
                        if (in_array($item->id, $usedTopicIds, true)) {
                            return false;
                        }

                        return strtolower((string) $item->platform) === strtolower((string) $profile->platform);
                    });

                if (!$topic) {
                    $topic = $allTopics
                        ->first(function ($item) use ($usedTopicIds) {
                            if (in_array($item->id, $usedTopicIds, true)) {
                                return false;
                            }

                            return strtolower((string) $item->category) === 'general';
                        });
                }

                if (!$topic) {
                    break 3;
                }

                MonthlyPlanItem::create([
                    'monthly_plan_id' => $plan->id,
                    'user_id' => $user->id,
                    'content_topic_id' => $topic->id,
                    'plan_date' => $date,
                    'task_title' => $topic->title,
                    'platform' => $profile->platform,
                    'series' => $topic->series,
                    'voice_tool' => $profile->default_voice_tool ?? $topic->voice_tool,
                    'publish_time' => $profile->default_publish_time,
                    'status' => 'planned',
                ]);

                $usedTopicIds[] = $topic->id;
            }
        }
    }

    return redirect()->route('todo.monthly-plan.index');
}


public function syncTodayTasks()
{
    $user = auth()->user();
    $today = now()->toDateString();

    $this->syncTasksForDate($user->id, $today);

    return redirect()->route('todo.dashboard');
}

private function syncTasksForDate(int $userId, string $date): void
{
    $planItems = \App\Models\MonthlyPlanItem::where('user_id', $userId)
        ->whereDate('plan_date', $date)
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
                'scheduled_for' => $date,
                'publish_time' => $item->publish_time,
                'status' => 'pending',
            ]);
        }
    }
}

}