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

        $pendingOrdersQuery = DB::table('laravel.orders')
            ->leftJoin(
                'laravel.tables',
                'orders.table_id',
                '=',
                'tables.id'
            )
            ->select(
                'orders.id',
                'orders.receipt_no',
                'orders.order_source',
                'orders.payment_method',
                'orders.created_at',
                'tables.table_number'
            )
            ->where('orders.branch_id', $branchId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', 'pending');

        $pendingOrders = $pendingOrdersQuery
            ->orderBy('orders.created_at', 'asc')
            ->get();

        $orderItems = $pendingOrders->isEmpty()
            ? collect()
            : DB::table('laravel.order_items')
                ->join(
                    'laravel.menu_items',
                    'order_items.menu_item_id',
                    '=',
                    'menu_items.id'
                )
                ->whereIn('order_items.order_id', $pendingOrders->pluck('id'))
                ->select(
                    'order_items.order_id',
                    'order_items.quantity',
                    'menu_items.name',
                    'menu_items.img_url'
                )
                ->get()
                ->groupBy('order_id');

        foreach ($pendingOrders as $order) {
            $order->items = $orderItems->get($order->id, collect());
        }

        $pendingCount = $pendingOrders->count();
        $posCount = $pendingOrders->where('order_source', 'pos')->count();
        $qrCount = $pendingOrders->where('order_source', 'qr')->count();

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

        $orderCounts = DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(*) FILTER (WHERE order_source = 'pos') as pos_count")
            ->selectRaw("COUNT(*) FILTER (WHERE order_source = 'qr') as qr_count")
            ->first();

        return response()->json([
            'count' => (int) $orderCounts->total,
            'pos_count' => (int) $orderCounts->pos_count,
            'qr_count' => (int) $orderCounts->qr_count,
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