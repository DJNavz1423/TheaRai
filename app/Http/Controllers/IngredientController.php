<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class IngredientController extends Controller
{
    public function index(): View{
        $ingredients = DB::table('laravel.admin_global_inventory')->orderBy('updated_at', 'desc')->get();
        
        $categories = DB::table('laravel.ingredient_categories')->get();
        $units = DB::table('laravel.units')->get();
        $branches = DB::table('laravel.branches')->get();

        return view('admin.inventory.inventory', compact('ingredients', 'categories', 'units', 'branches'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'item_code'         => 'nullable|unique:pgsql.laravel.ingredients,item_code',
            'name'              => 'required|string|max:255',
            'category_id'       => 'required|exists:pgsql.laravel.ingredient_categories,id',
            'primary_unit_id'   => 'required|exists:pgsql.laravel.units,id',
            'secondary_unit_id' => 'required|exists:pgsql.laravel.units,id',
            'conversion_factor' => 'required|numeric|min:0.01',
            'description'       => 'nullable|string|max:1000',
            'img_url'           => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'branch_id'         => 'required|exists:pgsql.laravel.branches,id',
            'stock_quantity'    => 'nullable|numeric|min:0',
            'purchase_price'    => 'nullable|numeric|min:0',
            'alert_threshold'   => 'nullable|numeric|min:0',
            'fund_source'       => 'required|string|max:255',    
        ]);

        if($request->hasFile('img_url')){
            $file = $request->file('img_url');
            $path = $file->store('images', 'supabase');
            $validated['img_url'] = Storage::disk('supabase')->url($path);
        } else{
            unset($validated['img_url']);
        }

        $stockQty = $validated['stock_quantity'] ?? 0;
        $purchasePrice = $validated['purchase_price'] ?? 0;
        $totalExpense = $stockQty * $purchasePrice;

        if($validated['fund_source'] === 'cash_in_hand' && $totalExpense > 0){
            $totalSystemCashIn = DB::table('laravel.orders')->where('payment_method', 'cash')->sum('total_amount');
            $totalSystemCashSpent = DB::table('laravel.expenses')->where('fund_source', 'cash_in_hand')->sum('total_amount');
            $availableSystemCash = $totalSystemCashIn - $totalSystemCashSpent;

            if($totalExpense > $availableSystemCash){
                return back()->with('error', 'Insufficient System Cash! Available: ₱' . number_format($availableSystemCash, 2));
            }
        }

        DB::beginTransaction();
        try {
            // 1. Insert into Global Ingredients Table
            $ingredientId = DB::table('laravel.ingredients')->insertGetId([
                'item_code' => $validated['item_code'],
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'primary_unit_id' => $validated['primary_unit_id'],
                'secondary_unit_id' => $validated['secondary_unit_id'],
                'conversion_factor' => $validated['conversion_factor'],
                'description' => $validated['description'],
                'img_url' => $validated['img_url'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Insert into Branch Inventory Table
            DB::table('laravel.branch_inventory')->insert([
                'branch_id' => $validated['branch_id'],
                'ingredient_id' => $ingredientId,
                'stock_quantity' => $stockQty,
                'purchase_price' => $purchasePrice,
                'alert_threshold' => $validated['alert_threshold'] ?? 0,
            ]);

            // 3. Log Expense if there is an opening stock cost
            if($totalExpense > 0){  
                $unitAbbr = DB::table('laravel.units')->where('id', $validated['primary_unit_id'])->value('abbreviation');
                $expenseId = DB::table('laravel.expenses')->insertGetId([
                    'expense_type' => 'ingredient_purchase',
                    'fund_source' => $validated['fund_source'],
                    'branch_id' => $validated['branch_id'], // Link expense to the branch
                    'total_amount' => $totalExpense,
                    'description' => 'Opening stock for ' . $stockQty . ' ' . $unitAbbr . ' of ' . $validated['name'], 
                    'created_at' => now()
                ]);
                $this->logActivity('created', 'expense', $expenseId, "Added opening stock expense for: {$validated['name']}");
            }

            $this->logActivity('created', 'ingredient', $ingredientId, "Added new inventory item: {$validated['name']}");

            DB::commit();
            return back()->with('success', 'Ingredient added to global catalog and branch inventory!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_code'         => 'nullable|unique:pgsql.laravel.ingredients,item_code,' . $id,
            'name'              => 'required|string|max:255',
            'category_id'       => 'required|exists:pgsql.laravel.ingredient_categories,id',
            'primary_unit_id'   => 'required|exists:pgsql.laravel.units,id',
            'secondary_unit_id' => 'required|exists:pgsql.laravel.units,id',
            'conversion_factor' => 'required|numeric|min:0.01',
            'description'       => 'nullable|string|max:1000',
            'img_url'           => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'alert_threshold'   => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('img_url')) {
            $file = $request->file('img_url');
            $path = $file->store('images', 'supabase');
            $validated['img_url'] = Storage::disk('supabase')->url($path);
        } else {
            unset($validated['img_url']);
        }

        $newThreshold = $validated['alert_threshold'] ?? null;
        unset($validated['alert_threshold']);

        $validated['updated_at'] = now();
        DB::table('laravel.ingredients')->where('id', $id)->update($validated);

        if ($newThreshold !== null) {
            DB::table('laravel.branch_inventory')
                ->where('ingredient_id', $id)
                ->update(['alert_threshold' => $newThreshold]);
        }

        $this->logActivity('updated', 'ingredient', $id, "Updated global details for ingredient: {$validated['name']}");
        
        return back()->with('success', 'Item Updated successfully!');
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
            'branch_id'   => 'required|exists:pgsql.laravel.branches,id', // Required from modal
            'quantity'    => 'required|numeric|min:0.01',
            'unit_type'   => 'required|in:primary,secondary',
            'unit_price'  => 'required|numeric|min:0',
            'fund_source' => 'required|string',
            'remarks'     => 'nullable|string|max:255'
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
            // Fetch the specific branch inventory
            $branchInventory = DB::table('laravel.branch_inventory')
                ->where('ingredient_id', $id)
                ->where('branch_id', $validated['branch_id'])
                ->first();

            if (!$branchInventory) {
                $inheritedThreshold = DB::table('laravel.branch_inventory')
                    ->where('ingredient_id', $id)
                    ->whereNotNull('alert_threshold')
                    ->value('alert_threshold') ?? 0;    

                DB::table('laravel.branch_inventory')->insert([
                    'branch_id' => $validated['branch_id'],
                    'ingredient_id' => $id,
                    'stock_quantity' => $addedQuantityPrimary,
                    'purchase_price' => $validated['unit_price'],
                    'alert_threshold' => $inheritedThreshold,
                ]);
            } else {
                // Update existing branch stock with Weighted Average Cost
                $currentTotalValue = $branchInventory->stock_quantity * $branchInventory->purchase_price;
                $newTotalValue = $currentTotalValue + $actualTotalCost;
                $newTotalStock = $branchInventory->stock_quantity + $addedQuantityPrimary;
                $newWacPrice = $newTotalStock > 0 ? ($newTotalValue / $newTotalStock) : $branchInventory->purchase_price;
                $newWacPrice = round($newWacPrice, 2);

                DB::table('laravel.branch_inventory')
                    ->where('id', $branchInventory->id)
                    ->update([
                        'stock_quantity' => $newTotalStock,
                        'purchase_price' => $newWacPrice
                    ]);
            }

            // Update the global updated_at timestamp
            DB::table('laravel.ingredients')->where('id', $id)->update(['updated_at' => now()]);

            if($actualTotalCost > 0){
                $remarks = $validated['remarks'] ?: "Restocked {$ingredient->name}";
                $expenseId = DB::table('laravel.expenses')->insertGetId([
                    'expense_type' => 'ingredient_purchase',
                    'fund_source' => $validated['fund_source'],
                    'branch_id' => $validated['branch_id'],
                    'total_amount' => $actualTotalCost,
                    'description' => $remarks,
                    'created_at' => now()
                ]);

                $this->logActivity('created', 'expense', $expenseId, "Restock expense for: {$ingredient->name}");
            }

            $this->logActivity('updated', 'ingredient_stock', $id, "Added stock to branch {$validated['branch_id']} for: {$ingredient->name}");

            DB::commit();
            return back()->with('success', 'Stock added successfully to Branch!');
        }catch(\Exception $e){
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function reduceStock(Request $request, $id){
        $validated = $request->validate([
            'branch_id' => 'required|exists:pgsql.laravel.branches,id',
            'quantity'  => 'required|numeric|min:0.01',
            'unit_type' => 'required|in:primary,secondary',
            'remarks'   => 'nullable|string|max:255'
        ]);

        $ingredient = DB::table('laravel.ingredients')->where('id', $id)->first();
        if (!$ingredient) return back()->with('error', 'Ingredient not found!');

        $branchInventory = DB::table('laravel.branch_inventory')
            ->where('ingredient_id', $id)
            ->where('branch_id', $validated['branch_id'])
            ->first();

        if (!$branchInventory) return back()->with('error', 'This branch does not have this ingredient in stock.');

        $reducedQuantityPrimary = $validated['unit_type'] === 'primary' ? $validated['quantity'] : ($validated['quantity'] / $ingredient->conversion_factor);

        if ($reducedQuantityPrimary > $branchInventory->stock_quantity){
            return back()->with('error', 'Cannot reduce more stock than available in this branch!');
        }

        // Reduce stock strictly from the branch
        DB::table('laravel.branch_inventory')
            ->where('id', $branchInventory->id)
            ->update([
                'stock_quantity' => DB::raw("stock_quantity - {$reducedQuantityPrimary}")
            ]);
            
        // Update global timestamp
        DB::table('laravel.ingredients')->where('id', $id)->update(['updated_at' => now()]);

        $this->logActivity('updated', 'ingredient_stock', $id, "Reduced stock from branch {$validated['branch_id']} for: {$ingredient->name}. Reason: {$validated['remarks']}");

        return back()->with('success', 'Stock reduced successfully!');
    }
}
