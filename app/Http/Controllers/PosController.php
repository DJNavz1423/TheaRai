<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller{
    private function getActiveBranchId() {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return session('active_pos_branch_id');
        }
        return $user->branch_id;
    }

    public function index() {
        $user = auth()->user();
        $activeBranchId = $this->getActiveBranchId();

        if ($user->role === 'admin' && !$activeBranchId) {
            return redirect('/admin/pos/select-branch');
        }

        $layout = $user->role === 'admin' ? 'layouts.admin' : 'layouts.cashier';
        $activeBranch = DB::table('laravel.branches')->where('id', $activeBranchId)->first();
        $categories = DB::table('laravel.menu_categories')->get();
        
        $menuItems = DB::table('laravel.branch_menu_items as bmi')
            ->join('laravel.menu_items as mi', 'bmi.menu_item_id', '=', 'mi.id')
            ->where('bmi.branch_id', $activeBranchId)
            ->select(
                'mi.id',
                'mi.name',
                'mi.category_id',
                'mi.img_url',
                'mi.created_at',
                'bmi.is_available',
                DB::raw('COALESCE(bmi.branch_price, mi.final_price) as final_price')
            )
            ->where(DB::raw('COALESCE(bmi.branch_price, mi.final_price)'), '>', 0)
            ->get();

        return view('pos.pos', compact('categories', 'menuItems', 'layout', 'activeBranch'));
    }

    public function processOrder(Request $request){
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|integer|exists:pgsql.laravel.menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'cash_tendered' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,digital',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $activeBranchId = $this->getActiveBranchId();

        try{
            DB::beginTransaction();

            $receiptNo = 'REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $changeAmount = $request->cash_tendered - $request->total_amount;

            // 1. Insert Order WITH the branch_id
            $orderId = DB::table('laravel.orders')->insertGetId([
                'branch_id' => $activeBranchId,
                'receipt_no' => $receiptNo,
                'total_amount' => $request->total_amount,
                'cash_tendered' => $request->cash_tendered,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'created_at' => now(),
            ]);

            // 2. Process Cart Items
            foreach ($request->cart as $cartItem){
                DB::table('laravel.order_items')->insert([
                    'order_id' => $orderId,
                    'menu_item_id' => $cartItem['id'],
                    'quantity' => $cartItem['quantity'],
                    'price_at_time' => $cartItem['price'],
                    'subtotal' => $cartItem['price'] * $cartItem['quantity'],
                    'created_at' => now(),
                ]);

                // 3. Fetch Recipe
                $recipeItems = DB::table('laravel.menu_item_ingredient as pivot')
                    ->join('laravel.ingredients as ing', 'pivot.ingredient_id', '=', 'ing.id')
                    ->where('pivot.menu_item_id', $cartItem['id'])
                    ->select('ing.id', 'pivot.quantity_used', 'ing.conversion_factor')
                    ->get();

                // 4. Deduct Inventory from the SPECIFIC BRANCH
                foreach ($recipeItems as $ingredient){
                    $totalSUnitsUsed = $ingredient->quantity_used * $cartItem['quantity'];
                    $primaryUnitsUsed = $totalSUnitsUsed / $ingredient->conversion_factor;

                    DB::table('laravel.branch_inventory')
                        ->where('ingredient_id', $ingredient->id)
                        ->where('branch_id', $activeBranchId)
                        ->decrement('stock_quantity', $primaryUnitsUsed);
                }
            }

            $this->logActivity('created', 'order', $orderId, "{$receiptNo} processed at Branch {$activeBranchId} for ₱" . number_format($request->total_amount, 2));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully',
                'order_id' => $orderId,
                'receipt' => $receiptNo,
                'change' => $changeAmount,
            ]);
        } catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleAvailability(Request $request, $id){
        $activeBranchId = $this->getActiveBranchId();

        $pivot = DB::table('laravel.branch_menu_items')
            ->where('menu_item_id', $id)
            ->where('branch_id', $activeBranchId)
            ->first();

        if(!$pivot){
            return response()->json(['success' => false, 'message' => 'Menu item not assigned to this branch'], 404);
        }

        $newStatus = !$pivot->is_available;

        DB::table('laravel.branch_menu_items')
            ->where('id', $pivot->id)
            ->update(['is_available' => $newStatus]);

        $statusText = $newStatus ? 'Available' : 'Unavailable';
        $this->logActivity('updated', 'menu_item', $id, "Cashier marked item ID {$id} as {$statusText} at Branch {$activeBranchId}");

        return response()->json([
            'success' => true, 
            'is_available' => $newStatus,
            'message' => "Menu item marked as {$statusText}"
        ]);
    }

    public function printReceipt($id){
        $order = DB::table('laravel.orders')
            ->join('laravel.branches', 'orders.branch_id', '=', 'branches.id')
            ->select('orders.*', 'branches.name as branch_name', 'branches.address as branch_address')
            ->where('orders.id', $id)
            ->first();

        if(!$order) abort(404, 'Order not found');

        $items = DB::table('laravel.order_items')
            ->join('laravel.menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('order_id', $id)
            ->select('menu_items.name', 'order_items.quantity', 'order_items.subtotal')
            ->get();

        $pdf = Pdf::loadView('pos.receipt', compact('order', 'items'));
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');
        return $pdf->stream("receipt-{$order->receipt_no}.pdf");
    }

    // Shows the branch selection screen for Admins
    public function selectBranch()
    {
        $branches = DB::table('laravel.branches')->get();
        return view('admin.pos.select-branch', compact('branches'));
    }

    // Saves the selected branch to the session and redirects to the POS
    public function setBranch($id)
    {
        session(['active_pos_branch_id' => $id]);
        
        // Redirect back to the POS dashboard
        return redirect('/cashier/pos'); 
    }
}