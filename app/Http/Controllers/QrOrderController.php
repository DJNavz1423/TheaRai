<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QrOrderController extends Controller
{
    private function getActiveBranchId() {
        $user = auth()->user();
        if (in_array($user->role, ['admin', 'dev', 'owner'])) {
            return session('active_pos_branch_id');
        }
        return $user->branch_id;
    }

    public function index()
    {
        $user = auth()->user();
        $branchId = $this->getActiveBranchId();

        if (in_array($user->role, ['admin', 'dev', 'owner']) && !$branchId) {
            return redirect('/admin/pos/select-branch');
        }

        $activeBranch = DB::table('laravel.branches')
            ->where('id', $branchId)
            ->first();

        $qrOrders = DB::table('laravel.orders')
            ->join('laravel.tables', 'orders.table_id', '=', 'tables.id')
            ->select('orders.*', 'tables.table_number')
            ->where('orders.branch_id', $branchId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', 'pending')
            ->where('orders.order_source', 'qr')
            ->orderBy('orders.created_at', 'asc')
            ->get();
        
        $philippineNow = Carbon::now('Asia/Manila');

        $todayStart = $philippineNow
            ->copy()
            ->startOfDay()
            ->utc();

        $todayEnd = $philippineNow
            ->copy()
            ->endOfDay()
            ->utc();

        $todayQrTransactions = DB::table('laravel.orders as orders')
            ->leftJoin(
                'laravel.branches as branches',
                'orders.branch_id',
                '=',
                'branches.id'
            )
            ->select(
                'orders.id',
                'orders.receipt_no',
                'orders.total_amount',
                'orders.payment_method',
                'orders.payment_status',
                'orders.status',
                'orders.created_at',
                'orders.branch_id',
                'branches.name as branch_name'
            )
            ->where('orders.branch_id', $branchId)
            ->where('orders.order_source', 'qr')
            ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
            ->orderByDesc('orders.created_at')
            ->get();

        foreach ($qrOrders as $order) {
            $order->items = DB::table('laravel.order_items')
                ->join('laravel.menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.quantity', 'menu_items.name', 'menu_items.img_url')
                ->get();
        }

        return view('pos.qr_orders', compact('qrOrders', 'activeBranch', 'todayQrTransactions'));
    }

    public function serve($id)
    {
        DB::table('laravel.orders')
            ->where('id', $id)
            ->update([
                'status' => 'served',
                'updated_at' => now()
            ]);

        return back()->with('success', 'Order marked as served!');
    }

    public function getNotifications(){
        $user = auth()->user();
        $branchId = $this->getActiveBranchId();

        /*
        * QR ORDER NOTIFICATIONS
        *
        * QR orders remain branch-based.
        * Admin/dev/owner need a selected branch to receive QR order alerts.
        */
        $count = 0;
        $latestOrder = null;

        if ($branchId) {

            $baseQuery = DB::table('laravel.orders')
                ->where('orders.branch_id', $branchId)
                ->where('orders.payment_status', 'paid')
                ->where('orders.status', 'pending')
                ->where('orders.order_source', 'qr');

            $count = (clone $baseQuery)->count();

            $latestOrder = $baseQuery
                ->leftJoin(
                    'laravel.tables',
                    'orders.table_id',
                    '=',
                    'tables.id'
                )
                ->select(
                    'orders.id',
                    'orders.receipt_no',
                    'orders.created_at',
                    'tables.table_number'
                )
                ->orderByDesc('orders.created_at')
                ->first();
        }


        /*
        * INVENTORY NOTIFICATIONS
        *
        * Admin/dev/owner = ALL branches.
        * Staff = assigned branch only.
        */
        $inventoryQuery = DB::table('laravel.branch_inventory as bi')
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

        $isAdminLevel = in_array(
            $user->role,
            ['admin', 'dev', 'owner']
        );

        if (!$isAdminLevel) {
            $inventoryQuery->where(
                'bi.branch_id',
                $user->branch_id
            );
        }

        $warnings = $inventoryQuery
            ->select(
                'i.id',
                'i.name',
                'bi.branch_id',
                'b.name as branch_name',
                'bi.stock_quantity',
                'bi.alert_threshold',
                'u.abbreviation as unit'
            )
            ->orderBy('bi.stock_quantity')
            ->get();

        $outOfStock = $warnings
            ->filter(function ($item) {
                return $item->stock_quantity <= 0;
            })
            ->values();

        $lowStock = $warnings
            ->filter(function ($item) {
                return $item->stock_quantity > 0
                    && $item->stock_quantity <= $item->alert_threshold;
            })
            ->values();


        return response()->json([
            'branch_id' => $branchId,

            'qr_orders' => [
                'count' => $count,
                'latest_order' => $latestOrder,
            ],

            'inventory_scope' => $isAdminLevel
                ? 'all'
                : 'branch:' . $user->branch_id,

            'inventory' => [
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStock,
            ],
        ]);
    }
}