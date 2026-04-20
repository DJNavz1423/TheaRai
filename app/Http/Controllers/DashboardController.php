<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Firebase\JWT\JWT;

class DashboardController extends Controller
{
    public function index(): View{
        $totalInventoryValue = DB::table('laravel.branch_inventory')
            ->selectRaw('COALESCE(SUM(stock_quantity * purchase_price), 0) as total')
            ->value('total');

        $lowStockCount = DB::table('laravel.admin_global_inventory')
            ->whereRaw('stock_quantity <= alert_threshold')
            ->count();

        $lowStockItems = DB::table('laravel.admin_global_inventory')
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

        $expensesData = (object) [
            'monthly_total' => DB::table('laravel.expenses')
                ->where('fund_source', 'cash_in_hand')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
        ];

        $totalMoneyIn = DB::table('laravel.orders')
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $totalMoneyOut = DB::table('laravel.expenses')
            ->where('fund_source', 'cash_in_hand')
            ->sum('total_amount');

        $currentCashBalance = $totalMoneyIn - $totalMoneyOut;

        $recentTransactions = DB::table('laravel.orders')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $activityLogs = DB::table('laravel.activity_logs')
            ->select('created_at', 'action', 'model_type', 'description')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $recentActivities = $activityLogs;

        $metabaseSecretKey = env('METABASE_SECRET_KEY');
        $metabaseSiteUrl = env('METABASE_SITE_URL', 'http://localhost:3000');

        $dailyPayload = [
            'resource' => ['question' => 44],
            'params' => (object)[],
            'iat' => time(),
            'exp' => time() + (60 * 60)
        ];

        $dailyToken = JWT::encode($dailyPayload, $metabaseSecretKey, 'HS256');

        $monthlyPayload = [
            'resource' => ['question' => 43], 
            'params' => (object)[],
            'iat' => time(),
            'exp' => time() + (60 * 15)
        ];

        $monthlyToken = JWT::encode($monthlyPayload, $metabaseSecretKey, 'HS256');

        return view('admin.dashboard.dashboard', compact(
            'totalInventoryValue',
            'lowStockCount',
            'lowStockItems',
            'salesData',
            'expensesData',
            'totalMoneyIn',
            'totalMoneyOut',
            'currentCashBalance',
            'recentTransactions',
            'recentActivities',
            'dailyToken',
            'monthlyToken',
            'metabaseSiteUrl'
        ));
    }
}