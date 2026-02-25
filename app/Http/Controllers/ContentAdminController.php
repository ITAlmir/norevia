<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentAdminController extends Controller
{
    public function dashboard()
{
    // CONTENT ADMIN ILI SUPER ADMIN
    $user = auth()->user();
    if (!$user->isContentAdmin() && !$user->isSuperAdmin()) {
        abort(403, 'Unauthorized');
    }
    
    // Broj stranica koje je kreirao ovaj user
    $stats = [
        'totalPages' => Page::where('user_id', auth()->id())->count(),
        'publishedPages' => Page::where('user_id', auth()->id())->where('status', 'published')->count(),
    ];

    return Inertia::render('ContentAdmin/Dashboard', [
        'stats' => $stats,
        'recentPages' => Page::where('user_id', auth()->id())->latest()->take(5)->get(),
    ]);
}
}