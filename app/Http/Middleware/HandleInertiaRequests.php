<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // Osnovni props
        $sharedProps = [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'email_verified_at' => $request->user()->email_verified_at,
                ] : null,
            ],
            'brand' => [
                'site_name' => config('app.name', 'Norevia'),
                'title_suffix' => ' | ' . config('app.name', 'Norevia'),
                'default_description' => 'Utility tools and optimization guides for gamers, creators and PC users.',
            ],

            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'flash' => [
                'message' => $request->session()->get('message'),
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];

        // Dodaj ziggy samo ako postoji
        if (class_exists(\Tighten\Ziggy\Ziggy::class)) {
            $sharedProps['ziggy'] = fn () => [
                ...(new \Tighten\Ziggy\Ziggy)->toArray(),
                'location' => $request->url(),
            ];
        }

        return array_merge(parent::share($request), $sharedProps);
    }
}