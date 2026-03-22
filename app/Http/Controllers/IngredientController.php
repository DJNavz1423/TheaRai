<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(): View{
        $ingredients = DB::table('laravel.ingredients')
            #join 1: get category
            ->join('laravel.ingredient_categories as cat', 'laravel.ingredients.category_id', '=', 'cat.id')
            # join 2: get primary unit
            ->join('laravel.units as p_unit', 'laravel.ingredients.primary_unit_id', '=', 'p_unit.id')   
            #join 3: get secondary unit
            ->join('laravel.units as s_unit', 'laravel.ingredients.secondary_unit_id', '=', 's_unit.id')
            ->select(
                'laravel.ingredients.*',
                'cat.name as category_name',
                'p_unit.name as primary_unit_name',
                'p_unit.abbreviation as primary_unit_abbr',
                's_unit.name as secondary_unit_name',
                's_unit.abbreviation as secondary_unit_abbr'
            )->get();

            $categories = DB::table('laravel.ingredient_categories')->get();
            $units = DB::table('laravel.units')->get();

            return view('admin.inventory.inventory', compact('ingredients', 'categories', 'units'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            // Format is 'connection.schema.table,column'
        'item_code'         => 'required|unique:pgsql.laravel.ingredients,item_code',
        'name'              => 'required',
        'category_id'       => 'required|exists:pgsql.laravel.ingredient_categories,id',
        'primary_unit_id'      => 'required|exists:pgsql.laravel.units,id',
        'secondary_unit_id'    => 'required|exists:pgsql.laravel.units,id',
        'conversion_factor' => 'required|numeric|min:0.01',
        'purchase_price'    => 'required|numeric|min:0',
        'stock_quantity'    => 'required|numeric|min:0',
        ]);
        DB::table('laravel.ingredients')->insert($validated);
        return back()->with('success', 'Ingredient added successfully!');
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required',
        'category_id' => 'required',
        'purchase_price' => 'required|numeric',
        'stock_quantity' => 'required|numeric',
    ]);

    DB::table('laravel.ingredients')->where('id', $id)->update($validated);
    return back()->with('success', 'Updated successfully!');
}

public function destroy($id)
{
    DB::table('laravel.ingredients')->where('id', $id)->delete();
    return back()->with('success', 'Deleted successfully!');
}
}
