<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
        ]);

        $file = $request->file('image');
        
        // Generiši jedinstveno ime
        $filename = 'page_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        // Spremi u storage/app/public/pages
        $path = $file->storeAs('pages', $filename, 'public');
        
        // Kreiraj URL
        $url = Storage::url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path,
            'filename' => $filename
        ]);
    }

    public function uploadFromUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        // Ovdje bi bilo dobro validirati da je URL slika
        // Za sada samo vratimo URL
        return response()->json([
            'success' => true,
            'url' => $request->url,
        ]);
    }

     public function store(Request $request)
{
    Log::info('Image upload started', [
        'has_file' => $request->hasFile('image'),
        'content_type' => $request->header('content-type'),
    ]);

    $request->validate([
        'image' => 'required|file|image|max:51200', // 50MB
    ]);

    $file = $request->file('image');

    $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
    $path = $file->storeAs('uploads', $filename, 'public');

    return response()->json([
        'success' => true,
        'url' => Storage::disk('public')->url($path),
        'path' => $path,
    ]);
}
}