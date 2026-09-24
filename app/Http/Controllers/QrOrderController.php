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
            ->where('orders.payment_method', '!=', 'cash')
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
            ->where('orders.payment_method', '!=', 'cash')
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

    public function getNotifications() {
        $branchId = $this->getActiveBranchId();

        if (!$branchId) {
            return response()->json(['count' => 0, 'branch_id' => null, 'latest_order' => null,]);
        }
        
        $baseQuery = DB::table('laravel.orders')
                ->where('orders.branch_id', $branchId)
                ->where('orders.payment_status', 'paid')
                ->where('orders.status', 'pending')
                ->where('orders.payment_method', '!=', 'cash');

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

        return response()->json(['count' => $count, 'branch_id' => $branchId, 'latest_order' => $latestOrder,]);
    }
}