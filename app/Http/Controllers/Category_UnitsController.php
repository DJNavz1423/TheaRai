<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Category_UnitsController extends Controller
{
    public function index(){
        $categories = DB::table('laravel.ingredient_categories')
            ->orderBy('created_at', 'desc')
            ->whereNull('deleted_at')
            ->get();

        $units = DB::table('laravel.units')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();


        return view('admin.inventory.categories', compact('categories', 'units'));
    }

    public function store(Request $request){
        if ($request->table === 'category') {

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:pgsql.laravel.ingredient_categories,name',
            ]);

            $categoryId = DB::table('laravel.ingredient_categories')->insertGetId([
                'name' => $validated['name'],
                'created_at' => now(),
            ]);

            $this->logActivity(
                'created',
                'ingredient_category',
                $categoryId,
                "Created ingredient category: {$validated['name']}"
            );

            return back()->with('success', 'Category added successfully.');
        }

        if ($request->table === 'unit') {

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:pgsql.laravel.units,name',
                'abbreviation' => 'required|string|max:5|unique:pgsql.laravel.units,abbreviation',
            ]);

            $unitId = DB::table('laravel.units')->insertGetId([
                'name' => $validated['name'],
                'abbreviation' => strtoupper($validated['abbreviation']),
                'created_at' => now(),
            ]);

            $this->logActivity(
                'created',
                'unit',
                $unitId,
                "Created unit: {$validated['name']} / ({$validated['abbreviation']})"
            );

            return back()->with('success', 'Unit added successfully.');
        }

        abort(404);
    }

    public function update(Request $request, $id){
        if ($request->table == 'category') {

            $validated = $request->validate([
                'name' => 'required|unique:pgsql.laravel.ingredient_categories,name,' . $id,
            ]);

            DB::table('laravel.ingredient_categories')
                ->where('id', $id)
                ->update([
                    'name' => $validated['name'],
                ]);

            $this->logActivity(
                'updated',
                'ingredient_category',
                $id,
                "Updated ingredient category: {$validated['name']}"
            );

            return back()->with('success', 'Category updated.');
        }

        if ($request->table == 'unit') {

            $validated = $request->validate([
                'name' => 'required|unique:pgsql.laravel.units,name,' . $id,
                'abbreviation' => 'required|unique:pgsql.laravel.units,abbreviation,' . $id,
            ]);

            DB::table('laravel.units')
                ->where('id', $id)
                ->update([
                    'name' => $validated['name'],
                    'abbreviation' => strtoupper($validated['abbreviation']),
                ]);

            $this->logActivity(
                'updated',
                'unit',
                $id,
                "Updated unit: {$validated['name']} / (" . strtoupper($validated['abbreviation']) . ")"
            );

            return back()->with('success', 'Unit updated.');
        }

        abort(404);
    }

    public function destroy(Request $request, $id){
        if ($request->table === 'category') {

            $category = DB::table('laravel.ingredient_categories')
                ->where('id', $id)
                ->first();

            if (!$category) {
                return back()->with('error', 'Category not found.');
            }

            DB::table('laravel.ingredient_categories')
                ->where('id', $id)
                ->update([
                    'deleted_at' => now()
                ]);

            $this->logActivity(
                'archived',
                'ingredient_category',
                $id,
                "Moved ingredient category to trash: {$category->name}"
            );

            return back()->with(
                'success',
                'Category moved to trash successfully.'
            );
        }

        if ($request->table === 'unit') {

            $unit = DB::table('laravel.units')
                ->where('id', $id)
                ->first();

            if (!$unit) {
                return back()->with('error', 'Unit not found.');
            }

            DB::table('laravel.units')
                ->where('id', $id)
                ->update([
                    'deleted_at' => now()
                ]);

            $this->logActivity(
                'archived',
                'unit',
                $id,
                "Moved unit to trash: {$unit->name} ({$unit->abbreviation})"
            );

            return back()->with(
                'success',
                'Unit moved to trash successfully.'
            );
        }

        abort(404);
    }
}