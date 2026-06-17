<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('laravel.ingredient_categories')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.inventory.categories', compact('categories'));
    }
}
