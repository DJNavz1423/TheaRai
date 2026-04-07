<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(): View{
        $ingredients = DB::table('laravel.ingredients')
            #join 1: get category
            ->join('laravel.ingredient_categories as cat', 'laravel.ingredients.category_id', '=', 'cat.id')
            # join 2: get primary unit
            ->join('laravel.units as p_unit', 'laravel.ingredients.primary_unit_id', '=', 'p_unit.id')   
            #join 3: get secondary unit
            ->join('laravel.units as s_unit', 'laravel.ingredients.secondary_unit_id', '=', 's_unit.id')
            ->select(
                'laravel.ingredients.*',
                'cat.name as category_name',
                'p_unit.name as primary_unit_name',
                'p_unit.abbreviation as primary_unit_abbr',
                's_unit.name as secondary_unit_name',
                's_unit.abbreviation as secondary_unit_abbr'
            )
            ->orderBy('laravel.ingredients.created_at', 'desc')
            ->get();

            $categories = DB::table('laravel.ingredient_categories')->get();
            $units = DB::table('laravel.units')->get();

            return view('admin.inventory.inventory', compact('ingredients', 'categories', 'units'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            // Format is 'connection.schema.table,column'
        'item_code'         => 'nullable|unique:pgsql.laravel.ingredients,item_code',
        'name'              => 'required|string|max:255',
        'category_id'       => 'required|exists:pgsql.laravel.ingredient_categories,id',
        'primary_unit_id'      => 'required|exists:pgsql.laravel.units,id',
        'secondary_unit_id'    => 'required|exists:pgsql.laravel.units,id',
        'conversion_factor' => 'required|numeric|min:0.01',
        'purchase_price'    => 'required|numeric|min:0',
        'stock_quantity'    => 'required|numeric|min:0',
        'fund_source'      => 'required|string|max:255',    
        //optional data
        'alert_threshold'   => 'nullable|numeric|min:0',
        'description'       => 'nullable|string|max:1000',
        ]);

        $totalExpense = $validated['stock_quantity'] * $validated['purchase_price'];

        if($validated['fund_source'] === 'cash_in_hand' && $totalExpense > 0){
            $totalSystemCashIn = DB::table('laravel.orders')
                ->where('payment_method', 'cash')
                ->sum('total_amount');

            $totalSystemCashSpent = DB::table('laravel.expenses')
                ->where('fund_source', 'cash_in_hand')
                ->sum('total_amount');

            $availableSystemCash = $totalSystemCashIn - $totalSystemCashSpent;

            if($totalExpense > $availableSystemCash){
                return back()->with('error', 'Insufficient System Cash! Available: ₱' . number_format($availableSystemCash, 2));
            }
        }

        DB::beginTransaction();
        try {
            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            $fundSource = $validated['fund_source'];
            unset($validated['fund_source']);

            $ingredientId = DB::table('laravel.ingredients')->insertGetId($validated);

            if($totalExpense > 0){  
                $unitAbbr = DB::table('laravel.units')
                    ->where('id', $validated['primary_unit_id'])
                    ->value('abbreviation');
                    
                $expenseId = DB::table('laravel.expenses')->insertGetId([
                    'expense_type' => 'ingredient_purchase',
                    'fund_source' => $fundSource,
                    'total_amount' => $totalExpense,
                    'description' => 'Opening stock for ' . $validated['stock_quantity']. ' ' . $unitAbbr . ' of ' . $validated['name'], 
                    'created_at' => now()
                ]);

                $this->logActivity('created', 'expense', $expenseId, "Added opening stock expense for: {$validated['name']}");
            }

            $this->logActivity('created', 'ingredient', $ingredientId, "Added new inventory item: {$validated['name']}");

            DB::commit();
            return back()->with('success', 'Ingredient added & expense recorded!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|exists:pgsql.laravel.ingredient_categories,id',
        'primary_unit_id' => 'required|exists:pgsql.laravel.units,id',
        'secondary_unit_id' => 'required|exists:pgsql.laravel.units,id',
        'conversion_factor' => 'required|numeric|min:0.01',
        'purchase_price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|numeric|min:0',
        'alert_threshold' => 'nullable|numeric|min:0',
        'description' => 'nullable|string|max:1000',
    ]);

    $validated['updated_at'] = now();

    DB::table('laravel.ingredients')->where('id', $id)->update($validated);

    $this->logActivity('updated', 'ingredient', $id, "Updated details for ingredient: {$validated['name']}");
    
    return back()->with('success', 'Updated successfully!');
}

public function destroy($id)
{
    $ingredientName = DB::table('laravel.ingredients')->where('id', $id)->value('name');

    
    DB::table('laravel.ingredients')->where('id', $id)->update([
        'deleted_at' => now()
    ]);

    $this->logActivity('archived', 'ingredient', $id, "Moved inventory item to trash: {$ingredientName}");

    return back()->with('success', 'Item moved to trash successfully!');
}

public function addStock(Request $request, $id){
    $validated = $request->validate([
        'quantity' => 'required|numeric|min:0.01',
        'unit_type' => 'required|in:primary,secondary',
        'unit_price' => 'required|numeric|min:0',
        'fund_source' => 'required|string',
        'remarks' => 'nullable|string|max:255'
    ]);

    $ingredient = DB::table('laravel.ingredients')->where('id', $id)->first();
    if(!$ingredient) return back()->with('error', 'Ingredient not found!');

    $addedQuantityPrimary = $validated['unit_type'] === 'primary' 
    ? $validated['quantity'] 
    : ($validated['quantity'] / $ingredient->conversion_factor);

    $actualTotalCost = $addedQuantityPrimary * $validated['unit_price'];

    if($validated['fund_source'] === 'cash_in_hand' && $actualTotalCost > 0){
        $totalSystemCashIn = DB::table('laravel.orders')->where('payment_method', 'cash')->sum('total_amount');
        $totalSystemCashSpent = DB::table('laravel.expenses')->where('fund_source', 'cash_in_hand')->sum('total_amount');
        $availableSystemCash = $totalSystemCashIn - $totalSystemCashSpent;

        if($actualTotalCost > $availableSystemCash){
            return back()->with('error', 'Insufficient System Cash! Available: ₱' . number_format($availableSystemCash, 2));
        }
    }

    DB::beginTransaction();
    try{
        $currentTotalValue = $ingredient->stock_quantity * $ingredient->purchase_price;
        $newTotalValue = $currentTotalValue + $actualTotalCost;
        $newTotalStock = $ingredient->stock_quantity + $addedQuantityPrimary;
        $newWacPrice = $newTotalStock > 0 ? ($newTotalValue / $newTotalStock) : $ingredient->purchase_price;

        DB::table('laravel.ingredients')->where('id', $id)->update([
            'stock_quantity' => $newTotalStock,
            'purchase_price' => $newWacPrice,
            'updated_at' => now()
        ]);

        if($actualTotalCost > 0){
            $remarks = $validated['remarks'] ?: "Restocked {$ingredient->name}";
            $expenseId = DB::table('laravel.expenses')->insertGetId([
                'expense_type' => 'ingredient_purchase',
                'fund_source' => $validated['fund_source'],
                'total_amount' => $actualTotalCost,
                'description' => $remarks,
                'created_at' => now()
            ]);

            $this->logActivity('created', 'expense', $expenseId, "Restock expense for: {$ingredient->name}");
        }

        $this->logActivity('updated', 'ingredient_stock', $id, "Added stock for: {$ingredient->name}");

        DB::commit();
        return back()->with('success', 'Stock added successfully!');
    }catch(\Exception $e){
        DB::rollBack();
        return back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

    public function reduceStock(Request $request, $id){
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'unit_type' => 'required|in:primary,secondary',
            'remarks' => 'nullable|string|max:255'
        ]);

        $ingredient = DB::table('laravel.ingredients')->where('id', $id)->first();
        if (!$ingredient) return back()->with('error', 'Ingredient not found!');

        $reducedQuantityPrimary = $validated['unit_type'] === 'primary' ? $validated['quantity'] : ($validated['quantity'] / $ingredient->conversion_factor);

        if ($reducedQuantityPrimary > $ingredient->stock_quantity){
            return back()->with('error', 'Cannot reduce more stock than available!');
        }

        DB::table('laravel.ingredients')->where('id', $id)->update([
            'stock_quantity' => DB::raw("stock_quantity - {$reducedQuantityPrimary}"),
            'updated_at' => now()
        ]);

        $this->logActivity('updated', 'ingredient_stock', $id, "Reduced stock for: {$ingredient->name}. Reason: {$validated['remarks']}");

        return back()->with('success', 'Stock reduced successfully!');
    }
}
