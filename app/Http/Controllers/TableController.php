<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View {
        $tables = DB::table('laravel.tables')
            ->whereNull('deleted_at')
            ->join('laravel.branches', 'tables.branch_id', '=', 'branches.id')
            ->select('tables.*', 'branches.name as branch_name')
            ->orderBy('branches.name')
            ->orderBy('tables.table_number')
            ->get();

        $branches = DB::table('laravel.branches')->orderBy('name')->get();
        
        // This grabs your app's base URL (e.g., http://127.0.0.1:8000) from the .env file
        $baseUrl = config('app.url');

        return view('admin.menu.tables', compact('tables', 'branches', 'baseUrl'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|integer|exists:pgsql.laravel.branches,id',
            'table_number' => 'required|string|max:50'
        ]);

        $exists = DB::table('laravel.tables')
            ->where('branch_id', $validated['branch_id'])
            ->where('table_number', $validated['table_number'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'This table number already exists in that branch!');
        }

        DB::table('laravel.tables')->insert([
            'branch_id' => $validated['branch_id'],
            'table_number' => $validated['table_number'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Table created successfully!');
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'branch_id' => 'required|integer|exists:pgsql.laravel.branches,id',
            'table_number' => 'required|string|max:50'
        ]);

        $exists = DB::table('laravel.tables')
            ->where('branch_id', $validated['branch_id'])
            ->where('table_number', $validated['table_number'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This table number already exists in that branch!');
        }

        DB::table('laravel.tables')
            ->where('id', $id)
            ->update([
                'branch_id' => $validated['branch_id'],
                'table_number' => $validated['table_number'],
                'updated_at' => now()
            ]);

        return back()->with('success', 'Table updated successfully!');
    }

    public function destroy($id){
        $table = DB::table('laravel.tables')
            ->where('id', $id)
            ->first();

        if (!$table) {
            return back()->with('error', 'Table not found!');
        }

        DB::table('laravel.tables')
            ->where('id', $id)
            ->update([
                'deleted_at' => now()
            ]);

        $this->logActivity(
            'archived',
            'table',
            $id,
            "Moved table to trash: {$table->table_number}"
        );
    
        return back()->with('success', 'Table moved to trash successfully!'
        );

        /*DB::table('laravel.tables')->where('id', $id)->delete();
        return back()->with('success', 'Table deleted!');*/
    }
}