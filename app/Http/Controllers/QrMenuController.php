<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        $categories = DB::table('laravel.menu_categories')->get();

        $menuItems = DB::table('laravel.branch_menu_items as bmi')
            ->join('laravel.menu_items as mi', 'bmi.menu_item_id', '=', 'mi.id')
            ->where('bmi.branch_id', $branchId)
            ->where('bmi.is_available', true)
            ->whereNull('mi.deleted_at')
            ->select(
                'mi.id', 
                'mi.name', 
                'mi.img_url', 
                'mi.category_id', 
                DB::raw('COALESCE(bmi.branch_price, mi.final_price) as price')
            )
            ->get();

        return view('customer.qrMenu', compact('menuItems', 'table', 'branch', 'categories'));
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
        $tableName = DB::table('laravel.tables')->where('id', $validated['table_id'])->value('table_number');
        $description = "TheaRai Eatery - Order for Table " . $tableName;

        foreach ($validated['items'] as $item) {
            $totalAmount += ($item['price'] * $item['quantity']);
        }

        $receiptNo = 'QR-' . strtoupper(Str::random(8));

        DB::beginTransaction();
        try {
            // 1. Save Pending Order to Database
            $orderId = DB::table('laravel.orders')->insertGetId([
                'branch_id' => $validated['branch_id'],
                'table_id' => $validated['table_id'],
                'receipt_no' => $receiptNo,
                'total_amount' => $totalAmount,
                'payment_method' => 'xendit', 
                'payment_status' => 'pending', // Locked until Xendit verifies it
                'cash_tendered' => $totalAmount,
                'change_amount' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $orderItems = [];
            foreach ($validated['items'] as $item) {
                $orderItems[] = [
                    'order_id' => $orderId,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price_at_time' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ];
            }
            DB::table('laravel.order_items')->insert($orderItems);

            // 2. Send API Request to Xendit
            $secretKey = env('XENDIT_SECRET_KEY');
            
            // Xendit requires Basic Auth (Username is Secret Key, Password is blank)
            $response = Http::withBasicAuth($secretKey, '')
                ->post('https://api.xendit.co/v2/invoices', [
                    'external_id' => $receiptNo,
                    'amount' => $totalAmount,
                    'description' => $description,
                    'success_redirect_url' => url('/qr-menu/success?receipt=' . $receiptNo),
                    'currency' => 'PHP',
                    'payment_methods' => ['GCASH', 'PAYMAYA']
                ]);

            if ($response->successful()) {
                $xenditData = $response->json();

                // Save Xendit's Invoice ID so we can verify it later via Webhook
                DB::table('laravel.orders')
                    ->where('id', $orderId)
                    ->update(['payment_reference' => $xenditData['id']]);

                DB::commit();

                // Send the Xendit checkout link to the customer's phone
                return response()->json([
                    'success' => true,
                    'checkout_url' => $xenditData['invoice_url']
                ]);
            }

            throw new \Exception('Xendit API Error: ' . $response->body());

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        $receiptNo = $request->query('receipt');

        $order = DB::table('laravel.orders')
            ->join('laravel.branches', 'orders.branch_id', '=', 'branches.id')
            ->leftJoin('laravel.tables', 'orders.table_id', '=', 'tables.id')
            ->select('orders.*', 'branches.name as branch_name', 'branches.address as branch_address', 'tables.table_number')
            ->where('orders.receipt_no', $receiptNo)
            ->first();

        if (!$order) {
            return "Order not found.";
        }

        // --- NEW: Ask Xendit for the actual payment details! ---
        if ($order->payment_status === 'pending' && $order->payment_reference) {
            $secretKey = env('XENDIT_SECRET_KEY');
            
            $response = Http::withBasicAuth($secretKey, '')
                ->get("https://api.xendit.co/v2/invoices/" . $order->payment_reference);

            if ($response->successful()) {
                $xenditData = $response->json();
                
                if ($xenditData['status'] === 'PAID') {
                    // Update the database with the real payment method (GCASH or PAYMAYA)
                    $realMethod = strtolower($xenditData['payment_channel']);
                    
                    DB::table('laravel.orders')
                        ->where('id', $order->id)
                        ->update([
                            'payment_status' => 'paid',
                            'payment_method' => $realMethod,
                            'updated_at' => now()
                        ]);
                        
                    // Update the local $order variable so the blade view shows the right word
                    $order->payment_method = $realMethod;
                }
            }
        }
        // -------------------------------------------------------

        $items = DB::table('laravel.order_items')
            ->join('laravel.menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('order_items.order_id', $order->id)
            ->select('order_items.*', 'menu_items.name')
            ->get();

        return view('customer.success', compact('order', 'items'));
    }
}