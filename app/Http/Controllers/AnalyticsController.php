<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Firebase\JWT\JWT;

class AnalyticsController extends Controller
{
    public function index() : View
    {   
        $todayCash = DB::table('laravel.orders')
    ->whereDate('created_at', now()->toDateString())
    ->where('payment_method', 'cash')
    ->sum('total_amount');

    $todayDigital = DB::table('laravel.orders')
    ->whereDate('created_at', now()->toDateString())
    ->where('payment_method', 'digital')
    ->sum('total_amount');

    $dailyCount = DB::table('laravel.orders')
    ->whereDate('created_at', now()->toDateString())
    ->count();

    $totalToday = $todayCash + $todayDigital;

    $salesData = (object) [
        'todayCash'    => $todayCash,
        'todayDigital' => $todayDigital,
        'daily_total'  => $totalToday,
        'daily_count'  => $dailyCount,

        'cash_pct' => $totalToday > 0 ? ($todayCash / $totalToday) * 100 : 0,
        'digital_pct' => $totalToday > 0 ? ($todayDigital / $totalToday) * 100 : 0,
    ];

    $fastestMovers = DB::table('laravel.order_items')
            ->join('laravel.orders', 'order_items.order_id', '=', 'orders.id')
            ->join('laravel.menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select('menu_items.name', 'menu_items.img_url', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->whereDate('orders.created_at', now()->toDateString())
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.img_url')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

    $activeMenuCount = DB::table('laravel.menu_items')
            ->where('is_available', true)
            ->count();

        $lowStockCount = DB::table('laravel.ingredients')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->count();

        $lowStockItems = DB::table('laravel.ingredients as ingredient')
            ->join('laravel.units as p_unit', 'ingredient.primary_unit_id', '=', 'p_unit.id')
            ->select('ingredient.*', 'p_unit.abbreviation as primary_unit_abbr')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->limit(7)
            ->get();

        $totalMoneyIn = DB::table('laravel.orders')
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        // 2. All-time Total Money Out (System Cash Expenses)
        $totalMoneyOut = DB::table('laravel.expenses')
            ->where('fund_source', 'cash_in_hand')
            ->sum('total_amount');

        // 3. Current Cash in Hand Balance
        $currentCashBalance = $totalMoneyIn - $totalMoneyOut;

        $metabaseSecretKey = env('METABASE_SECRET_KEY');
        $metabaseSiteUrl = env('METABASE_SITE_URL', 'http://localhost:3000');

        $dashboardId = 2;

        $payload = array(
            "resource" => ["dashboard" => $dashboardId],
            "params" => (object) array(),
            "iat" => time(),
            "exp" => time() + 60*60
        );

        $token = JWT::encode($payload, $metabaseSecretKey, 'HS256');

        return view('admin.analytics.analytics', compact(
            'token', 
            'metabaseSiteUrl',
            'salesData',
            'fastestMovers',
            'activeMenuCount',
            'lowStockCount',
            'lowStockItems',
            'currentCashBalance',
        ));
    }
}
