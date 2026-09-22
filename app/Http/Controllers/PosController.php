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

    private function hasInsufficientIngredients(int $menuItemId, int $branchId, int $dishQuantity = 1): bool {

    $recipeItems = DB::table('laravel.menu_item_ingredient as pivot')
        ->join(
            'laravel.ingredients as ing',
            'pivot.ingredient_id',
            '=',
            'ing.id'
        )
        ->select(
            'pivot.ingredient_id',
            'pivot.quantity_used',
            'pivot.unit_id',
            'ing.primary_unit_id',
            'ing.secondary_unit_id',
            'ing.conversion_factor'
        )
        ->where('pivot.menu_item_id', $menuItemId)
        ->get();

    // No recipe means there are no tracked ingredients to check.
    if ($recipeItems->isEmpty()) {
        return false;
    }

    $ingredientIds = $recipeItems
        ->pluck('ingredient_id')
        ->all();

    $stockByIngredient = DB::table('laravel.branch_inventory')
        ->where('branch_id', $branchId)
        ->whereIn('ingredient_id', $ingredientIds)
        ->pluck('stock_quantity', 'ingredient_id');

    foreach ($recipeItems as $ingredient) {

        $requiredAmount =
            $ingredient->quantity_used * $dishQuantity;

        if (
            $ingredient->unit_id ==
            $ingredient->primary_unit_id
        ) {

            $requiredPrimaryUnits =
                $requiredAmount;

        } elseif (
            $ingredient->unit_id ==
            $ingredient->secondary_unit_id
        ) {

            if (
                !$ingredient->conversion_factor ||
                $ingredient->conversion_factor <= 0
            ) {
                throw new \Exception(
                    "Invalid conversion factor for ingredient {$ingredient->ingredient_id}"
                );
            }

            $requiredPrimaryUnits =
                $requiredAmount /
                $ingredient->conversion_factor;

        } else {

            throw new \Exception(
                "Invalid recipe unit for ingredient {$ingredient->ingredient_id}"
            );
        }

        $availableStock =
            (float) $stockByIngredient->get(
                $ingredient->ingredient_id,
                0
            );

        if ($availableStock < $requiredPrimaryUnits) {
            return true;
        }
    }

    return false;
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
                'bmi.disabled_reason',
                DB::raw('COALESCE(bmi.branch_price, mi.final_price) as final_price')
            )
            ->where(DB::raw('COALESCE(bmi.branch_price, mi.final_price)'), '>', 0)
            ->get();
        
        $menuItems->transform(function ($item) use ($activeBranchId) {
            $item->out_of_stock =
                $this->hasInsufficientIngredients(
                    (int) $item->id,
                    (int) $activeBranchId
                );

            /*
            * Automatically disable an AVAILABLE item when
            * one or more required ingredients are insufficient.
            */
            if ($item->out_of_stock && ($item->is_available === true || $item->is_available == 1)) {

                DB::table('laravel.branch_menu_items')
                    ->where('id', $item->id)
                    ->where('branch_id', $activeBranchId)
                    ->update([
                        'is_available' => false,
                        'disabled_reason' => 'out_of_stock',
                    ]);

                $item->is_available = false;
                $item->disabled_reason = 'out_of_stock';
            }

            return $item;
        });

        return view('pos.pos', compact('categories', 'menuItems', 'layout', 'activeBranch'));
    }

    public function processOrder(Request $request, ?int $branchId = null){
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|integer|exists:pgsql.laravel.menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,amount',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'cash_tendered' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,digital',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $activeBranchId = $branchId ?? $this->getActiveBranchId();

        try{
            DB::beginTransaction();

            foreach ($request->cart as $cartItem) {

    if (
        $this->hasInsufficientIngredients(
            (int) $cartItem['id'],
            $activeBranchId,
            (int) $cartItem['quantity']
        )
    ) {

        $itemName = DB::table('laravel.menu_items')
            ->where('id', $cartItem['id'])
            ->value('name');

        throw new \Exception(
            "Insufficient ingredients for {$itemName}."
        );
    }
}

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

            $subtotalAmount = collect($request->cart)
                ->sum(function ($item) {
                    return $item['price'] * $item['quantity'];
                });

            $discountType = $request->input('discount_type');
            $discountValue = (float) ($request->input('discount_value') ?? 0);
            $discountAmount = 0;

            if ($discountType === 'percentage') {
                $discountValue = min($discountValue, 100);

                $discountAmount = round($subtotalAmount * ($discountValue / 100), 2);
            } elseif ($discountType === 'amount') {

                $discountAmount = min(max($discountValue, 0), $subtotalAmount);
            } else {
                $discountType = null;
                $discountValue = 0;
            }

            $totalAmount = round($subtotalAmount - $discountAmount, 2);

            $changeAmount = $request->cash_tendered - $totalAmount;

            // 1. Insert Order WITH the branch_id
            $orderId = DB::table('laravel.orders')->insertGetId([
                'branch_id' => $activeBranchId,
                'receipt_no' => $receiptNo,
                'subtotal_amount' => $subtotalAmount,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
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

            $this->logActivity('created', 'order', $orderId, "{$receiptNo} processed at Branch {$activeBranchId} for ₱" . number_format($totalAmount, 2));

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

        /*
        * Currently available -> DISABLE
        */
        if ($pivot->is_available) {

            $reason = $request->input('reason');
            $reasonOther = trim($request->input('reason_other', ''));

            if (!in_array($reason, ['sold_out', 'out_of_stock', 'others'], true)) {
                return response()->json(['success' => false, 'message' => 'Please select a reason for disabling this item.'], 422);
            }

            if ($reason === 'others' && $reasonOther === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please specify the reason.'
                ], 422);
            }

            /*
            * "Others" is stored as the default "disabled" type.
            */
            $disabledReason = match ($reason) {
                'sold_out' => 'sold_out',
                'out_of_stock' => 'out_of_stock',
                'others' => 'disabled',
            };

            /*
            * Keep the custom "Others" text only in the activity log.
            */
            $reasonText = match ($reason) {
                'sold_out' => 'Sold Out',
                'out_of_stock' => 'Out of Stock',
                'others' => $reasonOther,
            };

            DB::table('laravel.branch_menu_items')
                ->where('id', $pivot->id)
                ->update([
                    'is_available' => false,
                    'disabled_reason' => $disabledReason,
                ]);

            $this->logActivity('updated', 'menu_item', $id, "Cashier disabled item ID {$id} at Branch {$activeBranchId}. Reason: {$reasonText}");

            return response()->json(['success' => true, 'is_available' => false, 'disabled_reason' => $disabledReason, 'message' => "Menu item disabled. Reason: {$reasonText}"]);
        }

        /*
        * Currently disabled -> ENABLE
        */

        if ($this->hasInsufficientIngredients((int) $id, (int) $activeBranchId)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot enable this item because one or more required ingredients are out of stock or insufficient.'
            ], 422);
        }

        DB::table('laravel.branch_menu_items')
            ->where('id', $pivot->id)
            ->update([
                'is_available' => true,
                'disabled_reason' => null,
            ]);

        $this->logActivity('updated', 'menu_item', $id, "Cashier enabled item ID {$id} at Branch {$activeBranchId}");

        return response()->json([
            'success' => true,
            'is_available' => true,
            'disabled_reason' => null,
            'message' => 'Menu item enabled.'
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