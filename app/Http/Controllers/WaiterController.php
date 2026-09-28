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
            'pendingOrders'
        ));
    }

    public function serve($id)
    {
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