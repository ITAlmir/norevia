<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
{
    return Sponsor::where('user_id', auth()->id())
        ->orderByDesc('id') // ili orderByDesc('created_at')
        ->get();
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $sponsor = Sponsor::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json($sponsor, 201);
    }

    public function destroy(Sponsor $sponsor)
{
    abort_unless($sponsor->user_id === auth()->id(), 403);

    $sponsor->delete();

    return response()->json(['success' => true]);
}

}
