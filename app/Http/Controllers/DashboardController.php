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
        $lowStockItems = DB::table('laravel.ingredients')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalInventoryValue', 'lowStockCount', 'lowStockItems'
        ));
    }
}
