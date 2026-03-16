<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\PublishingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PublishingProfileController extends Controller
{
    public function index()
    {
        $profiles = PublishingProfile::query()
            ->where('user_id', auth()->id())
            ->latest('id')
            ->get([
                'id',
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
                'created_at',
            ]);

        $summary = [
            'total_profiles' => $profiles->count(),
            'active_profiles' => $profiles->where('is_active', true)->count(),
            'daily_capacity' => $profiles->where('is_active', true)->sum(function ($profile) {
                $timesCount = count($profile->publish_times ?? []);
                return max((int) $profile->daily_target, $timesCount ?: 0, 1);
            }),
        ];

        return Inertia::render('Todo/Profiles/Index', [
            'profiles' => $profiles,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProfile($request);

        $contentBucket = $this->resolveContentBucketForCreate(
            auth()->id(),
            $data['name'],
            $data['platform'],
            $data['content_group'] ?? null
        );

        PublishingProfile::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'platform' => strtolower($data['platform']),
            'daily_target' => $data['daily_target'],
            'default_voice_tool' => $data['default_voice_tool'] ?? null,
            'default_publish_time' => $data['default_publish_time'] ?? null,
            'publish_times' => $data['publish_times'] ?? [],
            'schedule_type' => $data['schedule_type'] ?? 'daily',
            'schedule_days' => $data['schedule_days'] ?? [],
            'description' => $data['description'] ?? null,
            'use_shared_content' => $data['use_shared_content'] ?? false,
            'content_group' => $data['content_group'] ?? null,
            'content_bucket' => $contentBucket,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return back();
    }

    public function update(Request $request, PublishingProfile $profile)
    {
        abort_if($profile->user_id !== auth()->id(), 403);

        $data = $this->validateProfile($request, true);

        if (array_key_exists('platform', $data)) {
            $data['platform'] = strtolower($data['platform']);
        }

        $newGroup = $data['content_group'] ?? $profile->content_group;
        $newName = $data['name'] ?? $profile->name;
        $newPlatform = $data['platform'] ?? $profile->platform;

        $data['content_bucket'] = $this->resolveContentBucketForUpdate(
            $profile,
            $newName,
            $newPlatform,
            $newGroup
        );

        $profile->update($data);

        if (!empty($newGroup)) {
            PublishingProfile::query()
                ->where('user_id', auth()->id())
                ->where('content_group', $newGroup)
                ->update([
                    'content_bucket' => $data['content_bucket'],
                ]);
        }

        return back();
    }

    public function destroy(PublishingProfile $profile)
    {
        abort_if($profile->user_id !== auth()->id(), 403);

        $profile->delete();

        return back();
    }

    private function validateProfile(Request $request, bool $partial = false): array
    {
        $rules = [
            'name' => [$partial ? 'sometimes' : 'required', 'required', 'string', 'max:255'],
            'platform' => [$partial ? 'sometimes' : 'required', 'required', 'string', 'max:100'],
            'daily_target' => [$partial ? 'sometimes' : 'required', 'required', 'integer', 'min:1', 'max:31'],
            'default_voice_tool' => ['nullable', 'string', 'max:100'],
            'default_publish_time' => ['nullable', 'date_format:H:i'],
            'publish_times' => ['nullable', 'array', 'max:31'],
            'publish_times.*' => ['nullable', 'date_format:H:i'],
            'schedule_type' => ['nullable', Rule::in(['daily', 'weekly', 'monthly'])],
            'schedule_days' => ['nullable', 'array'],
            'schedule_days.*' => ['integer', 'min:1', 'max:31'],
            'description' => ['nullable', 'string'],
            'use_shared_content' => ['nullable', 'boolean'],
            'content_group' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];

        $data = $request->validate($rules);

        $data['publish_times'] = collect($data['publish_times'] ?? [])
            ->map(fn ($time) => trim((string) $time))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $data['schedule_days'] = collect($data['schedule_days'] ?? [])
            ->map(fn ($day) => (int) $day)
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $scheduleType = $data['schedule_type'] ?? ($partial ? null : 'daily');

        if ($scheduleType === 'weekly') {
            foreach (($data['schedule_days'] ?? []) as $day) {
                if ($day < 1 || $day > 7) {
                    abort(422, 'Weekly schedule days must be between 1 and 7.');
                }
            }
        }

        if ($scheduleType === 'monthly') {
            foreach (($data['schedule_days'] ?? []) as $day) {
                if ($day < 1 || $day > 31) {
                    abort(422, 'Monthly schedule days must be between 1 and 31.');
                }
            }
        }

        if (($data['daily_target'] ?? null) && count($data['publish_times']) > 0) {
            $data['daily_target'] = max((int) $data['daily_target'], count($data['publish_times']));
        }

        if (empty($data['publish_times']) && !empty($data['default_publish_time'])) {
            $data['publish_times'] = [$data['default_publish_time']];
        }

        $useSharedContent = (bool) ($data['use_shared_content'] ?? false);

        if (!$useSharedContent) {
            $data['content_group'] = null;
        } else {
            $data['content_group'] = trim((string) ($data['content_group'] ?? ''));
            if ($data['content_group'] === '') {
                $data['content_group'] = null;
            }
        }

        return $data;
    }

    private function generateContentBucket(string $profileName): string
    {
        $base = Str::slug($profileName);
        $seed = now()->format('dHis');
        $suffix = substr((string) crc32($base . $seed), -4);

        return "{$base}-{$suffix}";
    }

    private function resolveContentBucketForCreate(int $userId, string $name, string $platform, ?string $contentGroup): string
    {
        if (!empty($contentGroup)) {
            $existing = PublishingProfile::query()
                ->where('user_id', $userId)
                ->where('content_group', $contentGroup)
                ->first();

            if ($existing && !empty($existing->content_bucket)) {
                return $existing->content_bucket;
            }
        }

        return $this->generateContentBucket($name);
    }

    private function resolveContentBucketForUpdate(PublishingProfile $profile, string $name, string $platform, ?string $newGroup): string
    {
        $oldGroup = $profile->content_group;
        $currentBucket = $profile->content_bucket ?: $this->generateContentBucket($name);

        if ($oldGroup === $newGroup) {
            return $currentBucket;
        }

        if (!empty($newGroup)) {
            $existing = PublishingProfile::query()
                ->where('user_id', $profile->user_id)
                ->where('content_group', $newGroup)
                ->where('id', '!=', $profile->id)
                ->first();

            if ($existing && !empty($existing->content_bucket)) {
                return $existing->content_bucket;
            }

            return $currentBucket;
        }

        return $this->generateContentBucket($name);
    }
}