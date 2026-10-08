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
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $todayDigital = DB::table('laravel.orders')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('payment_method', '!=', 'cash')
            ->sum('total_amount');

        $dailyCount = DB::table('laravel.orders')
            ->where('payment_status', 'paid')
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
            ->select(
                'menu_items.name',
                'menu_items.img_url',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw("
                    SUM(
                        CASE
                            WHEN orders.subtotal_amount > 0
                            THEN order_items.subtotal
                                - (
                                    order_items.subtotal
                                    / orders.subtotal_amount
                                    * orders.discount_amount
                                )
                            ELSE order_items.subtotal
                        END
                    ) as total_revenue
                ")
            )
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startOfDay, $endOfDay])
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.img_url')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $activeMenuCount = DB::table('laravel.branch_menu_items')
            ->where('is_available', true)
            ->distinct('menu_item_id')
            ->count('menu_item_id');

        $lowStockQuery = DB::table('laravel.branch_inventory as bi')
            ->join(
                'laravel.ingredients as i',
                'bi.ingredient_id',
                '=',
                'i.id'
            )
            ->join(
                'laravel.branches as b',
                'bi.branch_id',
                '=',
                'b.id'
            )
            ->leftJoin(
                'laravel.units as u',
                'i.primary_unit_id',
                '=',
                'u.id'
            )
            ->whereNull('bi.deleted_at')
            ->whereNull('i.deleted_at')
            ->whereColumn(
                'bi.stock_quantity',
                '<=',
                'bi.alert_threshold'
            );

        $lowStockCount = (clone $lowStockQuery)->count();

        $lowStockItems = $lowStockQuery
            ->select(
                'i.id',
                'i.name',
                'bi.stock_quantity',
                'bi.purchase_price',
                'u.abbreviation as primary_unit_abbr',
                'b.name as branch_name'
            )
            ->orderBy('bi.stock_quantity')
            ->limit(7)
            ->get();

        $totalMoneyIn = DB::table('laravel.orders')
            ->where('payment_method', 'cash')
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalMoneyOut = DB::table('laravel.cash_transactions')
                ->where('transaction_type', 'expense')
                ->where('fund_source', 'cash_in_hand')
                ->sum('amount');

        $currentCashBalance = $totalMoneyIn - $totalMoneyOut;

        $metabaseSecretKey = config('services.metabase.secret_key');
        $metabaseSiteUrl = rtrim((string) config('services.metabase.site_url', ''), '/');
        $metabaseConfigured = is_string($metabaseSecretKey)
            && $metabaseSecretKey !== ''
            && $metabaseSiteUrl !== '';
        $token = null;

        if ($metabaseConfigured) {
            $token = JWT::encode([
                'resource' => ['dashboard' => 2],
                'params' => (object)[],
                'iat' => time(),
                'exp' => time() + (60 * 60),
            ], $metabaseSecretKey, 'HS256');
        }

        return view('admin.analytics.analytics', compact(
            'token', 
            'metabaseSiteUrl',
            'metabaseConfigured',
            'salesData',
            'fastestMovers',
            'activeMenuCount',
            'lowStockCount',
            'lowStockItems',
            'currentCashBalance'
        ));
    }
}