<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageBranchController extends Controller
{
    public function index()
    {
        $menuCounts = DB::table('laravel.branch_menu_items')
            ->where('is_available', true)
            ->select('branch_id')
            ->selectRaw('COUNT(DISTINCT menu_item_id) as dishes_available')
            ->groupBy('branch_id');

        $inventoryValues = DB::table('laravel.branch_inventory')
            ->whereNull('deleted_at')
            ->select('branch_id')
            ->selectRaw('COALESCE(SUM(total_item_value), 0) as inventory_value')
            ->groupBy('branch_id');

        $branches = DB::table('laravel.branches as branches')
            ->leftJoinSub($menuCounts, 'menu_counts', function ($join) {
                $join->on('branches.id', '=', 'menu_counts.branch_id');
            })
            ->leftJoinSub($inventoryValues, 'inventory_values', function ($join) {
                $join->on('branches.id', '=', 'inventory_values.branch_id');
            })
            ->select(
                'branches.id',
                'branches.name',
                'branches.address',
                'branches.created_at',
                DB::raw('COALESCE(menu_counts.dishes_available, 0) as dishes_available'),
                DB::raw('COALESCE(inventory_values.inventory_value, 0) as inventory_value')
            )
            ->orderByDesc('branches.created_at')
            ->get();

        return view(
            'admin.branch.manageBranch',
            compact('branches')
        );
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        DB::table('laravel.branches')->insert([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'created_at' => now(),
        ]);

        return back()->with(
            'success',
            'Branch added successfully!'
        );
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $branch = DB::table('laravel.branches')
            ->where('id', $id)
            ->first();

        if (!$branch) {
            return back()->with('error', 'Branch not found.');
        }

        DB::table('laravel.branches')
            ->where('id', $id)
            ->update([
                'name' => $validated['name'],
                'address' => $validated['address'] ?? null,
            ]);

        return back()->with(
            'success',
            'Branch updated successfully!'
        );
    }
}
