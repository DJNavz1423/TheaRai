<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;

class PosController extends Controller{
    private function getActiveBranchId() {
        $user = auth()->user();
        if(in_array($user->role, ['admin', 'dev', 'owner'])) {
            return session('active_pos_branch_id');
        }
        return $user->branch_id;
    }

    public function index() {
        $user = auth()->user();
        $activeBranchId = $this->getActiveBranchId();

        if (in_array($user->role, ['admin', 'dev', 'owner']) && !$activeBranchId) {
            return redirect('/admin/pos/select-branch');
        }

        $layout = in_array($user->role, ['admin', 'dev', 'owner']) ? 'layouts.admin' : 'layouts.cashier';

        $activeBranch = DB::table('laravel.branches')
            ->where('id', $activeBranchId)
            ->first();

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

    public function processOrder(Request $request, ?int $branchId = null){
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

        $activeBranchId = $branchId ?? $this->getActiveBranchId();

        try{
            DB::beginTransaction();

            $receiptNo = $request->input('receipt_no')
                ?: 'REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $existingOrder = DB::table('laravel.orders')
                ->where('receipt_no', $receiptNo)
                ->first();

            if ($existingOrder) {
                DB::rollBack();

                return response()->json([
                    'success' => true,
                    'message' => 'Order was already synchronized.',
                    'order_id' => $existingOrder->id,
                    'receipt' => $receiptNo,
                ]);
            }

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
                    ->select('ing.id', 
                             'pivot.quantity_used',
                             'pivot.unit_id',
                             'ing.primary_unit_id',
                             'ing.secondary_unit_id', 
                             'ing.conversion_factor')
                    ->where('pivot.menu_item_id', $cartItem['id'])
                    ->get();

                // 4. Deduct Inventory from the SPECIFIC BRANCH
                foreach ($recipeItems as $ingredient){
                    $totalUsed = $ingredient->quantity_used * $cartItem['quantity'];

                    if ($ingredient->unit_id == $ingredient->primary_unit_id) {

                        // Recipe uses primary unit
                        $primaryUnitsUsed = $totalUsed;

                    } elseif ($ingredient->unit_id == $ingredient->secondary_unit_id) {

                        // Recipe uses secondary unit
                        $primaryUnitsUsed =
                            $totalUsed / $ingredient->conversion_factor;

                    } else {

                        throw new \Exception(
                            "Invalid recipe unit for ingredient {$ingredient->id}"
                        );
                    }

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


    
    //offline data
    public function offlineData(){
        $branches = DB::table('laravel.branches')
            ->orderBy('name')
            ->get();

        $categories = DB::table('laravel.menu_categories')
            ->get();

        $menuItems = DB::table('laravel.branch_menu_items as bmi')
            ->join(
                'laravel.menu_items as mi',
                'bmi.menu_item_id',
                '=',
                'mi.id'
            )
            ->select(
                'mi.id',
                'mi.name',
                'mi.category_id',
                'mi.img_url',
                'mi.created_at',
                'bmi.branch_id',
                'bmi.is_available',
                DB::raw(
                    'COALESCE(
                        bmi.branch_price,
                        mi.final_price
                    ) as final_price'
                )
            )
            ->where(
                DB::raw(
                    'COALESCE(
                        bmi.branch_price,
                        mi.final_price
                    )'
                ),
                '>',
                0
            )
            ->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
            'categories' => $categories,
            'menu_items' => $menuItems,
        ]);
    }

    public function syncOfflineOrders(Request $request)
    {
        $orders = $request->validate([
            'orders' => 'present|array',
            'orders.*.local_id' => 'required|string|max:100',
            'orders.*.sync_token' => 'required|string',
            'orders.*.branch_id' => 'required|integer',
            'orders.*.receipt_no' => 'required|string|max:100',
            'orders.*.total_amount' => 'required|numeric|min:0',
            'orders.*.cash_tendered' => 'required|numeric|min:0',
            'orders.*.payment_method' => 'required|string|in:cash,digital',
            'orders.*.reference_number' => 'nullable|string|max:255',
            'orders.*.items' => 'required|array|min:1',
            'orders.*.items.*.menu_item_id' => 'required|integer',
            'orders.*.items.*.quantity' => 'required|integer|min:1',
            'orders.*.items.*.price_at_time' => 'required|numeric|min:0',
            'orders.*.items.*.subtotal' => 'required|numeric|min:0',
        ])['orders'];

        if (!$orders) {
            return response()->json([
                'success' => true,
                'results' => [],
            ]);
        }

        $results = [];

        foreach ($orders as $order) {
            try {
                $claims = json_decode(
                    Crypt::decryptString($order['sync_token']),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );

                $user = \App\Models\User::findOrFail($claims['user_id']);

                if ($user->role !== $claims['role']) {
                    throw new \RuntimeException('Offline sync credentials are invalid.');
                }

                if (
                    $user->role === 'staff' &&
                    (int) $user->branch_id !== (int) $order['branch_id']
                ) {
                    throw new \RuntimeException('Offline order branch is not assigned to this user.');
                }

                Auth::login($user);

                $response = $this->processOrder(new Request([
                    'cart' => array_map(fn ($item) => [
                        'id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price_at_time'],
                    ], $order['items']),
                    'total_amount' => $order['total_amount'],
                    'receipt_no' => $order['receipt_no'],
                    'cash_tendered' => $order['cash_tendered'],
                    'payment_method' => $order['payment_method'],
                    'reference_number' => $order['reference_number'] ?? null,
                ]), (int) $order['branch_id']);

                $responseData = $response->getData(true);

                $results[] = [
                    'local_id' => $order['local_id'],
                    'success' => $response->getStatusCode() < 400,
                    'error' => $responseData['error'] ?? null,
                ];
            } catch (\Throwable $error) {
                $results[] = [
                    'local_id' => $order['local_id'],
                    'success' => false,
                    'error' => $error->getMessage(),
                ];
            }
        }

        return response()->json(['success' => true, 'results' => $results]);
    }
}