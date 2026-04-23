<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Firebase\JWT\JWT;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index() : View
    {   
        $manilaNow = now()->timezone('Asia/Manila');
        $startOfDay = $manilaNow->copy()->startOfDay()->utc();
        $endOfDay = $manilaNow->copy()->endOfDay()->utc();

        $todayCash = DB::table('laravel.orders')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $todayDigital = DB::table('laravel.orders')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('payment_method', '!=', 'cash')
            ->sum('total_amount');

        $dailyCount = DB::table('laravel.orders')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        $totalToday = $todayCash + $todayDigital;

        $salesData = (object) [
            'todayCash'    => $todayCash,
            'todayDigital' => $todayDigital,
            'daily_total'  => $totalToday,
            'daily_count'  => $dailyCount,
            'cash_pct'     => $totalToday > 0 ? ($todayCash / $totalToday) * 100 : 0,
            'digital_pct'  => $totalToday > 0 ? ($todayDigital / $totalToday) * 100 : 0,
        ];

        $fastestMovers = DB::table('laravel.order_items')
            ->join('laravel.orders', 'order_items.order_id', '=', 'orders.id')
            ->join('laravel.menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select('menu_items.name', 'menu_items.img_url', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->whereBetween('orders.created_at', [$startOfDay, $endOfDay])
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.img_url')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $activeMenuCount = DB::table('laravel.branch_menu_items')
            ->where('is_available', true)
            ->distinct('menu_item_id')
            ->count('menu_item_id');

        $lowStockCount = DB::table('laravel.admin_global_inventory')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->count();

        $lowStockItems = DB::table('laravel.admin_global_inventory')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->limit(7)
            ->get();

        $totalMoneyIn = DB::table('laravel.orders')
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $totalMoneyOut = DB::table('laravel.expenses')
            ->where('fund_source', 'cash_in_hand')
            ->sum('total_amount');

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
            'currentCashBalance'
        ));
    }
}