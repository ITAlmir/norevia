<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\TodoTask;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
{
    $tasks = TodoTask::query()
        ->where('user_id', auth()->id())
        ->whereNull('cleared_at')
        ->with(['planItem.contentTopic'])
        ->orderByDesc('scheduled_for')
        ->orderByDesc('id')
        ->get()
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

    return inertia('Todo/Tasks/Index', [
        'tasks' => $tasks,
    ]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:100'],
            'series' => ['nullable', 'string', 'max:100'],
            'voice_tool' => ['nullable', 'string', 'max:100'],
            'scheduled_for' => ['nullable', 'date'],
            'publish_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string'],
        ]);

        TodoTask::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'platform' => $data['platform'] ?? null,
            'series' => $data['series'] ?? null,
            'voice_tool' => $data['voice_tool'] ?? null,
            'scheduled_for' => $data['scheduled_for'] ?? null,
            'publish_time' => $data['publish_time'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back();
    }

    public function update(Request $request, TodoTask $task)
{
    abort_if($task->user_id !== auth()->id(), 403);

    if ($task->cleared_at) {
        abort(403, 'Cleared tasks cannot be modified.');
    }

    $data = $request->validate([
        'title' => ['sometimes', 'required', 'string', 'max:255'],
        'platform' => ['nullable', 'string', 'max:100'],
        'series' => ['nullable', 'string', 'max:100'],
        'voice_tool' => ['nullable', 'string', 'max:100'],
        'scheduled_for' => ['nullable', 'date'],
        'publish_time' => ['nullable', 'date_format:H:i'],
        'notes' => ['nullable', 'string'],
        'status' => ['nullable', 'in:pending,done,late,used,skipped'],
    ]);

    $requestedStatus = $data['status'] ?? null;

    unset($data['status']);

    if (!empty($data)) {
        $task->update($data);
    }

    if ($requestedStatus) {
        // USED = published / archive
        if ($requestedStatus === 'used') {
            $task->update([
                'status' => 'done',
                'cleared_at' => now(),
            ]);

            if ($task->planItem) {
                $task->planItem->update([
                    'status' => 'done',
                ]);

                if ($task->planItem->contentTopic) {
                    $task->planItem->contentTopic->update([
                        'status' => 'used',
                    ]);
                }
            }

            return back();
        }

        // DONE = prepared
        if ($requestedStatus === 'done') {
            $task->update([
                'status' => 'done',
                'cleared_at' => null,
            ]);

            if ($task->planItem) {
                $task->planItem->update([
                    'status' => 'done',
                ]);

                if ($task->planItem->contentTopic) {
                    $task->planItem->contentTopic->update([
                        'status' => 'available',
                    ]);
                }
            }

            return back();
        }

        // SKIPPED = keep active
        if ($requestedStatus === 'skipped') {
            $task->update([
                'status' => 'late',
                'cleared_at' => null,
            ]);

            if ($task->planItem) {
                $task->planItem->update([
                    'status' => 'skipped',
                ]);

                if ($task->planItem->contentTopic) {
                    $task->planItem->contentTopic->update([
                        'status' => 'available',
                    ]);
                }
            }

            return back();
        }

        // PENDING / reset
        if ($requestedStatus === 'pending') {
            $task->update([
                'status' => 'pending',
                'cleared_at' => null,
            ]);

            if ($task->planItem) {
                $task->planItem->update([
                    'status' => 'planned',
                ]);

                if ($task->planItem->contentTopic) {
                    $task->planItem->contentTopic->update([
                        'status' => 'available',
                    ]);
                }
            }

            return back();
        }
    }

    return back();
}

    public function destroy(TodoTask $task)
    {
        abort_if($task->user_id !== auth()->id(), 403);

        $task->delete();

        return back();
    }

    public function clearActiveTasks()
{
    $userId = auth()->id();

    TodoTask::query()
        ->where('user_id', $userId)
        ->where('status', 'done')
        ->whereNull('cleared_at')
        ->update([
            'cleared_at' => now(),
        ]);

    TodoTask::query()
        ->where('user_id', $userId)
        ->whereNull('cleared_at')
        ->whereIn('status', ['pending', 'late'])
        ->where(function ($query) {
            $query->whereNull('plan_item_id')
                ->orWhereHas('planItem', function ($planItemQuery) {
                    $planItemQuery->where('status', 'planned');
                });
        })
        ->delete();

    return back();
}

    public function archive()
{
    $tasks = TodoTask::query()
        ->where('user_id', auth()->id())
        ->whereNotNull('cleared_at')
        ->with(['planItem.contentTopic'])
        ->orderByDesc('scheduled_for')
        ->orderByDesc('publish_time')
        ->orderByDesc('id')
        ->get()
        ->map(function ($task) {
            $topic = $task->planItem?->contentTopic;

            return [
                'id' => $task->id,
                'title' => $task->title,
                'platform' => $task->platform,
                'series' => $task->series,
                'voice_tool' => $task->voice_tool,
                'scheduled_for' => $task->scheduled_for,
                'publish_time' => $task->publish_time,
                'notes' => $task->notes,
                'caption' => $topic?->caption,
                'description' => $topic?->description,
                'hashtags' => $topic?->hashtags,
                'cleared_at' => $task->cleared_at,
            ];
        })
        ->values();

    $topics = \App\Models\ContentTopic::query()
        ->where('user_id', auth()->id())
        ->whereIn('status', ['used', 'archived'])
        ->latest('updated_at')
        ->latest('id')
        ->get([
            'id',
            'category',
            'platform',
            'series',
            'topic_code',
            'title',
            'voice_tool',
            'caption',
            'description',
            'hashtags',
            'script_notes',
            'status',
            'created_at',
            'updated_at',
        ]);

    return inertia('Todo/Archive/Index', [
        'tasks' => $tasks,
        'topics' => $topics,
    ]);
}
}