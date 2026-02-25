<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ SQLite stability (WAL + timeout)
        if (config('database.default') === 'sqlite') {
           // DB::statement('PRAGMA journal_mode = WAL;');
            DB::statement('PRAGMA busy_timeout = 5000;');
            DB::statement('PRAGMA synchronous = NORMAL;');
        }

        if (config('database.default') === 'sqlite') {
    DB::statement('PRAGMA journal_mode = DELETE;');
    DB::statement('PRAGMA synchronous = NORMAL;');
    DB::statement('PRAGMA temp_store = MEMORY;');
    DB::statement('PRAGMA cache_size = -20000;'); // ~20MB cache
}

    RateLimiter::for('downloads', function (Request $request) {
    $key = optional($request->user())->id
        ? 'user:' . $request->user()->id
        : 'ip:' . $request->ip();

    return Limit::perMinute(10)->by($key);
});



        // existing
        Inertia::share([
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'appName' => config('app.name'),
        ]);

        Vite::prefetch(concurrency: 3);
    }
}
