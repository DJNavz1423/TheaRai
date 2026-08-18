<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function index(): View{
        $trashedIngredients = DB::table('laravel.ingredients')
            ->whereNotNull('deleted_at')
            ->select('id', 'name', DB::raw("'Ingredient' as type"), 'deleted_at', DB::raw("'ingredients' as table_name"))
            ->get();

        $trashedBranchInventory = DB::table('laravel.branch_inventory as bi')
            ->join('laravel.ingredients as i', 'bi.ingredient_id', '=', 'i.id')
            ->whereNotNull('bi.deleted_at')
            ->select(
                'bi.id',
                'i.name as name', 
                DB::raw("'Branch Ingredients' as type"),
                'bi.deleted_at',
                DB::raw("'branch_inventory' as table_name")
            )
            ->get();

        $trashedCategories = DB::table('laravel.ingredient_categories')
            ->whereNotNull('deleted_at')
            ->select(
                'id',
                'name',
                DB::raw("'Ingredient Category' as type"),
                'deleted_at',
                DB::raw("'ingredient_categories' as table_name")
            )
            ->get();

        $trashedUnits = DB::table('laravel.units')
            ->whereNotNull('deleted_at')
            ->select(
                'id',
                DB::raw("CONCAT(name, ' (', abbreviation, ')') as name"),
                DB::raw("'Unit' as type"),
                'deleted_at',
                DB::raw("'units' as table_name")
            )
            ->get();

        $trashedMenuItems = DB::table('laravel.menu_items')
            ->whereNotNull('deleted_at')
            ->select('id', 'name', DB::raw("'Menu Item' as type"), 'deleted_at', DB::raw("'menu_items' as table_name"))
            ->get();

        $trashedUsers = DB::table('laravel.users')
            ->whereNotNull('deleted_at')
            ->select('id', 'name', DB::raw("'User' as type"), 'deleted_at', DB::raw("'users' as table_name"))
            ->get();
        
         $trashedTables = DB::table('laravel.tables')
            ->whereNotNull('deleted_at')
            ->select(
                'id',
                DB::raw("CONCAT('Table ', table_number) as name"),
                DB::raw("'Table' as type"),
                'deleted_at',
                DB::raw("'tables' as table_name")
            )
            ->get();
            
        $archives = $trashedIngredients
            ->merge($trashedBranchInventory)
            ->merge($trashedCategories)
            ->merge($trashedUnits)
            ->merge($trashedMenuItems)
            ->merge($trashedUsers)
            ->merge($trashedTables)
            ->sortByDesc('deleted_at');

        return view('admin.archive.archives', compact('archives'));
    }

    public function restore(Request $request){
        $table = $request->input('table_name');
        $id = $request->input('id');

        $allowedTables = ['ingredients', 'ingredient_categories',
    'units', 'branch_inventory', 'menu_items', 'users', 'tables'];

        if(!in_array($table, $allowedTables)){
            return back()->with('error', 'Invalid table reference!');      
        }
        
        if ($table === 'branch_inventory') {
            $itemName = DB::table('laravel.branch_inventory as bi')
                ->join('laravel.ingredients as i', 'bi.ingredient_id', '=', 'i.id')
                ->where('bi.id', $id)
                ->value('i.name');
        } else if($table === 'tables'){
            $number = DB::table('laravel.tables')
                ->where('id', $id)
                ->value('table_number');

            $itemName = 'Table ' . $number;
        } else {
            $itemName = DB::table('laravel.' . $table)
            ->where('id', $id)
            ->value('name');
        }

        $updateData = ['deleted_at' => null];

        if (!in_array($table, ['branch_inventory', 'ingredient_categories', 'units'])) {
            $updateData['updated_at'] = now();
        }

        DB::table('laravel.' . $table)
            ->where('id', $id)
            ->update($updateData);

        $modules = [
            'ingredients' => 'ingredient',
            'ingredient_categories' => 'ingredient_category',
            'units'       => 'unit',
            'menu_items'  => 'menu_item',
            'users'       => 'user',
            'tables'      => 'table'
        ];

        $module = $modules[$table] ?? 'system';

        // Log the restore action
        $this->logActivity('restored', $module, $id, "Restored {$module} from archive: {$itemName}");

        return back()->with('success', 'Item restored successfully!');
    }

    public function forceDelete(Request $request){
        $table = $request->input('table_name');
        $id = $request->input('id');

        $allowedTables = ['ingredients', 'branch_inventory', 'menu_items', 'users', 'tables'];

        if (!in_array($table, $allowedTables)) {
            return back()->with('error', 'Invalid table reference.');
        }

        if ($table === 'branch_inventory') {
            $itemName = DB::table('laravel.branch_inventory as bi')
                ->join('laravel.ingredients as i', 'bi.ingredient_id', '=', 'i.id')
                ->where('bi.id', $id)
                ->value('i.name');
        } elseif($table === 'tables'){
            $number = DB::table('laravel.tables')
                ->where('id', $id)
                ->value('table_number');

            $itemName = 'Table ' . $number;
        } else {
            $itemName = DB::table('laravel.' . $table)->where('id', $id)->value('name');
        }

        DB::table('laravel.' . $table)->where('id', $id)->delete();

        // Map the table name to a singular module name for clean logging
        $modules = [
            'ingredients' => 'ingredient',
            'menu_items'  => 'menu_item',
            'users'       => 'user',
            'table'       => 'table'
        ];
        
        $module = $modules[$table] ?? 'system';

        // Log the permanent deletion
        $this->logActivity('deleted', $module, $id, "Permanently deleted {$module}: {$itemName}");

        return back()->with('success', 'Item permanently deleted!');
    }
}