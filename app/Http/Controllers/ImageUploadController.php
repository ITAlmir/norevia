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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
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
            'all_data' => $request->all()
        ]);

        if (!$request->hasFile('image')) {
            Log::error('No image file in request');
            return response()->json([
                'success' => false,
                'error' => 'No image file provided'
            ], 400);
        }

        $file = $request->file('image');
        
        if (!$file->isValid()) {
            Log::error('Invalid image file', ['error' => $file->getErrorMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Invalid image file: ' . $file->getErrorMessage()
            ], 400);
        }

        try {
            // Kreiraj direktno u public/uploads folder
            $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Kreiraj uploads folder ako ne postoji
            $uploadsPath = public_path('uploads');
            if (!file_exists($uploadsPath)) {
                mkdir($uploadsPath, 0777, true);
            }
            
            // Premjesti fajl direktno
            $file->move($uploadsPath, $filename);
            
            // Vrati direktan URL
            $url = url('uploads/' . $filename);
            
            Log::info('Image uploaded successfully', [
                'filename' => $filename,
                'url' => $url,
                'path' => $uploadsPath . '/' . $filename
            ]);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => 'uploads/' . $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}