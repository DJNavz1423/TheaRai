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

        $archives = $trashedIngredients->sortByDesc('deleted_at');

        return view('admin.archive.archives', compact('archives'));
    }

    public function restore(Request $request){
        $table = $request->input('table_name');
        $id = $request->input('id');

        $allowedTables = ['ingredients', 'menu_items', 'users'];
        if(!in_array($table, $allowedTables)){
            return back()->with('error', 'Invalid table reference!');      
        }
        
        $itemName = DB::table('laravel.' . $table)
            ->where('id', $id)
            ->value('name');

        DB::table('laravel.' . $table)
            ->where('id', $id)
            ->update(['deleted_at' => null, 'updated_at' => now()]);

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

        $allowedTables = ['ingredients', 'menu_items', 'users'];
        if (!in_array($table, $allowedTables)) {
            return back()->with('error', 'Invalid table reference.');
        }

        $itemName = DB::table('laravel.' . $table)->where('id', $id)->value('name');

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
