<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuCategoryController extends Controller{
    public function index(){
        $categories = DB::table('laravel.menu_categories')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.menu.menuCategory', compact('categories'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pgsql.laravel.menu_categories,name',
        ]);

        $categoryId = DB::table('laravel.menu_categories')->insertGetId([
            'name' => $validated['name'],
            'created_at' => now(),
        ]);

        $this->logActivity(
            'created',
            'menu_category',
            $categoryId,
            "Created menu category: {$validated['name']}"
        );

        return back()->with(
            'success',
            'Menu category added successfully.'
        );
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pgsql.laravel.menu_categories,name,' . $id,
        ]);

        DB::table('laravel.menu_categories')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
            ]);

        $this->logActivity(
            'updated',
            'menu_category',
            $id,
            "Updated menu category: {$validated['name']}"
        );

        return back()->with(
            'success',
            'Menu category updated successfully.'
        );
    }

     public function destroy($id){
        $category = DB::table('laravel.menu_categories')
            ->where('id', $id)
            ->first();

        if (!$category) {
            return back()->with('error', 'Menu category not found.');
        }

        DB::table('laravel.menu_categories')
            ->where('id', $id)
            ->update([
                'deleted_at' => now()
            ]);

        $this->logActivity(
            'archived',
            'menu_category',
            $id,
            "Moved menu category to trash: {$category->name}"
        );

        return back()->with(
            'success',
            'Menu category moved to trash successfully!'
        );
    }
}
