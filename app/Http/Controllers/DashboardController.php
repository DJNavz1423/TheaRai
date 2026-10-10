<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Firebase\JWT\JWT;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View{
        $manilaNow = now()->timezone('Asia/Manila');
        
        $startOfDay = $manilaNow->copy()->startOfDay()->utc();
        $endOfDay = $manilaNow->copy()->endOfDay()->utc();
        
        $startOfMonth = $manilaNow->copy()->startOfMonth()->utc();
        $endOfMonth = $manilaNow->copy()->endOfMonth()->utc();

        $totalInventoryValue = DB::table('laravel.branch_inventory')
            ->whereNull('deleted_at')
            ->selectRaw('COALESCE(SUM(stock_quantity * purchase_price), 0) as total')
            ->value('total');

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
        
        $orderTotals = DB::table('laravel.orders')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN payment_status = ? AND created_at BETWEEN ? AND ? THEN total_amount ELSE 0 END), 0) as monthly_total,
                COALESCE(SUM(CASE WHEN payment_status = ? AND created_at BETWEEN ? AND ? THEN total_amount ELSE 0 END), 0) as daily_total,
                COUNT(CASE WHEN payment_status = ? AND created_at BETWEEN ? AND ? THEN 1 END) as daily_count,
                COALESCE(SUM(CASE WHEN payment_method = ? AND payment_status = ? AND created_at BETWEEN ? AND ? THEN total_amount ELSE 0 END), 0) as today_cash_in,
                COALESCE(SUM(CASE WHEN payment_method = ? AND payment_status = ? THEN total_amount ELSE 0 END), 0) as total_money_in',
                [
                    'paid', $startOfMonth, $endOfMonth,
                    'paid', $startOfDay, $endOfDay,
                    'paid', $startOfDay, $endOfDay,
                    'cash', 'paid', $startOfDay, $endOfDay,
                    'cash', 'paid',
                ]
            )
            ->first();

        $salesData = (object) [
            'monthly_total' => $orderTotals->monthly_total,
            'daily_total' => $orderTotals->daily_total,
            'daily_count' => $orderTotals->daily_count,
        ];

        $cashTransactionTotals = DB::table('laravel.cash_transactions')
            ->where('transaction_type', 'expense')
            ->where('fund_source', 'cash_in_hand')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN amount ELSE 0 END), 0) as monthly_total,
                COALESCE(SUM(amount), 0) as total_money_out',
                [$startOfMonth, $endOfMonth]
            )
            ->first();

        $expensesData = (object) [
            'monthly_total' => $cashTransactionTotals->monthly_total,
        ];

        $todayCashIn = $orderTotals->today_cash_in;
        $totalMoneyIn = $orderTotals->total_money_in;
        $totalMoneyOut = $cashTransactionTotals->total_money_out;

        $currentCashBalance = $totalMoneyIn - $totalMoneyOut;

        $recentTransactions = DB::table('laravel.orders')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        $activityLogs = DB::table('laravel.activity_logs')
            ->select('created_at', 'action', 'model_type', 'description')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        $recentActivities = $activityLogs;

        $metabaseSecretKey = config('services.metabase.secret_key');
        $metabaseSiteUrl = rtrim((string) config('services.metabase.site_url', ''), '/');
        $metabaseConfigured = is_string($metabaseSecretKey)
            && $metabaseSecretKey !== ''
            && $metabaseSiteUrl !== '';
        $dailyToken = null;
        $monthlyToken = null;

        if ($metabaseConfigured) {
            $dailyToken = JWT::encode([
                'resource' => ['question' => 44],
                'params' => (object)[],
                'iat' => time(),
                'exp' => time() + (60 * 60),
            ], $metabaseSecretKey, 'HS256');

            $monthlyToken = JWT::encode([
                'resource' => ['question' => 43],
                'params' => (object)[],
                'iat' => time(),
                'exp' => time() + (15 * 60),
            ], $metabaseSecretKey, 'HS256');
        }

        return view('admin.dashboard.dashboard', compact(
            'totalInventoryValue',
            'lowStockCount',
            'lowStockItems',
            'salesData',
            'expensesData',
            'totalMoneyIn',
            'totalMoneyOut',
            'currentCashBalance',
            'todayCashIn',
            'recentTransactions',
            'recentActivities',
            'dailyToken',
            'monthlyToken',
            'metabaseSiteUrl',
            'metabaseConfigured'
        ));
    }
}