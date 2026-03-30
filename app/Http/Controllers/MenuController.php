<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View{
        // 1. Get menu items and join categories for the main table
        $menuItems = DB::table('laravel.menu_items as mi')
            ->leftJoin('laravel.menu_categories as mc', 'mi.category_id', '=', 'mc.id')
            ->select('mi.*', 'mc.name as category_name')
            ->orderBy('mi.id', 'desc')
            ->get();

        // 2. Get categories for the "Add Dish" dropdown
        $categories = DB::table('laravel.menu_categories')
            ->orderBy('name')
            ->get();

        // 3. Get ingredients for the Javascript Recipe Builder
        // We only need specific columns here to keep the JSON payload light
        $ingredients = DB::table('laravel.ingredients as ingredient')
            ->join('laravel.units as p_unit', 'ingredient.primary_unit_id','=', 'p_unit.id')
            ->join('laravel.units as s_unit', 'ingredient.secondary_unit_id','=', 's_unit.id')
            ->select('ingredient.id', 'ingredient.name', 'ingredient.s_unit_price', 'ingredient.purchase_price', 'p_unit.abbreviation as primary_unit_abbr', 's_unit.abbreviation as secondary_unit_abbr')
            ->get();

        // Return the view and pass the data
        return view('admin.menu.menu', compact('menuItems', 'categories', 'ingredients'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'final_price' => 'required|numeric|min:0',
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_id' => 'required|integer',
            'ingredients.*.quantity_used' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try{
            $menuItemId = DB::table('laravel.menu_items')->insertGetId([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'final_price' => $validated['final_price'],
                'created_at' => now()
            ]);

            if(!empty($validated['ingredients'])){
                $pivotData = [];
                foreach($validated['ingredients'] as $item){
                    $pivotData[] = [
                        'menu_item_id' => $menuItemId,
                        'ingredient_id' => $item['ingredient_id'],
                        'quantity_used' => $item['quantity_used']
                    ];
                }

                DB::table('laravel.menu_item_ingredient')->insert($pivotData);
            }

            DB::commit();
            return back()->with('success', 'Menu item added successfully!');
        }catch(Exception $e){
            DB::rollback();
            return back()->withErrors('Error saving menu item: '. $e->getMessage());
        }
    }
}
