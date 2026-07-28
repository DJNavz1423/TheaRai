<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuCategoryController extends Controller{
    public function index(){
        $categories = DB::table('laravel.menu_categories')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.menu.menuCategory', compact('categories'));
    }
}
