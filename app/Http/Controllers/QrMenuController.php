<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class QrMenuController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->query('branch');
        $tableId = $request->query('table');

        if (!$branchId || !$tableId) {
            return "Invalid QR Code. Please scan the code on your table.";
        }

        $table = DB::table('laravel.tables')->where('id', $tableId)->where('branch_id', $branchId)->first();
        $branch = DB::table('laravel.branches')->where('id', $branchId)->first();

        if (!$table || !$branch) {
            return "Table or Branch not found.";
        }

        $categories = DB::table('laravel.menu_categories')
            ->whereNull('deleted_at')
            ->get();

        $menuItems = DB::table('laravel.branch_menu_items as bmi')
            ->join('laravel.menu_items as mi', 'bmi.menu_item_id', '=', 'mi.id')
            ->where('bmi.branch_id', $branchId)
            ->where('bmi.is_available', true)
            ->whereNull('mi.deleted_at')
            ->select(
                'mi.id', 
                'mi.name', 
                'mi.description',
                'mi.img_url', 
                'mi.category_id', 
                DB::raw('COALESCE(bmi.branch_price, mi.final_price) as price')
            )
            ->get();

        $bestSellerItems = DB::table('laravel.order_items as oi')
            ->join('laravel.orders as o', 'oi.order_id', '=', 'o.id')
            ->join('laravel.menu_items as mi', 'oi.menu_item_id', '=', 'mi.id')
            ->join('laravel.branch_menu_items as bmi', function ($join) use ($branchId) {
                $join->on('bmi.menu_item_id', '=', 'mi.id')
                    ->where('bmi.branch_id', '=', $branchId);
            })
            ->where('o.branch_id', $branchId)
            ->where('o.payment_status', 'paid')
            ->where('bmi.is_available', true)
            ->whereNull('mi.deleted_at')
            ->select(
                'mi.id',
                'mi.name',
                'mi.description',
                'mi.img_url',
                'mi.category_id',
                'bmi.branch_price',
                'mi.final_price',
                DB::raw('COALESCE(bmi.branch_price, mi.final_price) as price'),
                DB::raw('SUM(oi.quantity) as total_sold')
            )
            ->groupBy(
                'mi.id',
                'mi.name',
                'mi.description',
                'mi.img_url',
                'mi.category_id',
                'bmi.branch_price',
                'mi.final_price'
            )
            ->orderByDesc('total_sold')
            ->limit(6)
            ->get();

        return view('customer.qrMenu', compact('menuItems', 'bestSellerItems', 'table', 'branch', 'categories'));
    }

    // 2. Process the Cart and Generate the Xendit Link
    public function checkout(Request $request)
{
    $validated = $request->validate([
        'branch_id' => 'required|integer',
        'table_id' => 'required|integer',
        'items' => 'required|array',
        'items.*.id' => 'required|integer',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric'
    ]);

    $totalAmount = 0;

    $table = DB::table('laravel.tables')
        ->where('id', $validated['table_id'])
        ->where('branch_id', $validated['branch_id'])
        ->first(['id', 'table_number']);

    if (!$table) {
        return response()->json([
            'success' => false,
            'message' => 'The selected table could not be found.',
        ], 422);
    }

    $description = "TheaRai Eatery - Order for Table " . $table->table_number;

    foreach ($validated['items'] as $item) {
        $totalAmount += ($item['price'] * $item['quantity']);
    }

    $checkoutId = 'QR-PAY-' . strtoupper((string) Str::uuid());

    $secretKey = config('services.xendit.secret_key');

    if (!is_string($secretKey) || $secretKey === '') {
        return response()->json([
            'success' => false,
            'message' => 'Online payments are not configured. Please contact the restaurant.',
        ], 503);
    }

    try {

        $response = Http::withBasicAuth($secretKey, '')
            ->post('https://api.xendit.co/v2/invoices', [
                'external_id' => $checkoutId,
                'amount' => $totalAmount,
                'description' => $description,
                'success_redirect_url' => url(
                    '/qr-menu/success?receipt=' . $checkoutId
                ),
                'currency' => 'PHP',
                'payment_methods' => ['GCASH', 'PAYMAYA']
            ]);

        if (!$response->successful()) {
            throw new \Exception(
                'Xendit API Error: ' . $response->body()
            );
        }

        $xenditData = $response->json();

        Cache::put(
            'qr_pending_order:' . $checkoutId,
            [
                'branch_id' => $validated['branch_id'],
                'table_id' => $validated['table_id'],
                'checkout_id' => $checkoutId,
                'total_amount' => $totalAmount,
                'items' => $validated['items'],
                'payment_reference' => $xenditData['id'] ?? null,
            ],
            now()->addHours(24)
        );

        return response()->json([
            'success' => true,
            'checkout_url' => $xenditData['invoice_url']
        ]);

    } catch (\Exception $e) {

        Cache::forget('qr_pending_order:' . $checkoutId);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function success(Request $request)
{
    $checkoutId = $request->query('receipt');

    $cacheKey = 'qr_pending_order:' . $checkoutId;

    $pendingOrder = Cache::get($cacheKey);

    if ($pendingOrder && !isset($pendingOrder['checkout_id'])) {
        $pendingOrder['checkout_id'] = $pendingOrder['receipt_no'] ?? $checkoutId;
    }

    /*
     * If the temporary cache is already gone, check whether
     * this payment was already converted into a paid order.
     *
     * This prevents the success page from showing
     * "session expired" when the success URL is opened again.
     */
    if (!$pendingOrder) {

        $existingOrder = DB::table('laravel.orders')
            ->where(function ($query) use ($checkoutId) {
                $query->where('reference_number', $checkoutId)
                    ->orWhere('receipt_no', $checkoutId);
            })
            ->where('payment_status', 'paid')
            ->first();

        if (!$existingOrder) {
            return "Order session expired or could not be found.";
        }

        $orderId = $existingOrder->id;

    } else {

        if (!$pendingOrder['payment_reference']) {
            return "Payment information is missing.";
        }

        $secretKey = config('services.xendit.secret_key');

        if (!is_string($secretKey) || $secretKey === '') {
            return response('Online payments are not configured. Please contact the restaurant.', 503);
        }

        // Ask Xendit for the actual invoice status.
        $response = Http::withBasicAuth($secretKey, '')
            ->get(
                'https://api.xendit.co/v2/invoices/' .
                $pendingOrder['payment_reference']
            );

        if (!$response->successful()) {
            return "Unable to verify payment. Please contact the staff.";
        }

        $xenditData = $response->json();

        // Do NOT create an order unless Xendit says PAID.
        if (($xenditData['status'] ?? null) !== 'PAID') {
            return "Payment was not completed.";
        }

        // Create the actual paid order now.
        try {

            $orderId = $this->createPaidOrder(
                $pendingOrder,
                $xenditData
            );

        } catch (\Throwable $e) {

            // Another request may have created this order already.
            $existingOrder = DB::table('laravel.orders')
                ->where(function ($query) use ($checkoutId) {
                    $query->where('reference_number', $checkoutId)
                        ->orWhere('receipt_no', $checkoutId);
                })
                ->where('payment_status', 'paid')
                ->first();

            if ($existingOrder) {
                // Reuse the existing order instead of showing an error.
                $orderId = $existingOrder->id;

                \Illuminate\Support\Facades\Log::info(
                    'QR payment success reused existing order',
                    ['checkout_id' => $checkoutId]
                );

            } else {
                // No completed order exists, so record the actual failure.
                \Illuminate\Support\Facades\Log::error(
                    'QR paid order creation failed',
                    [
                        'checkout_id' => $checkoutId,
                        'payment_reference' =>
                            $pendingOrder['payment_reference'] ?? null,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                );

                return response(
                    'Payment was received, but the order could not be created. Please contact the staff.',
                    500
                );
            }
        }

        // Remove the temporary pending order data.
        Cache::forget($cacheKey);
    }

    $order = DB::table('laravel.orders')
        ->join(
            'laravel.branches',
            'orders.branch_id',
            '=',
            'branches.id'
        )
        ->leftJoin(
            'laravel.tables',
            'orders.table_id',
            '=',
            'tables.id'
        )
        ->select(
            'orders.*',
            'branches.name as branch_name',
            'branches.address as branch_address',
            'tables.table_number'
        )
        ->where('orders.id', $orderId)
        ->first();

    $items = DB::table('laravel.order_items')
        ->join(
            'laravel.menu_items',
            'order_items.menu_item_id',
            '=',
            'menu_items.id'
        )
        ->where('order_items.order_id', $orderId)
        ->select(
            'order_items.*',
            'menu_items.name'
        )
        ->get();

    return view(
        'customer.success',
        compact('order', 'items')
    );
}

    private function createPaidOrder(array $pendingOrder, array $xenditData){
    DB::beginTransaction();

    try {

        /*
         * Prevent duplicate order creation.
         */
        $existingOrder = DB::table('laravel.orders')
            ->where(function ($query) use ($pendingOrder) {
                $query->where('reference_number', $pendingOrder['checkout_id']);

                if (!empty($pendingOrder['receipt_no'])) {
                    $query->orWhere('receipt_no', $pendingOrder['receipt_no']);
                }
            })
            ->first();

        if ($existingOrder) {
            DB::commit();
            return $existingOrder->id;
        }

        $paymentMethod = strtolower(
            $xenditData['payment_channel'] ?? 'xendit'
        );

        $orderCreatedAt = now();
        $receiptNo = $this->nextQrReceiptNumber(
            (int) $pendingOrder['table_id'],
            $orderCreatedAt
        );

        $orderId = DB::table('laravel.orders')->insertGetId([
            'branch_id' => $pendingOrder['branch_id'],
            'table_id' => $pendingOrder['table_id'],
            'receipt_no' => $receiptNo,
            'reference_number' => $pendingOrder['checkout_id'],
            'total_amount' => $pendingOrder['total_amount'],
            'payment_method' => $paymentMethod,
            'payment_status' => 'paid',
            'status' => 'pending',
            'order_source' => 'qr',
            'cash_tendered' => $pendingOrder['total_amount'],
            'change_amount' => 0,
            'payment_reference' => $pendingOrder['payment_reference'],
            'created_at' => $orderCreatedAt,
            'updated_at' => $orderCreatedAt
        ]);

        /*
        * Tell PostgreSQL that all inventory changes
        * in this transaction are caused by this QR order.
        */
        DB::statement(
            "SELECT set_config('app.stock_source_type', ?, true)",
            ['order']
        );

        DB::statement(
            "SELECT set_config('app.stock_source_id', ?, true)",
            [(string) $orderId]
        );

        DB::statement(
            "SELECT set_config('app.stock_order_id', ?, true)",
            [(string) $orderId]
        );

        DB::statement(
            "SELECT set_config('app.stock_expense_id', ?, true)",
            ['']
        );

        DB::statement(
            "SELECT set_config('app.stock_remarks', ?, true)",
            ["QR Order {$receiptNo}"]
        );

        $orderItems = [];

        foreach ($pendingOrder['items'] as $item) {
            $orderItems[] = [
                'order_id' => $orderId,
                'menu_item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price_at_time' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity']
            ];
        }

        DB::table('laravel.order_items')->insert($orderItems);

        /*
         * Deduct inventory only AFTER payment.
         */
        $recipesByMenuItem = DB::table('laravel.menu_item_ingredient as pivot')
            ->join(
                'laravel.ingredients as ing',
                'pivot.ingredient_id',
                '=',
                'ing.id'
            )
            ->select(
                'pivot.menu_item_id',
                'ing.id',
                'pivot.quantity_used',
                'pivot.unit_id',
                'ing.primary_unit_id',
                'ing.secondary_unit_id',
                'ing.conversion_factor'
            )
            ->whereIn(
                'pivot.menu_item_id',
                collect($pendingOrder['items'])->pluck('id')->unique()
            )
            ->get()
            ->groupBy('menu_item_id');

        foreach ($pendingOrder['items'] as $item) {

            $recipeItems = $recipesByMenuItem->get((int) $item['id'], collect());

            foreach ($recipeItems as $ingredient) {

                $totalUsed =
                    $ingredient->quantity_used * $item['quantity'];

                if ($ingredient->unit_id == $ingredient->primary_unit_id) {

                    $primaryUnitsUsed = $totalUsed;

                } elseif (
                    $ingredient->unit_id ==
                    $ingredient->secondary_unit_id
                ) {

                    $primaryUnitsUsed =
                        $totalUsed / $ingredient->conversion_factor;

                } else {

                    throw new \Exception(
                        "Invalid recipe unit for ingredient {$ingredient->id}"
                    );
                }

                DB::table('laravel.branch_inventory')
                    ->where('ingredient_id', $ingredient->id)
                    ->where(
                        'branch_id',
                        $pendingOrder['branch_id']
                    )
                    ->decrement(
                        'stock_quantity',
                        $primaryUnitsUsed
                    );
            }
        }

        DB::commit();

        return $orderId;

    } catch (\Exception $e) {

        DB::rollBack();

        throw $e;
    }
}

    private function nextQrReceiptNumber(int $tableId, \Illuminate\Support\Carbon $createdAt): string
    {
        $tableNumber = DB::table('laravel.tables')
            ->where('id', $tableId)
            ->value('table_number');

        if (
            !is_string($tableNumber)
            || !preg_match('/(\d+)\s*$/u', trim($tableNumber), $matches)
        ) {
            throw new \RuntimeException(
                "Table {$tableId} does not have a table number ending in digits."
            );
        }

        $tableCode = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $yearMonth = $createdAt->copy()->timezone('Asia/Manila')->format('ym');
        $prefix = "QR-{$yearMonth}-{$tableCode}-";

        DB::select(
            'SELECT pg_advisory_xact_lock(hashtextextended(?, 0))',
            [$prefix]
        );

        $usedNumbers = DB::table('laravel.orders')
            ->where('receipt_no', 'like', $prefix . '%')
            ->pluck('receipt_no')
            ->mapWithKeys(function (string $receiptNo) use ($prefix) {
                $suffix = substr($receiptNo, strlen($prefix));

                return preg_match('/^\d{4}$/', $suffix)
                    ? [(int) $suffix => true]
                    : [];
            });

        $highestUsedNumber = $usedNumbers->keys()->max();
        $nextNumber = $highestUsedNumber === null
            ? 0
            : ($highestUsedNumber + 1) % 10000;

        for ($offset = 0; $offset < 10000; $offset++) {
            $sequence = ($nextNumber + $offset) % 10000;

            if (!$usedNumbers->has($sequence)) {
                return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            }
        }

        throw new \RuntimeException(
            "All QR receipt numbers for table {$tableCode} in {$yearMonth} are already in use."
        );
    }
}