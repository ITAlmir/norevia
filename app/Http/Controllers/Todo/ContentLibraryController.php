<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\ContentTopic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentLibraryController extends Controller
{
    public function index()
{
    $user = auth()->user();

    $topics = ContentTopic::query()
    ->where('user_id', $user->id)
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
    ]);

    return Inertia::render('Todo/Library/Index', [
        'topics' => $topics,
    ]);
}

    public function store(Request $request)
{
    $request->validate([
        'bulk_input' => ['required', 'string'],
    ]);

    $lines = preg_split('/\r\n|\r|\n/', trim($request->bulk_input));
    $rowsToInsert = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '') {
            continue;
        }

        $parts = array_map('trim', explode('|', $line));

        $platform = !empty($parts[0]) ? strtolower(trim($parts[0])) : null;
        $series = !empty($parts[1]) ? strtolower(trim($parts[1])) : null;
        $voiceTool = $parts[2] ?? null;
        $title = $parts[3] ?? null;

        $caption = $parts[4] ?? null;
        $description = $parts[5] ?? null;
        $hashtags = $parts[6] ?? null;
        $scriptNotes = $parts[7] ?? null;

        if (!$title) {
            continue;
        }

        $category = $this->detectCategory($platform, $series, $title);

        $topicCode = $platform && $series
            ? "{$platform}#{$series}"
            : ($series ?: $title);

        $rowsToInsert[] = [
            'user_id' => auth()->id(),
            'category' => $category,
            'platform' => $platform,
            'series' => $series,
            'topic_code' => $topicCode,
            'title' => $title,
            'voice_tool' => $voiceTool,
            'status' => 'available',
            'caption' => $caption,
            'description' => $description,
            'hashtags' => $hashtags,
            'script_notes' => $scriptNotes,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    if (!empty($rowsToInsert)) {
        ContentTopic::insert($rowsToInsert);
    }

    return redirect()->route('todo.library.index');
}

    public function update(Request $request, ContentTopic $topic)
{
    abort_if($topic->user_id !== auth()->id(), 403);

    $data = $request->validate([
        'title' => ['sometimes', 'required', 'string', 'max:255'],
        'platform' => ['nullable', 'string', 'max:100'],
        'series' => ['nullable', 'string', 'max:100'],
        'voice_tool' => ['nullable', 'string', 'max:100'],
        'caption' => ['nullable', 'string'],
        'description' => ['nullable', 'string'],
        'hashtags' => ['nullable', 'string'],
        'script_notes' => ['nullable', 'string'],
        'status' => ['nullable', 'in:available,used,archived'],
    ]);

    if (isset($data['platform']) && $data['platform'] !== null) {
        $data['platform'] = strtolower($data['platform']);
    }

    $topic->update($data);

    return redirect()->route('todo.library.index');
}

    public function destroy(ContentTopic $topic)
    {
        abort_if($topic->user_id !== auth()->id(), 403);

        $topic->delete();

        return redirect()->route('todo.library.index');
    }

    private function detectCategory(?string $platform, ?string $series, ?string $title): string
    {
        $haystack = strtolower(trim(($platform ?? '') . ' ' . ($series ?? '') . ' ' . ($title ?? '')));

        if (str_contains($haystack, 'cs2')) {
            return 'cs2';
        }

        if (
            str_contains($haystack, 'svijet40') ||
            str_contains($haystack, 'fb') ||
            str_contains($haystack, 'facebook')
        ) {
            return 'fb';
        }

        if (
            str_contains($haystack, 'world') ||
            str_contains($haystack, 'fact') ||
            str_contains($haystack, 'place')
        ) {
            return 'world';
        }

        return 'general';
    }
}