<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Firebase\JWT\JWT;

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

        // 2. All-time Total Money Out (System Cash Expenses)
        $totalMoneyOut = DB::table('laravel.expenses')
            ->where('fund_source', 'cash_in_hand')
            ->sum('total_amount');

        // 3. Current Cash in Hand Balance
        $currentCashBalance = $totalMoneyIn - $totalMoneyOut;

        $recentTransactions = DB::table('laravel.orders')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        $stockLogs = DB::table('laravel.stock_logs as log')
            ->join('laravel.ingredients as ingredient', 'log.ingredient_id', '=', 'ingredient.id')
            ->join('laravel.units as p_unit', 'ingredient.primary_unit_id', '=', 'p_unit.id')
            ->select('log.created_at', 
                DB::raw("'updated' as action"),
                DB::raw("'inventory_stock' as model_type"),
                DB::raw("CONCAT(CASE WHEN log.quantity_change > 0 THEN '+' ELSE '' END, 
                CAST(log.quantity_change AS FLOAT), ' ', p_unit.abbreviation, ' ', ingredient.name,' (', COALESCE(log.remarks, log.type), ')') as description"))
            ->whereDate('log.created_at', now()->toDateString())
            ->orderBy('log.created_at', 'desc')
            ->limit(7)
            ->get();

        $activityLogs = DB::table('laravel.activity_logs')
            ->select('created_at', 'action', 'model_type', 'description')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        $recentActivities = $stockLogs->concat($activityLogs)
            ->sortByDesc('created_at')
            ->take(7);

        $metabaseSecretKey = env('METABASE_SECRET_KEY');

        $dailyPayload = [
            'resource' => ['question' => 44],
            'params' => (object)[],
            'iat' => time(),
            'exp' => time() + (60 * 15)
        ];

        $dailyToken = JWT::encode($dailyPayload, $metabaseSecretKey, 'HS256');

        $monthlyPayload = [
            'resource' => ['question' => 43], 
            'params' => (object)[],
            'iat' => time(),
            'exp' => time() + (60 * 15)
        ];

        $monthlyToken = JWT::encode($monthlyPayload, $metabaseSecretKey, 'HS256');

        return view('admin.dashboard', compact(
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
            'monthlyToken'
        ));
    }
}
