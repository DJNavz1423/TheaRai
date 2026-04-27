<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View{
        $expenses = DB::table('laravel.expenses')
            ->leftJoin('laravel.branches', 'expenses.branch_id', '=', 'branches.id')
            ->select('expenses.*', 'branches.name as branch_name')
            ->orderBy('expenses.created_at', 'desc')
            ->get();

        $ingredients = DB::table('laravel.admin_global_inventory')
            ->select('id', 
                'name', 
                'purchase_price', 
                'primary_unit_abbr', 
                'secondary_unit_abbr',
                DB::raw('(purchase_price / NULLIF(conversion_factor, 0)) as s_unit_price')
            )
            ->orderBy('name')
            ->get();
            
        $branches = DB::table('laravel.branches')->get();

        // Calculate available system cash for each branch
        foreach ($branches as $branch) {
            $totalSystemCashIn = DB::table('laravel.orders')
                ->where('branch_id', $branch->id)
                ->where('payment_method', 'cash')
                ->sum('total_amount');

            $totalSystemCashSpent = DB::table('laravel.expenses')
                ->where('branch_id', $branch->id)
                ->where('fund_source', 'cash_in_hand')
                ->sum('total_amount');

            $branch->available_cash = $totalSystemCashIn - $totalSystemCashSpent;
        }

        return view('admin.expenses.expenses', compact('expenses', 'ingredients', 'branches'));
    }

    public function storeRegular(Request $request){
        $validated = $request->validate([
            'branch_id' => 'required|integer|exists:pgsql.laravel.branches,id',
            'total_amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
            'expense_type' => 'required|string',
            'fund_source' => 'required|string',
        ]);

        if($validated['fund_source'] === 'cash_in_hand'){
            if(!$this->hasEnoughSystemCash($validated['total_amount'], $validated['branch_id'])){
                return back()->with('error', 'Insufficient System Cash at this specific branch!');
            }
        }

        $validated['created_at'] = now();

        $expenseId = DB::table('laravel.expenses')->insertGetId($validated);

        $this->logActivity('created', 'expense', $expenseId, "Added regular expense for Branch {$validated['branch_id']}: {$validated['description']}");

        return back()->with('success', 'Expense added successfully!');
    }

    public function storeRestock(Request $request){
        $request->validate([
            'branch_id'    => 'required|integer|exists:pgsql.laravel.branches,id',
            'fund_source'  => 'required|string',
            'description'  => 'nullable|string|max:1000',
            'total_amount' => 'required|numeric|min:0.01',
            'items'        => 'required|array',
            'items.*.ingredient_id' => 'required|integer|exists:pgsql.laravel.ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $totalExpense = $request->total_amount;
        $fundSource = $request->fund_source;
        $branchId = $request->branch_id;

        if($fundSource === 'cash_in_hand'){
            if(!$this->hasEnoughSystemCash($totalExpense, $branchId)){
                return back()->with('error', 'Insufficient System Cash at this specific branch!');
            }
        }

        DB::beginTransaction();
        try{
            $description = $request->description ?: 'Restocked ' . count($request->items). ' items';

            $expenseId = DB::table('laravel.expenses')->insertGetId([
                'branch_id' => $branchId,
                'expense_type' => 'restock',
                'fund_source' => $fundSource,
                'total_amount' => $totalExpense,
                'description' => $description,
                'created_at' => now()
            ]);

            foreach($request->items as $item){

                $branchInventory = DB::table('laravel.branch_inventory')
                    ->where('branch_id', $branchId)
                    ->where('ingredient_id', $item['ingredient_id'])
                    ->first();

                $qty = $item['quantity'];
                $unitCost = $item['unit_cost'];
                $totalCost = $qty * $unitCost;

                if ($branchInventory) {

                    // 🔥 WAC CALCULATION
                    $currentTotalValue = $branchInventory->stock_quantity * $branchInventory->purchase_price;
                    $newTotalValue = $currentTotalValue + $totalCost;
                    $newTotalStock = $branchInventory->stock_quantity + $qty;

                    $newWacPrice = $newTotalStock > 0
                        ? round($newTotalValue / $newTotalStock, 2)
                        : $branchInventory->purchase_price;

                    DB::table('laravel.branch_inventory')
                        ->where('id', $branchInventory->id)
                        ->update([
                            'stock_quantity' => $newTotalStock,
                            'purchase_price' => $newWacPrice
                        ]);

                } else {
                    DB::table('laravel.branch_inventory')->insert([
                        'branch_id' => $branchId,
                        'ingredient_id' => $item['ingredient_id'],
                        'stock_quantity' => $qty,
                        'purchase_price' => $unitCost, // use entered price
                        'alert_threshold' => 5,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $this->logActivity('created', 'restock', $expenseId, "Processed restock at Branch {$branchId}: {$description}");

            DB::commit();
            return back()->with('success', 'Restock added successfully!');

        } catch(\Exception $e){
            DB::rollback();
            return back()->with('error', 'Something went wrong: '. $e->getMessage());
        }
    }

    private function hasEnoughSystemCash($requiredAmount, $branchId){
        $totalSystemCashIn = DB::table('laravel.orders')
            ->where('branch_id', $branchId)
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $totalSystemCashSpent = DB::table('laravel.expenses')
            ->where('branch_id', $branchId)
            ->where('fund_source', 'cash_in_hand')
            ->sum('total_amount');

        $availableSystemCash = $totalSystemCashIn - $totalSystemCashSpent;

        return $availableSystemCash >= $requiredAmount;
    }
}