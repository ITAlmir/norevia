<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Page;

class SuperAdminController extends Controller
{
     public function dashboard()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('SuperAdmin/Dashboard', [
            'users' => User::latest()->take(10)->get(),
            'pages' => Page::with('author')->latest()->take(10)->get(),
        ]);
    }

    public function users()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('SuperAdmin/Users', [
            'users' => User::latest()->paginate(20),
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have permission to perform this action.'
            ], 403);
        }

        $request->validate([
            'role' => 'required|in:super_admin,content_admin,user',
        ]);

        try {
            $user->update(['role' => $request->role]);
            
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'user' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update role',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
