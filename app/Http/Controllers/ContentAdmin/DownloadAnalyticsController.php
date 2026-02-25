<?php

namespace App\Http\Controllers\ContentAdmin;

use App\Http\Controllers\Controller;
use App\Models\DownloadLog;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DownloadAnalyticsController extends Controller
{
    public function index()
    {
        // ---------- COUNTERS ----------
        $stats = [
            'totalItems' => Item::count(),
            'publishedItems' => Item::where('is_published', true)->count(),
            'totalDownloadsCounter' => (int) Item::sum('download_count'),
            'totalLogs' => DownloadLog::count(),
            'uniqueUsers' => (int) DownloadLog::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'uniqueIps' => (int) DownloadLog::distinct('ip_address')->count('ip_address'),
            'guestDownloads' => (int) DownloadLog::where('is_guest', true)->count(),
            'userDownloads' => (int) DownloadLog::where('is_guest', false)->count(),
        ];

        // ---------- LAST 7 DAYS ----------
        $since7 = now()->subDays(6)->startOfDay();

        $last7 = DownloadLog::query()
            ->selectRaw("date(created_at) as day, count(*) as cnt")
            ->where('created_at', '>=', $since7)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($r) => ['day' => $r->day, 'count' => (int) $r->cnt]);

        $guestUserLast7 = DownloadLog::query()
            ->selectRaw("date(created_at) as day,
                sum(case when is_guest = 1 then 1 else 0 end) as guests,
                sum(case when is_guest = 0 then 1 else 0 end) as users")
            ->where('created_at', '>=', $since7)
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($r) => ['day' => $r->day, 'guests' => (int) $r->guests, 'users' => (int) $r->users]);

        // ---------- TOP ITEMS ----------
        $topOverall = Item::query()
            ->orderByDesc('download_count')
            ->take(10)
            ->get(['id', 'title', 'slug', 'category', 'download_count'])
            ->map(fn ($i) => [
                'id' => $i->id,
                'title' => $i->title,
                'slug' => $i->slug,
                'category' => $i->category,
                'download_count' => (int) $i->download_count,
            ]);

        $top7raw = DownloadLog::query()
            ->select('item_id', DB::raw('count(*) as cnt'))
            ->where('created_at', '>=', $since7)
            ->groupBy('item_id')
            ->orderByDesc('cnt')
            ->take(10)
            ->get();

        $itemsMap = Item::whereIn('id', $top7raw->pluck('item_id'))
            ->get(['id', 'title', 'slug', 'category'])
            ->keyBy('id');

        $top7days = $top7raw->map(fn ($row) => [
            'item_id' => (int) $row->item_id,
            'title' => $itemsMap[$row->item_id]->title ?? 'Deleted item',
            'slug' => $itemsMap[$row->item_id]->slug ?? null,
            'category' => $itemsMap[$row->item_id]->category ?? null,
            'count' => (int) $row->cnt,
        ]);

        // ---------- BREAKDOWNS ----------
        $byCategory = Item::query()
            ->select('category', DB::raw('sum(download_count) as cnt'))
            ->groupBy('category')
            ->orderByDesc('cnt')
            ->get()
            ->map(fn ($r) => ['category' => $r->category, 'count' => (int) $r->cnt]);

        // ---------- TOP IPs / AGENTS ----------
        $topIps = DownloadLog::query()
            ->select('ip_address', DB::raw('count(*) as cnt'))
            ->groupBy('ip_address')
            ->orderByDesc('cnt')
            ->take(10)
            ->get()
            ->map(fn ($r) => ['ip' => $r->ip_address, 'count' => (int) $r->cnt]);

        $topAgents = DownloadLog::query()
            ->select('user_agent', DB::raw('count(*) as cnt'))
            ->whereNotNull('user_agent')
            ->groupBy('user_agent')
            ->orderByDesc('cnt')
            ->take(10)
            ->get()
            ->map(fn ($r) => ['ua' => $r->user_agent, 'count' => (int) $r->cnt]);

        // ---------- SUSPICIOUS ----------
        $suspiciousAgents = DownloadLog::query()
            ->select('user_agent', DB::raw('count(*) as cnt'))
            ->whereNotNull('user_agent')
            ->where(function ($q) {
                $q->where('user_agent', 'like', '%bot%')
                    ->orWhere('user_agent', 'like', '%spider%')
                    ->orWhere('user_agent', 'like', '%crawl%')
                    ->orWhere('user_agent', 'like', '%curl%')
                    ->orWhere('user_agent', 'like', '%wget%')
                    ->orWhere('user_agent', 'like', '%python%')
                    ->orWhere('user_agent', 'like', '%java%')
                    ->orWhere('user_agent', 'like', '%http%');
            })
            ->groupBy('user_agent')
            ->orderByDesc('cnt')
            ->take(10)
            ->get()
            ->map(fn ($r) => ['ua' => $r->user_agent, 'count' => (int) $r->cnt]);

        $suspiciousIps24h = DownloadLog::query()
            ->select('ip_address', DB::raw('count(*) as cnt'))
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('ip_address')
            ->havingRaw('count(*) >= 20')
            ->orderByDesc('cnt')
            ->take(10)
            ->get()
            ->map(fn ($r) => ['ip' => $r->ip_address, 'count' => (int) $r->cnt]);

        return Inertia::render('ContentAdmin/Downloads/Analytics', [
            'stats' => $stats,
            'last7' => $last7,
            'guestUserLast7' => $guestUserLast7,
            'topOverall' => $topOverall,
            'top7days' => $top7days,
            'byCategory' => $byCategory,
            'topIps' => $topIps,
            'topAgents' => $topAgents,
            'suspiciousAgents' => $suspiciousAgents,
            'suspiciousIps24h' => $suspiciousIps24h,
        ]);
    }
}
