<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function createItem()
    {
        return Inertia::render('Admin/Items/Create');
    }

    public function categories()
    {
        return Inertia::render('Admin/Categories/Index');
    }

    public function users()
    {
        return Inertia::render('Admin/Users/Index');
    }
}