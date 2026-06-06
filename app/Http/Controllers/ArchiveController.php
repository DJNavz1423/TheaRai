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

        $trashedMenuItems = DB::table('laravel.menu_items')
            ->whereNotNull('deleted_at')
            ->select('id', 'name', DB::raw("'Menu Item' as type"), 'deleted_at', DB::raw("'menu_items' as table_name"))
            ->get();

        $trashedUsers = DB::table('laravel.users')
            ->whereNotNull('deleted_at')
            ->select('id', 'name', DB::raw("'User' as type"), 'deleted_at', DB::raw("'users' as table_name"))
            ->get();
            
        $archives = $trashedIngredients
            ->merge($trashedBranchInventory)
            ->merge($trashedMenuItems)
            ->merge($trashedUsers)
            ->sortByDesc('deleted_at');

        return view('admin.archive.archives', compact('archives'));
    }

    public function restore(Request $request){
        $table = $request->input('table_name');
        $id = $request->input('id');

        $allowedTables = ['ingredients', 'branch_inventory', 'menu_items', 'users'];
        if(!in_array($table, $allowedTables)){
            return back()->with('error', 'Invalid table reference!');      
        }
        
        if ($table === 'branch_inventory') {
            $itemName = DB::table('laravel.branch_inventory as bi')
                ->join('laravel.ingredients as i', 'bi.ingredient_id', '=', 'i.id')
                ->where('bi.id', $id)
                ->value('i.name');
        } else {
            $itemName = DB::table('laravel.' . $table)->where('id', $id)->value('name');
        }

        $updateData = ['deleted_at' => null];

        if ($table !== 'branch_inventory') {
            $updateData['updated_at'] = now();
        }

        DB::table('laravel.' . $table)
            ->where('id', $id)
            ->update($updateData);

        $modules = [
            'ingredients' => 'ingredient',
            'menu_items'  => 'menu_item',
            'users'       => 'user'
        ];
        $module = $modules[$table] ?? 'system';

        // Log the restore action
        $this->logActivity('restored', $module, $id, "Restored {$module} from archive: {$itemName}");

        return back()->with('success', 'Item restored successfully!');
    }

    public function forceDelete(Request $request){
        $table = $request->input('table_name');
        $id = $request->input('id');

        $allowedTables = ['ingredients', 'branch_inventory', 'menu_items', 'users'];
        if (!in_array($table, $allowedTables)) {
            return back()->with('error', 'Invalid table reference.');
        }

        if ($table === 'branch_inventory') {
            $itemName = DB::table('laravel.branch_inventory as bi')
                ->join('laravel.ingredients as i', 'bi.ingredient_id', '=', 'i.id')
                ->where('bi.id', $id)
                ->value('i.name');
        } else {
            $itemName = DB::table('laravel.' . $table)->where('id', $id)->value('name');
        }

        DB::table('laravel.' . $table)->where('id', $id)->delete();

        // Map the table name to a singular module name for clean logging
        $modules = [
            'ingredients' => 'ingredient',
            'menu_items'  => 'menu_item',
            'users'       => 'user'
        ];
        $module = $modules[$table] ?? 'system';

        // Log the permanent deletion
        $this->logActivity('deleted', $module, $id, "Permanently deleted {$module}: {$itemName}");

        return back()->with('success', 'Item permanently deleted!');
    }
}