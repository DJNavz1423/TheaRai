<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrOrderController extends Controller
{
    private function getActiveBranchId() {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return session('active_pos_branch_id');
        }
        return $user->branch_id;
    }

    public function index()
    {
        $user = auth()->user();
        $branchId = $this->getActiveBranchId();

        if ($user->role === 'admin' && !$branchId) {
            return redirect('/admin/pos/select-branch');
        }

        $activeBranch = DB::table('laravel.branches')->where('id', $branchId)->first();

        $qrOrders = DB::table('laravel.orders')
            ->join('laravel.tables', 'orders.table_id', '=', 'tables.id')
            ->select('orders.*', 'tables.table_number')
            ->where('orders.branch_id', $branchId)
            ->where('orders.payment_status', 'paid') 
            ->where('orders.status', 'pending')
            ->where('orders.payment_method', '!=', 'cash')
            ->orderBy('orders.created_at', 'asc')
            ->get();

        foreach ($qrOrders as $order) {
            $order->items = DB::table('laravel.order_items')
                ->join('laravel.menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('order_items.order_id', $order->id)
                ->select('order_items.quantity', 'menu_items.name')
                ->get();
        }

        return view('pos.qr_orders', compact('qrOrders', 'activeBranch'));
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

    public function getNotifications()
    {
        $branchId = $this->getActiveBranchId();

        if (!$branchId) {
            return response()->json(['count' => 0]);
        }

        $count = DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->where('status', 'pending')
            ->where('payment_method', '!=', 'cash')
            ->count();

        return response()->json(['count' => $count]);
    }
}