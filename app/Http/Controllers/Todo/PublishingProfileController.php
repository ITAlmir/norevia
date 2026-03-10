<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\PublishingProfile;
use Illuminate\Http\Request;
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
                'is_active',
                'created_at',
                'default_publish_time',
            ]);

        $summary = [
            'total_profiles' => $profiles->count(),
            'active_profiles' => $profiles->where('is_active', true)->count(),
            'daily_capacity' => $profiles->where('is_active', true)->sum('daily_target'),
        ];

        return Inertia::render('Todo/Profiles/Index', [
            'profiles' => $profiles,
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'string', 'max:100'],
            'daily_target' => ['required', 'integer', 'min:1', 'max:10'],
            'default_voice_tool' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'default_publish_time' => ['nullable', 'date_format:H:i'],
        ]);

        PublishingProfile::create([
    'user_id' => auth()->id(),
    'name' => $data['name'],
    'platform' => strtolower($data['platform']),
    'daily_target' => $data['daily_target'],
    'default_voice_tool' => $data['default_voice_tool'] ?? null,
    'default_publish_time' => $data['default_publish_time'] ?? null,
    'is_active' => $data['is_active'] ?? true,
]);

        return redirect()->route('todo.profiles.index');
    }

    public function update(Request $request, PublishingProfile $profile)
{
    abort_if($profile->user_id !== auth()->id(), 403);

    $data = $request->validate([
        'name' => ['sometimes', 'required', 'string', 'max:255'],
        'platform' => ['sometimes', 'required', 'string', 'max:100'],
        'daily_target' => ['sometimes', 'required', 'integer', 'min:1', 'max:10'],
        'default_voice_tool' => ['nullable', 'string', 'max:100'],
        'default_publish_time' => ['nullable', 'date_format:H:i'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    if (isset($data['platform'])) {
        $data['platform'] = strtolower($data['platform']);
    }

    $profile->update($data);

    return redirect()->route('todo.profiles.index');
}

    public function destroy(PublishingProfile $profile)
    {
        abort_if($profile->user_id !== auth()->id(), 403);

        $profile->delete();

        return redirect()->route('todo.profiles.index');
    }
}