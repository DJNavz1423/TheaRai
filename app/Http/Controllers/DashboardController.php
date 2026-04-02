<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View{
        #get inventory value
        $totalInventoryValue = DB::table('laravel.ingredients')->sum('total_item_value');

        #get low stock count
        $lowStockCount = DB::table('laravel.ingredients')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->count();

        #get low stock list
        $lowStockItems = DB::table('laravel.ingredients as ingredient')
            ->join('laravel.units as p_unit', 'ingredient.primary_unit_id', '=', 'p_unit.id')
            ->select('ingredient.*', 'p_unit.abbreviation as primary_unit_abbr')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->limit(7)
            ->get();
        
        $salesData = (object) [
            'monthly_total' => DB::table('laravel.orders')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),

            'daily_total' => DB::table('laravel.orders')
                ->whereDate('created_at', now()->toDateString())
                ->sum('total_amount'),

            'daily_count' => DB::table('laravel.orders')                 
                ->whereDate('created_at', now()->toDateString())
                ->count(),
        ];

        return view('admin.dashboard', compact(
            'totalInventoryValue',
            'lowStockCount',
            'lowStockItems',
            'salesData'
        ));
    }
}
