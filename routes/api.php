<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\DownloadController;

Route::get('/downloads', [DownloadController::class, 'index']);

Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{id}', [ItemController::class, 'show']);
Route::post('/items', [ItemController::class, 'store'])->middleware('auth'); // ili auth:sanctum ako koristiš token API
