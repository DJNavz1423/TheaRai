<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WaiterController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $branchId = $user->branch_id;

        $posExpireBefore = now('Asia/Manila')
            ->subMinutes(25)
            ->utc();

        DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('order_source', 'pos')
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->where('created_at', '<', $posExpireBefore)
            ->update([
                'status' => 'served',
                'updated_at' => now(),
            ]);

        $pendingOrdersQuery = DB::table('laravel.orders')
            ->leftJoin(
                'laravel.tables',
                'orders.table_id',
                '=',
                'tables.id'
            )
            ->select(
                'orders.*',
                'tables.table_number'
            )
            ->where('orders.branch_id', $branchId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', 'pending');

        $pendingOrders = $pendingOrdersQuery
            ->orderBy('orders.created_at', 'asc')
            ->get();

        foreach ($pendingOrders as $order) {
            $order->items = DB::table('laravel.order_items')
                ->join(
                    'laravel.menu_items',
                    'order_items.menu_item_id',
                    '=',
                    'menu_items.id'
                )
                ->where('order_items.order_id', $order->id)
                ->select(
                    'order_items.quantity',
                    'menu_items.name',
                    'menu_items.img_url'
                )
                ->get();
        }

        $pendingCount = $pendingOrders->count();

        $startOfDay = now('Asia/Manila')->startOfDay()->utc();
        $endOfDay = now('Asia/Manila')->endOfDay()->utc();

        $todayTransactions = DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->select(
                'id',
                'receipt_no',
                'total_amount',
                'payment_method',
                'payment_status',
                'status',
                'order_source',
                'created_at'
            )
            ->orderByDesc('created_at')
            ->get();

        $posCount = DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->where('order_source', 'pos')
            ->count();

        $qrCount = DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->where('order_source', 'qr')
            ->count();

        return view('waiter.dashboard', compact(
            'user',
            'pendingCount',
            'posCount',
            'qrCount',
            'pendingOrders',
            'todayTransactions'
        ));
    }

    public function getNotifications() {
        $user = auth()->user();
        $branchId = $user->branch_id;

        $posExpireBefore = now('Asia/Manila')
            ->subMinutes(30)
            ->utc();

        /*
        |--------------------------------------------------------------------------
        | Automatically serve expired POS orders
        |--------------------------------------------------------------------------
        */

        DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('order_source', 'pos')
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->where('created_at', '<', $posExpireBefore)
            ->update([
                'status' => 'served',
                'updated_at' => now(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Pending waiter orders
        |--------------------------------------------------------------------------
        */

        $ordersQuery = DB::table('laravel.orders')
            ->leftJoin(
                'laravel.tables',
                'orders.table_id',
                '=',
                'tables.id'
            )
            ->where('orders.branch_id', $branchId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', 'pending');

        $count = (clone $ordersQuery)->count();

        $latestOrder = $ordersQuery
            ->select(
                'orders.id',
                'orders.receipt_no',
                'orders.order_source',
                'orders.created_at',
                'tables.table_number'
            )
            ->orderByDesc('orders.created_at')
            ->first();

        $posCount = (clone $ordersQuery)
            ->where('orders.order_source', 'pos')
            ->count();

        $qrCount = (clone $ordersQuery)
            ->where('orders.order_source', 'qr')
            ->count();

        return response()->json([
            'count' => $count,
            'pos_count' => $posCount,
            'qr_count' => $qrCount,
            'latest_order' => $latestOrder,
        ]);
    }

    public function serve($id) {
        $user = auth()->user();

        $updated = DB::table('laravel.orders')
            ->where('id', $id)
            ->where('branch_id', $user->branch_id)
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->update([
                'status' => 'served',
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return back()->with('error', 'Order not found.');
        }

        return back()->with('success', 'Order marked as served!');
    }
}