<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageBranchController extends Controller
{
    public function index()
    {
        $branches = DB::table('laravel.branches as branches')
            ->leftJoin(
                'laravel.branch_menu_items as branch_menu_items',
                'branches.id',
                '=',
                'branch_menu_items.branch_id'
            )
            ->leftJoin(
                'laravel.branch_inventory as branch_inventory',
                function ($join) {
                    $join->on(
                        'branches.id',
                        '=',
                        'branch_inventory.branch_id'
                    )
                    ->whereNull('branch_inventory.deleted_at');
                }
            )
            ->select(
                'branches.id',
                'branches.name',
                'branches.address',
                'branches.created_at'
            )
            ->selectRaw(
                'COUNT(DISTINCT CASE
                    WHEN branch_menu_items.is_available = true
                    THEN branch_menu_items.menu_item_id
                END) as dishes_available'
            )
            ->selectRaw(
                'COALESCE(SUM(branch_inventory.total_item_value), 0) as inventory_value'
            )
            ->groupBy(
                'branches.id',
                'branches.name',
                'branches.address',
                'branches.created_at'
            )
            ->orderByDesc('branches.created_at')
            ->get();

        return view(
            'admin.branch.manageBranch',
            compact('branches')
        );
    }
}
