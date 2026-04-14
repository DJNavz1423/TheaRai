<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller{
    public function index() : View{
      $layout = auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.cashier';
      
      $categories = DB::table('laravel.menu_categories')->get();
      
      $menuItems = DB::table('laravel.menu_items')
        ->where('final_price', '>', 0)
        ->get();

      return view('pos.pos', compact('categories', 'menuItems', 'layout'));
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

      try{
        DB::beginTransaction();

        $receiptNo = 'REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $changeAmount = $request->cash_tendered - $request->total_amount;

        $orderId = DB::table('laravel.orders')->insertGetId([
          'receipt_no' => $receiptNo,
          'total_amount' => $request->total_amount,
          'cash_tendered' => $request->cash_tendered,
          'change_amount' => $changeAmount,
          'payment_method' => $request->payment_method,
          'reference_number' => $request->reference_number,
          'created_at' => now(),
        ]);

        foreach ($request->cart as $cartItem){
          DB::table('laravel.order_items')->insert([
            'order_id' => $orderId,
            'menu_item_id' => $cartItem['id'],
            'quantity' => $cartItem['quantity'],
            'price_at_time' => $cartItem['price'],
            'subtotal' => $cartItem['price'] * $cartItem['quantity'],
            'created_at' => now(),
          ]);

          $recipeItems = DB::table('laravel.menu_item_ingredient as pivot')
            ->join('laravel.ingredients as ing', 'pivot.ingredient_id', '=', 'ing.id')
            ->where('pivot.menu_item_id', $cartItem['id'])
            ->select('ing.id', 'pivot.quantity_used', 'ing.conversion_factor')
            ->get();

          foreach ($recipeItems as $ingredient){
            $totalSUnitsUsed = $ingredient->quantity_used * $cartItem['quantity'];
            $primaryUnitsUsed = $totalSUnitsUsed / $ingredient->conversion_factor;

            DB::table('laravel.ingredients')
              ->where('id', $ingredient->id)
              ->decrement('stock_quantity', $primaryUnitsUsed);
          }
        }

        $this->logActivity('created', 'order', $orderId, "{$receiptNo} for ₱" . number_format($request->total_amount, 2));

        DB::commit();

        return response()->json([
          'success' => true,
          'message' => 'Order processed successfully',
          'receipt' => $receiptNo,
          'change' => $changeAmount,
        ]);
      }

      catch(\Exception $e){
        DB::rollback();
        return response()->json([
          'success' => false,
          'error' => $e->getMessage(),
                  ], 500);
      }
    }

    public function toggleAvailability(Request $request, $id){
      $item = DB::table('laravel.menu_items')->where('id', $id)->first();

      if(!$item){
        return response()->json(['success' => false, 'message' => 'Menu item not found'], 404);
      }

      $newStatus = !$item->is_available;

      DB::table('laravel.menu_items')
        ->where('id', $id)
        ->update(['is_available' => $newStatus]);

      $statusText = $newStatus ? 'Available' : 'Unavailable';
      $this->logActivity('updated', 'menu_item', $id, "Cashier marked {$item->name} as {$statusText}");

      return response()->json([
        'success' => true, 
        'is_available' => $newStatus,
        'message' => "Menu item marked as {$statusText}"
        ]);
    }
}
