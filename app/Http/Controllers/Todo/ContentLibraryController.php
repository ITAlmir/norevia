<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\ContentTopic;
use App\Models\MonthlyPlanItem;
use App\Models\PublishingProfile;
use App\Models\TodoTask;
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
                'content_bucket',
                'shared_content_group',
                'caption',
                'description',
                'hashtags',
                'script_notes',
                'status',
                'created_at',
            ]);

        $profiles = PublishingProfile::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
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
            ]);

        return Inertia::render('Todo/Library/Index', [
            'topics' => $topics,
            'profiles' => $profiles,
        ]);
    }

    public function previewImport(Request $request)
{
    $request->validate([
        'bulk_input' => ['required', 'string'],
    ]);

    $preview = $this->parseBulkInput($request->bulk_input, auth()->id());

    return response()->json($preview);
}

public function store(Request $request)
{
    $request->validate([
        'bulk_input' => ['required', 'string'],
        'allowed_warning_titles' => ['nullable', 'array'],
        'allowed_warning_titles.*' => ['string'],
    ]);

    $allowedWarningTitles = collect($request->input('allowed_warning_titles', []))
        ->map(fn ($title) => trim((string) $title))
        ->filter()
        ->values()
        ->all();

    $preview = $this->parseBulkInput($request->bulk_input, auth()->id());

    if (!empty($preview['invalid_rows'])) {
        return back()
            ->withErrors([
                'bulk_input' => 'Some topics need a quick fix before import. Review the highlighted lines below.',
            ])
            ->with([
                'import_preview' => $preview,
                'bulk_input' => $request->bulk_input,
            ]);
    }

    $rowsToInsert = collect($preview['ready_rows'])
        ->filter(function ($row) use ($allowedWarningTitles) {
            if (!($row['historical_warning'] ?? false)) {
                return true;
            }

            return in_array($row['title'], $allowedWarningTitles, true);
        })
        ->map(function ($row) {
            unset($row['historical_warning'], $row['previously_used_at']);
            return $row;
        })
        ->values()
        ->all();

    if (!empty($rowsToInsert)) {
        ContentTopic::insert($rowsToInsert);
    }

    return back()->with([
        'import_preview' => $preview,
    ]);
}

    public function update(Request $request, ContentTopic $topic)
    {
        abort_if($topic->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:100'],
            'series' => ['nullable', 'string', 'max:100'],
            'voice_tool' => ['nullable', 'string', 'max:100'],
            'content_bucket' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'hashtags' => ['nullable', 'string'],
            'script_notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:available,used,archived'],
        ]);

        if (array_key_exists('platform', $data) && $data['platform']) {
            $data['platform'] = strtolower(trim($data['platform']));
        }

        if (array_key_exists('series', $data) && $data['series']) {
            $data['series'] = strtolower(trim($data['series']));
        }

        $platform = $data['platform'] ?? $topic->platform;
        $series = $data['series'] ?? $topic->series;
        $title = $data['title'] ?? $topic->title;
        $bucket = $data['content_bucket'] ?? $topic->content_bucket;

        $data['category'] = $this->detectCategory($platform, $series, $title, $bucket);
        $data['topic_code'] = $platform && $series
            ? "{$platform}#{$series}"
            : ($series ?: $title);

        if (!empty($bucket)) {
            $data['shared_content_group'] = PublishingProfile::query()
                ->where('user_id', auth()->id())
                ->where('content_bucket', $bucket)
                ->whereNotNull('content_group')
                ->value('content_group');
        }

        $topic->update($data);

        return back();
    }

    public function destroy(ContentTopic $topic)
    {
        abort_if($topic->user_id !== auth()->id(), 403);

        $topic->delete();

        return back();
    }

    public function clearAvailable()
    {
        ContentTopic::query()
            ->where('user_id', auth()->id())
            ->where('status', 'available')
            ->delete();

        return back();
    }

    public function resetActiveWorkflow()
    {
        $userId = auth()->id();

        TodoTask::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'late'])
            ->delete();

        MonthlyPlanItem::query()
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'done');
            })
            ->delete();

        ContentTopic::query()
            ->where('user_id', $userId)
            ->where('status', 'available')
            ->delete();

        return back();
    }

    private function parseBulkInput(string $bulkInput, int $userId): array
{
    $lines = preg_split('/\r\n|\r|\n/', trim($bulkInput));

    $readyRows = [];
    $skippedDuplicates = [];
    $historicalWarnings = [];
    $invalidRows = [];
    $batchKeys = [];

    foreach ($lines as $index => $line) {
        $lineNumber = $index + 1;
        $line = trim($line);
        $line = preg_replace('/\s+/', ' ', $line);

        if ($line === '') {
            continue;
        }

        $parsed = $this->parseBulkLine($line);

        if (!($parsed['valid'] ?? false)) {
            $invalidRows[] = [
                'line_number' => $lineNumber,
                'line' => $line,
                'reason' => $parsed['error'] ?? 'Invalid format.',
            ];
            continue;
        }

        if (!$this->userHasActiveBucket($userId, $parsed['content_bucket'] ?? null)) {
            $invalidRows[] = [
                'line_number' => $lineNumber,
                'line' => $line,
                'reason' => 'This content bucket does not match any active publishing profile for your account.',
            ];
            continue;
        }

        if (!($parsed['valid'] ?? false)) {
            $invalidRows[] = [
                'line_number' => $lineNumber,
                'line' => $line,
                'reason' => $parsed['error'] ?? 'Invalid format.',
            ];
            continue;
        }

        $batchKey = strtolower(
            trim(
                ($parsed['content_bucket'] ?: '__no_bucket__') . '|' .
                ($parsed['platform'] ?? '') . '|' .
                ($parsed['title'] ?? '')
            )
        );

        if (in_array($batchKey, $batchKeys, true)) {
            $skippedDuplicates[] = [
                'title' => $parsed['title'],
                'platform' => $parsed['platform'],
                'content_bucket' => $parsed['content_bucket'],
            ];
            continue;
        }

        $batchKeys[] = $batchKey;

        $normalizedBucket = strtolower((string) $parsed['content_bucket']);
        $normalizedPlatform = strtolower((string) $parsed['platform']);
        $normalizedTitle = strtolower((string) $parsed['title']);

        $existingTopicQuery = ContentTopic::query()
            ->where('user_id', $userId)
            ->whereRaw('LOWER(platform) = ?', [$normalizedPlatform])
            ->whereRaw('LOWER(title) = ?', [$normalizedTitle]);

        $existingPlanItemQuery = MonthlyPlanItem::query()
            ->where('user_id', $userId)
            ->whereRaw('LOWER(platform) = ?', [$normalizedPlatform])
            ->whereRaw('LOWER(task_title) = ?', [$normalizedTitle]);

        $existingTaskQuery = TodoTask::query()
            ->where('user_id', $userId)
            ->whereRaw('LOWER(platform) = ?', [$normalizedPlatform])
            ->whereRaw('LOWER(title) = ?', [$normalizedTitle]);

        if (!empty($normalizedBucket)) {
            $existingTopicQuery->whereRaw('LOWER(content_bucket) = ?', [$normalizedBucket]);
            $existingPlanItemQuery->whereRaw('LOWER(content_bucket) = ?', [$normalizedBucket]);
            $existingTaskQuery->whereRaw('LOWER(content_bucket) = ?', [$normalizedBucket]);
        }

        $existingTopic = $existingTopicQuery
            ->orderByDesc('created_at')
            ->first([
                'id',
                'title',
                'created_at',
                'status',
            ]);

        $existingPlanItem = $existingPlanItemQuery
            ->orderByDesc('created_at')
            ->first([
                'id',
                'task_title',
                'created_at',
                'status',
            ]);

        $existingTask = $existingTaskQuery
            ->orderByDesc('created_at')
            ->first([
                'id',
                'title',
                'created_at',
                'status',
            ]);

        $existing = $existingTopic ?: ($existingPlanItem ?: $existingTask);

        $row = [
            'user_id' => $userId,
            'category' => $this->detectCategory(
                $parsed['platform'],
                $parsed['series'],
                $parsed['title'],
                $parsed['content_bucket']
            ),
            'platform' => $parsed['platform'],
            'series' => $parsed['series'],
            'topic_code' => $parsed['platform'] && $parsed['series']
                ? "{$parsed['platform']}#{$parsed['series']}"
                : ($parsed['series'] ?: $parsed['title']),
            'title' => $parsed['title'],
            'voice_tool' => $parsed['voice_tool'],
            'content_bucket' => $parsed['content_bucket'],
            'shared_content_group' => PublishingProfile::query()
                ->where('user_id', $userId)
                ->where('content_bucket', $parsed['content_bucket'])
                ->whereNotNull('content_group')
                ->value('content_group'),
            'status' => 'available',
            'caption' => $parsed['caption'],
            'description' => $parsed['description'],
            'hashtags' => $parsed['hashtags'],
            'script_notes' => $parsed['script_notes'],
            'created_at' => now(),
            'updated_at' => now(),
            'historical_warning' => false,
            'previously_used_at' => null,
        ];

        if ($existing) {
            $row['historical_warning'] = true;
            $row['previously_used_at'] = optional($existing->created_at)->toDateString();

            $historicalWarnings[] = [
                'title' => $parsed['title'],
                'platform' => $parsed['platform'],
                'content_bucket' => $parsed['content_bucket'],
                'previously_used_at' => optional($existing->created_at)->toDateString(),
                'previous_status' => $existing->status ?? null,
            ];
        }

        $readyRows[] = $row;
    }

    return [
        'ready_rows' => $readyRows,
        'summary' => [
            'parsed_count' => count($readyRows),
            'invalid_rows_count' => count($invalidRows),
            'skipped_duplicates_count' => count($skippedDuplicates),
            'historical_warnings_count' => count($historicalWarnings),
            'safe_import_count' => collect($readyRows)->where('historical_warning', false)->count(),
        ],
        'invalid_rows' => array_values($invalidRows),
        'skipped_duplicates' => array_values($skippedDuplicates),
        'historical_warnings' => array_values($historicalWarnings),
    ];
}

    private function parseBulkLine(string $line): array
{
    $parts = array_map('trim', explode('|', $line));
    $count = count($parts);

    $validPlatforms = $this->validPlatforms();

    $platform = null;
    $series = null;
    $voiceTool = null;
    $contentBucket = null;
    $title = null;
    $caption = null;
    $description = null;
    $hashtags = null;
    $scriptNotes = null;

    if (!in_array($count, [4, 5, 6, 7, 8, 9], true)) {
        return [
            'valid' => false,
            'error' => 'This row could not be read. Make sure each topic is on its own line and fields are separated with |.',
        ];
    }

    // shortest: platform|series|content_bucket|title
    if ($count === 4) {
        $platform = $this->normalizePlatform($parts[0] ?? null);
        $series = !empty($parts[1]) ? strtolower(trim((string) $parts[1])) : null;

        if ($this->looksLikeContentBucket($parts[2] ?? null)) {
            $contentBucket = $parts[2] ?? null;
            $title = $parts[3] ?? null;
        } else {
            $voiceTool = $parts[2] ?? null;
            $title = $parts[3] ?? null;
        }
    }

    // short: platform|series|voice_tool|content_bucket|title
    if ($count === 5) {
        $platform = $this->normalizePlatform($parts[0] ?? null);
        $series = !empty($parts[1]) ? strtolower(trim((string) $parts[1])) : null;
        $voiceTool = $parts[2] ?? null;
        $contentBucket = $parts[3] ?? null;
        $title = $parts[4] ?? null;
    }

    // medium: platform|series|voice_tool|content_bucket|title|caption
    if ($count === 6) {
        $platform = $this->normalizePlatform($parts[0] ?? null);
        $series = !empty($parts[1]) ? strtolower(trim((string) $parts[1])) : null;

        if ($this->looksLikeContentBucket($parts[3] ?? null)) {
            $voiceTool = $parts[2] ?? null;
            $contentBucket = $parts[3] ?? null;
            $title = $parts[4] ?? null;
            $caption = $parts[5] ?? null;
        } else {
            // legacy fallback
            $voiceTool = $parts[2] ?? null;
            $title = $parts[3] ?? null;
            $caption = $parts[4] ?? null;
            $description = $parts[5] ?? null;
        }
    }

    // old long: platform|series|voice_tool|title|caption|description|hashtags
    if ($count === 7) {
        $platform = $this->normalizePlatform($parts[0] ?? null);
        $series = !empty($parts[1]) ? strtolower(trim((string) $parts[1])) : null;
        $voiceTool = $parts[2] ?? null;
        $title = $parts[3] ?? null;
        $caption = $parts[4] ?? null;
        $description = $parts[5] ?? null;
        $hashtags = $parts[6] ?? null;
    }

    // new long: platform|series|voice_tool|content_bucket|title|caption|description|hashtags
    if ($count === 8) {
        $platform = $this->normalizePlatform($parts[0] ?? null);
        $series = !empty($parts[1]) ? strtolower(trim((string) $parts[1])) : null;
        $voiceTool = $parts[2] ?? null;
        $contentBucket = $parts[3] ?? null;
        $title = $parts[4] ?? null;
        $caption = $parts[5] ?? null;
        $description = $parts[6] ?? null;
        $hashtags = $parts[7] ?? null;
    }

    // long + script notes
    if ($count === 9) {
        $platform = $this->normalizePlatform($parts[0] ?? null);
        $series = !empty($parts[1]) ? strtolower(trim((string) $parts[1])) : null;
        $voiceTool = $parts[2] ?? null;
        $contentBucket = $parts[3] ?? null;
        $title = $parts[4] ?? null;
        $caption = $parts[5] ?? null;
        $description = $parts[6] ?? null;
        $hashtags = $parts[7] ?? null;
        $scriptNotes = $parts[8] ?? null;
    }

    if (empty($platform) || !in_array($platform, $validPlatforms, true)) {
        return [
            'valid' => false,
            'error' => 'Platform not recognized. Use one of these: tiktok, instagram, facebook, youtube, website, other.',
        ];
    }

    if (empty($title)) {
        return [
            'valid' => false,
            'error' => 'This row is missing a title. Add the topic title at the end of the row.',
        ];
    }

    if ($this->looksLikeMergedRow(
    $platform,
    $series,
    $voiceTool,
    $contentBucket,
    $title,
    $caption,
    $description,
    $hashtags,
    $scriptNotes
)) {
    return [
        'valid' => false,
        'error' => 'This row looks like two topics were pasted together. Put each topic on its own line and try again.',
    ];
}

    return [
        'valid' => true,
        'platform' => $platform,
        'series' => $series,
        'voice_tool' => !empty($voiceTool) ? trim((string) $voiceTool) : null,
        'content_bucket' => !empty($contentBucket) ? trim((string) $contentBucket) : null,
        'title' => trim((string) $title),
        'caption' => !empty($caption) ? trim((string) $caption) : null,
        'description' => !empty($description) ? trim((string) $description) : null,
        'hashtags' => !empty($hashtags) ? trim((string) $hashtags) : null,
        'script_notes' => !empty($scriptNotes) ? trim((string) $scriptNotes) : null,
    ];
}

private function looksLikeContentBucket(?string $value): bool
{
    $value = trim((string) $value);

    if ($value === '') {
        return false;
    }

    // content bucket usually looks like slug-ish profile bucket:
    // norevia-world-4821, gaming-norevia-7314, etc.
    // voice tools like TTSMaker / ElevenLabs usually don't match this shape
    return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+){1,}$/', strtolower($value));
}

private function normalizePlatform(?string $value): ?string
{
    $platform = strtolower(trim((string) $value));

    if ($platform === '') {
        return null;
    }

    $aliases = [
        // YouTube
        'yt' => 'youtube',
        'youtube shorts' => 'youtube',
        'shorts' => 'youtube',

        // Instagram
        'ig' => 'instagram',
        'insta' => 'instagram',
        'instagram reels' => 'instagram',
        'reels' => 'instagram',

        // Facebook
        'fb' => 'facebook',
        'face' => 'facebook',
        'facebook reels' => 'facebook',

        // TikTok
        'tt' => 'tiktok',
        'tik tok' => 'tiktok',
        'tik tok shorts' => 'tiktok',
        'tiktok shorts' => 'tiktok',

        // Website
        'web' => 'website',
        'site' => 'website',
        'blog' => 'website',
        'article' => 'website',

        // Other
        'misc' => 'other',
        'custom' => 'other',
    ];

    return $aliases[$platform] ?? $platform;
}

private function validPlatforms(): array
{
    return ['tiktok', 'facebook', 'instagram', 'youtube', 'website', 'other'];
}

private function looksLikeVoiceTool(?string $value): bool
{
    $value = strtolower(trim((string) $value));

    if ($value === '') {
        return false;
    }

    $knownVoiceTools = [
        'voicemaker',
        'ttsmaker',
        'elevenlabs',
        'playht',
        'other',
    ];

    return in_array($value, $knownVoiceTools, true);
}

private function looksLikeSeries(?string $value): bool
{
    $value = strtolower(trim((string) $value));

    if ($value === '') {
        return false;
    }

    // common pattern: short slug-like series names
    if (preg_match('/^[a-z0-9]+(?:[-#][a-z0-9]+)*$/', $value)) {
        return true;
    }

    return false;
}

private function looksLikeMergedRow(
    ?string $platform,
    ?string $series,
    ?string $voiceTool,
    ?string $contentBucket,
    ?string $title,
    ?string $caption,
    ?string $description,
    ?string $hashtags,
    ?string $scriptNotes
): bool {
    $validPlatforms = $this->validPlatforms();

    $title = trim((string) $title);
    $caption = trim((string) $caption);
    $description = trim((string) $description);
    $hashtags = trim((string) $hashtags);
    $scriptNotes = trim((string) $scriptNotes);

    // 1) title accidentally glued to a platform word
    foreach ($validPlatforms as $validPlatform) {
        if (str_ends_with(strtolower($title), $validPlatform)) {
            return true;
        }
    }

    // 2) suspicious structural shift:
    // caption looks like a series,
    // description looks like a voice tool,
    // hashtags looks like a content bucket,
    // script notes looks like a title
    if (
        $caption !== '' &&
        $description !== '' &&
        $hashtags !== '' &&
        $scriptNotes !== '' &&
        $this->looksLikeSeries($caption) &&
        $this->looksLikeVoiceTool($description) &&
        $this->looksLikeContentBucket($hashtags) &&
        str_word_count($scriptNotes) >= 2
    ) {
        return true;
    }

    // 3) if script notes looks like a full title, but hashtags is not actually hashtags
    if (
        $scriptNotes !== '' &&
        str_word_count($scriptNotes) >= 2 &&
        $hashtags !== '' &&
        !str_contains($hashtags, '#') &&
        $this->looksLikeContentBucket($hashtags)
    ) {
        return true;
    }

    return false;
}

    private function detectCategory(?string $platform, ?string $series, ?string $title, ?string $contentBucket = null): string
    {
        $platform = strtolower((string) $platform);
        $series = strtolower((string) $series);
        $title = strtolower((string) $title);
        $contentBucket = strtolower((string) $contentBucket);

        if (
            str_contains($contentBucket, 'gaming') ||
            str_contains($series, 'cs2') ||
            str_contains($title, 'cs2')
        ) {
            return 'cs2';
        }

        if (
            str_contains($contentBucket, 'balkan') ||
            str_contains($platform, 'facebook') ||
            $platform === 'fb' ||
            str_contains($series, 'svijet40') ||
            str_contains($series, 'balkan')
        ) {
            return 'fb';
        }

        if (
            str_contains($contentBucket, 'world') ||
            str_contains($series, 'world') ||
            str_contains($series, 'fact') ||
            str_contains($series, 'place') ||
            str_contains($title, 'world')
        ) {
            return 'world';
        }

        return 'general';
    }

        private function userHasActiveBucket(int $userId, ?string $bucket): bool
    {
        $bucket = trim((string) $bucket);

        if ($bucket === '') {
            return false;
        }

        return PublishingProfile::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->whereRaw('LOWER(content_bucket) = ?', [strtolower($bucket)])
            ->exists();
    }
}