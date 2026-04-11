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
        $salesData = (object) [
            'daily_total' => DB::table('laravel.orders')
                ->whereDate('created_at', now()->toDateString())
                ->sum('total_amount'),
            
            'daily_count' => DB::table('laravel.orders')
                ->whereDate('created_at', now()->toDateString())
                ->count(),
        ];

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
            'activeMenuCount',
            'lowStockCount',
            'lowStockItems'
        ));
    }
}
