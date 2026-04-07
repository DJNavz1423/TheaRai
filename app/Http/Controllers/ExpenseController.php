<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View{
        $expenses = DB::table('laravel.expenses')
            ->orderBy('created_at', 'desc')
            ->get();

        $ingredients = DB::table('laravel.ingredients as ingredient')
            ->join('laravel.units as p_unit', 'ingredient.primary_unit_id', '=', 'p_unit.id')
            ->select('ingredient.id', 'ingredient.name', 'ingredient.purchase_price', 'p_unit.abbreviation as primary_unit_abbr')
            ->get();

        return view('admin.expenses.expenses', compact('expenses', 'ingredients'));
    }

    public function storeRegular(Request $request){
        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'expense_type' => 'required|string',
            'fund_source' => 'required|string',
        ]);

        if($validated['fund_source'] === 'cash_in_hand'){
            if(!$this->hasEnoughSystemCash($validated['total_amount'])){
                return back()->with('error', 'Insufficient System Cash!');
            }
        }

        $validated['created_at'] = now();

        $expenseId = DB::table('laravel.expenses')->insertGetId($validated);

        $this->logActivity('created', 'expense', $expenseId, "Added regular expense: {$validated['description']}");

        return back()->with('success', 'Expense added successfully!');
    }

    public function storeRestock(Request $request){
        $request->validate([
            'fund_source'  => 'required|string',
            'description'  => 'nullable|string|max:1000',
            'total_amount' => 'required|numeric|min:0.01',
            'items'        => 'required|array',
            'items.*.ingredient_id' => 'required|integer|exists:pgsql.laravel.ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $totalExpense = $request->total_amount;
        $fundSource = $request->fund_source;

        if($fundSource === 'cash_in_hand'){
            if(!$this->hasEnoughSystemCash($totalExpense)){
                return back()->with('error', 'Insufficient System Cash!');
            }
        }

        DB::beginTransaction();
        try{
            $description = $request->description ?: 'Restocked ' . count($request->items). ' items';

            $expenseId = DB::table('laravel.expenses')->insertGetId([
                'expense_type' => 'restock',
                'fund_source' => $fundSource,
                'total_amount' => $totalExpense,
                'description' => $description,
                'created_at' => now()
            ]);

            foreach($request->items as $item){
                DB::table('laravel.ingredients')
                    ->where('id', $item['ingredient_id'])
                    ->increment('stock_quantity', $item['quantity']);
            }

            $this->logActivity('created', 'restock', $expenseId, "Processed restock: {$description}");

            DB::commit();
            return back()->with('success', 'Restock added successfully!');

        } catch(\Exception $e){
            DB::rollback();
            return back()->with('error', 'Something went wrong: '. $e->getMessage());
        }
    }

    private function hasEnoughSystemCash($requiredAmount){
        $totalSystemCashIn = DB::table('laravel.orders')
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $totalSystemCashSpent = DB::table('laravel.expenses')
            ->where('fund_source', 'cash_in_hand')
            ->sum('total_amount');

        $availableSystemCash = $totalSystemCashIn - $totalSystemCashSpent;

        return $availableSystemCash >= $requiredAmount;
    }
}