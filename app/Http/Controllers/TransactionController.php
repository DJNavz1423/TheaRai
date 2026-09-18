<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller {
    public function index()
    {
        $user = auth()->user();

        $isAdminLevel =
            in_array($user->role, ['admin', 'dev', 'owner']);

        /*
        |--------------------------------------------------------------------------
        | Branches
        |--------------------------------------------------------------------------
        */

        if ($isAdminLevel) {

            $branches = DB::table('laravel.branches')
                ->orderBy('name')
                ->get();

        } else {

            $branches = DB::table('laravel.branches')
                ->where('id', $user->branch_id)
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Transactions
        |--------------------------------------------------------------------------
        */

        $query = DB::table('laravel.orders as orders')
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
                'orders.cash_tendered',
                'orders.change_amount',
                'orders.payment_method',
                'orders.reference_number',
                'orders.payment_status',
                'orders.status',
                'orders.created_at',
                'orders.branch_id',
                'branches.name as branch_name'
            )
            ->orderByDesc('orders.created_at');

        /*
        |--------------------------------------------------------------------------
        | Staff can only see their assigned branch
        |--------------------------------------------------------------------------
        */

        if (!$isAdminLevel) {
            $query->where(
                'orders.branch_id',
                $user->branch_id
            );
        }

        $transactions = $query->get();


        /*
        |--------------------------------------------------------------------------
        | Payment methods for filter
        |--------------------------------------------------------------------------
        */

        $paymentMethods = $transactions
            ->pluck('payment_method')
            ->filter()
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Payment statuses for filter
        |--------------------------------------------------------------------------
        */

        $paymentStatuses = $transactions
            ->pluck('payment_status')
            ->filter()
            ->unique()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */

        $layout = $isAdminLevel
            ? 'layouts.admin'
            : 'layouts.cashier';


        return view(
            'admin.transaction.transaction',
            compact(
                'transactions',
                'branches',
                'paymentMethods',
                'paymentStatuses',
                'layout',
                'isAdminLevel'
            )
        );
    }

    public function show($id){
        $user = auth()->user();

        $isAdminLevel =
            in_array($user->role, ['admin', 'dev', 'owner']);

        $query = DB::table('laravel.orders as orders')
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
                'orders.cash_tendered',
                'orders.change_amount',
                'orders.payment_method',
                'orders.reference_number',
                'orders.payment_status',
                'orders.created_at',
                'orders.branch_id',
                'branches.name as branch_name'
            )
            ->where('orders.id', $id);

        if (!$isAdminLevel) {
            $query->where(
                'orders.branch_id',
                $user->branch_id
            );
        }

        $transaction = $query->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.'
            ], 404);
        }

        $items = DB::table('laravel.order_items as order_items')
            ->join(
                'laravel.menu_items as menu_items',
                'order_items.menu_item_id',
                '=',
                'menu_items.id'
            )
            ->where(
                'order_items.order_id',
                $transaction->id
            )
            ->select(
                'menu_items.name',
                'menu_items.img_url',
                'order_items.quantity',
                'order_items.price_at_time',
                'order_items.subtotal'
            )
            ->get();

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'items' => $items,
        ]);
    }

    public function refund(Request $request, $id){
        $user = auth()->user();

        $isAdminLevel =
            in_array($user->role, ['admin', 'dev', 'owner']);

        $validated = $request->validate([
            'refund_reason' => 'required|in:food_quality,wrong_order,customer_complaint,allergy_safety,other',
            'refund_condition' => 'required|in:resellable,wasted',
        ]);

        $query = DB::table('laravel.orders')
            ->where('id', $id);

        if (!$isAdminLevel) {
            $query->where(
                'branch_id',
                $user->branch_id
            );
        }

        $transaction = $query->first();

        if (!$transaction) {
            return back()->with(
                'error',
                'Transaction not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Already refunded
        |--------------------------------------------------------------------------
        */

        if ($transaction->payment_status === 'refunded') {
            return back()->with(
                'error',
                'This transaction has already been refunded.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 30-minute refund limit
        |--------------------------------------------------------------------------
        */

        $refundDeadline =
            \Carbon\Carbon::parse($transaction->created_at)
                ->addMinutes(30);

        if (now()->greaterThan($refundDeadline)) {
            return back()->with(
                'error',
                'This transaction can no longer be refunded because it is more than 30 minutes old.'
            );
        }


        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Restore inventory only when food is untouched/resellable
            |--------------------------------------------------------------------------
            */

            if ($validated['refund_condition'] === 'resellable') {

                $orderItems = DB::table('laravel.order_items as order_items')
                    ->join(
                        'laravel.menu_item_ingredient as menu_item_ingredient',
                        'order_items.menu_item_id',
                        '=',
                        'menu_item_ingredient.menu_item_id'
                    )
                    ->join(
                        'laravel.ingredients as ingredients',
                        'menu_item_ingredient.ingredient_id',
                        '=',
                        'ingredients.id'
                    )
                    ->where(
                        'order_items.order_id',
                        $transaction->id
                    )
                    ->select(
                        'menu_item_ingredient.ingredient_id',
                        'menu_item_ingredient.quantity_used',
                        'menu_item_ingredient.unit_id',
                        'ingredients.primary_unit_id',
                        'ingredients.secondary_unit_id',
                        'ingredients.conversion_factor',
                        'order_items.quantity'
                    )
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | Combine ingredient quantities from all dishes
                |--------------------------------------------------------------------------
                */

                $restoreQuantities = [];


                foreach ($orderItems as $item) {

                    $recipeQuantity =
                        (float) $item->quantity_used;


                    /*
                    |--------------------------------------------------------------
                    | Convert secondary unit back to primary unit
                    |--------------------------------------------------------------
                    */

                    if (
                        (string) $item->unit_id ===
                        (string) $item->secondary_unit_id
                    ) {
                        $conversionFactor =
                            (float) $item->conversion_factor;

                        if ($conversionFactor <= 0) {
                            throw new \RuntimeException(
                                'Invalid conversion factor for ingredient ID ' .
                                $item->ingredient_id
                            );
                        }

                        $recipeQuantity =
                            $recipeQuantity /
                            $conversionFactor;
                    }


                    $totalQuantity =
                        $recipeQuantity *
                        (float) $item->quantity;


                    if (!isset(
                        $restoreQuantities[$item->ingredient_id]
                    )) {
                        $restoreQuantities[$item->ingredient_id] = 0;
                    }


                    $restoreQuantities[$item->ingredient_id] +=
                        $totalQuantity;
                }


                /*
                |--------------------------------------------------------------------------
                | Restore branch inventory
                |--------------------------------------------------------------------------
                */

                foreach ($restoreQuantities as $ingredientId => $quantity) {

                    if ($quantity <= 0) {
                        continue;
                    }


                    $branchInventory =
                        DB::table('laravel.branch_inventory')
                            ->where(
                                'ingredient_id',
                                $ingredientId
                            )
                            ->where(
                                'branch_id',
                                $transaction->branch_id
                            )
                            ->lockForUpdate()
                            ->first();


                    if (!$branchInventory) {
                        throw new \RuntimeException(
                            'Branch inventory was not found for ingredient ID ' .
                            $ingredientId
                        );
                    }


                    DB::table('laravel.branch_inventory')
                        ->where('id', $branchInventory->id)
                        ->update([
                            'stock_quantity' =>
                                (float) $branchInventory->stock_quantity
                                + $quantity
                        ]);
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Mark transaction as refunded
            |--------------------------------------------------------------------------
            */

            DB::table('laravel.orders')
                ->where('id', $transaction->id)
                ->update([
                    'status' => 'refunded',
                    'payment_status' => 'refunded',
                    'refund_reason' => $validated['refund_reason'],
                    'refund_condition' => $validated['refund_condition'],
                    'refunded_at' => now(),
                    'refunded_by' => $user->id,
                    'updated_at' => now(),
                ]);


            DB::commit();

            return back()->with(
                'success',
                'Transaction refunded successfully.'
            );

        } catch (\Throwable $error) {

            DB::rollBack();

            return back()->with(
                'error',
                'Refund failed: ' . $error->getMessage()
            );
        }
    }
}