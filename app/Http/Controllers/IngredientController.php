<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class IngredientController extends Controller
{
    public function index(): View{
        $ingredients = DB::table('laravel.admin_global_inventory')
            ->orderBy('updated_at', 'desc')
            ->where('is_deleted', false)
            ->get();

        $inventoryBreakdown = DB::table('laravel.branch_inventory')
            ->whereNull('deleted_at')
            ->get();
        
        $categories = DB::table('laravel.ingredient_categories')->get();
        $units = DB::table('laravel.units')->get();

        $branches = DB::table('laravel.branches')
            ->get()
            ->map(function($branch) {

                $cashIn = DB::table('laravel.orders')
                    ->where('branch_id', $branch->id)
                    ->where('payment_method', 'cash')
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');

                $cashSpent = DB::table('laravel.expenses')
                    ->where('branch_id', $branch->id)
                    ->where('fund_source', 'cash_in_hand')
                    ->sum('total_amount');

                $branch->cash_in_hand = max(0, $cashIn - $cashSpent);
                return $branch;
            });

        $branchCash = [];

        foreach ($branches as $branch) {
            $branchCash[$branch->id] = $branch->cash_in_hand;
        }    

        return view('admin.inventory.inventory', compact('ingredients', 'inventoryBreakdown', 'categories', 'units', 'branches', 'branchCash'));
    }

    public function store(Request $request) {
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

        if ($request->hasFile('img_url')) {
            $file = $request->file('img_url');
            $path = $file->store('images', 'supabase');
            $validated['img_url'] = Storage::disk('supabase')->url($path);
        } else {
            unset($validated['img_url']);
        }

        $stockQty = $validated['stock_quantity'] ?? 0;
        $purchasePrice = $validated['purchase_price'] ?? 0;
        $totalExpense = $stockQty * $purchasePrice;

        if ($validated['fund_source'] === 'cash_in_hand' && $totalExpense > 0) {

            $branchId = $validated['branch_id'];

            $totalBranchCashIn = DB::table('laravel.orders')
                ->where('payment_method', 'cash')
                ->where('payment_status', 'paid')
                ->where('branch_id', $branchId)
                ->sum('total_amount');

            $totalBranchCashSpent = DB::table('laravel.expenses')
                ->where('fund_source', 'cash_in_hand')
                ->where('branch_id', $branchId)
                ->sum('total_amount');

            $availableSystemCash = $totalBranchCashIn - $totalBranchCashSpent;

            if ($totalExpense > $availableSystemCash) {
                return back()->with(
                    'error',
                    'Insufficient System Cash! Available: ₱' .
                    number_format($availableSystemCash, 2)
                );
            }
        }

        DB::beginTransaction();

        try {
            /*
            * 1. Create the global ingredient.
            */
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

            /*
            * 2. Create the opening-stock expense BEFORE
            *    inserting into branch_inventory.
            *
            *    This gives the stock-log trigger an expense_id.
            */
            $expenseId = null;

            if ($totalExpense > 0) {
                $unitAbbr = DB::table('laravel.units')
                    ->where('id', $validated['primary_unit_id'])
                    ->value('abbreviation');

                $expenseId = DB::table('laravel.expenses')->insertGetId([
                    'expense_type' => 'ingredient_purchase',
                    'fund_source' => $validated['fund_source'],
                    'branch_id' => $validated['branch_id'],
                    'total_amount' => $totalExpense,
                    'description' => 'Opening stock for ' .
                        $stockQty . ' ' .
                        $unitAbbr . ' of ' .
                        $validated['name'],
                    'created_at' => now()
                ]);

                if ($validated['fund_source'] === 'cash_in_hand') {
                    DB::table('laravel.cash_transactions')->insert([
                        'transaction_type' => 'expense',
                        'fund_source' => 'cash_in_hand',
                        'amount' => $totalExpense,
                        'branch_id' => $validated['branch_id'],
                        'description' => 'Opening stock for ' .
                            $validated['name'],
                        'created_at' => now(),
                    ]);
                }

                $this->logActivity(
                    'created',
                    'expense',
                    $expenseId,
                    "Added opening stock expense for: {$validated['name']}"
                );
            }

            /*
            * 3. Tell PostgreSQL what is about to happen.
            *
            *    The log_opening_stock trigger will read this.
            */
            DB::statement(
                "SELECT set_config('app.stock_source_type', ?, true)",
                ['new_ingredient']
            );

            DB::statement(
                "SELECT set_config('app.stock_source_id', ?, true)",
                [(string) $ingredientId]
            );

            DB::statement(
                "SELECT set_config('app.stock_order_id', ?, true)",
                ['']
            );

            DB::statement(
                "SELECT set_config('app.stock_expense_id', ?, true)",
                [$expenseId ? (string) $expenseId : '']
            );

            DB::statement(
                "SELECT set_config('app.stock_remarks', ?, true)",
                ['Initial Stock']
            );

            /*
            * 4. Insert branch inventory.
            *
            *    DO NOT insert into stock_logs here.
            *    Supabase's log_opening_stock trigger does it.
            */
            DB::table('laravel.branch_inventory')->insert([
                'branch_id' => $validated['branch_id'],
                'ingredient_id' => $ingredientId,
                'stock_quantity' => $stockQty,
                'purchase_price' => $purchasePrice,
                'alert_threshold' => $validated['alert_threshold'] ?? 0,
            ]);

            $this->logActivity(
                'created',
                'ingredient',
                $ingredientId,
                "Added new inventory item: {$validated['name']}"
            );

            DB::commit();

            return back()->with(
                'success',
                'Ingredient added to global catalog and branch inventory!'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with(
                'error',
                'An error occurred: ' . $e->getMessage()
            );
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
            'stock_quantity' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'branch_id'      => 'nullable|exists:pgsql.laravel.branches,id',
        ]);

        if ($request->hasFile('img_url')) {
            $file = $request->file('img_url');
            $path = $file->store('images', 'supabase');
            $validated['img_url'] = Storage::disk('supabase')->url($path);
        } else {
            unset($validated['img_url']);
        }

        $newThreshold = $validated['alert_threshold'] ?? null;
        $newStockQty = $validated['stock_quantity'] ?? null;
        $newPurchasePrice = $validated['purchase_price'] ?? null;
        $branchId = $validated['branch_id'] ?? null;

        unset(
            $validated['alert_threshold'],
            $validated['stock_quantity'],
            $validated['purchase_price'],
            $validated['branch_id']
        );

        $validated['updated_at'] = now();
        DB::table('laravel.ingredients')->where('id', $id)->update($validated);

        if ($branchId) {

            $branchInventory = DB::table('laravel.branch_inventory')
                ->where('ingredient_id', $id)
                ->where('branch_id', $branchId)
                ->first();

            if ($branchInventory) {

                DB::table('laravel.branch_inventory')
                    ->where('id', $branchInventory->id)
                    ->update([
                        'stock_quantity' => $newStockQty,
                        'purchase_price' => $newPurchasePrice,
                        'alert_threshold' => $newThreshold,
                    ]);

            } else {

                DB::table('laravel.branch_inventory')
                    ->insert([
                        'ingredient_id' => $id,
                        'branch_id' => $branchId,
                        'stock_quantity' => $newStockQty ?? 0,
                        'purchase_price' => $newPurchasePrice ?? 0,
                        'alert_threshold' => $newThreshold ?? 0,
                    ]);
            }
        }

        $this->logActivity('updated', 'ingredient', $id, "Updated global details for ingredient: {$validated['name']}");
        
        return back()->with('success', 'Item Updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $branchId = $request->branch_id;

        $ingredientName = DB::table('laravel.ingredients')
            ->where('id', $id)
            ->value('name');

        $isUsedInMenu = DB::table('laravel.menu_item_ingredient')
            ->where('ingredient_id', $id)
            ->exists();

        if ($isUsedInMenu) {
            return back()->with(
                'error',
                "Cannot archive {$ingredientName} because it is currently used in one or more menu items."
            );
        }

        // BRANCH VIEW
        if ($branchId) {

            DB::table('laravel.branch_inventory')
                ->where('ingredient_id', $id)
                ->where('branch_id', $branchId)
                ->update([
                    'deleted_at' => now()
                ]);

            $this->logActivity(
                'archived',
                'ingredient_branch',
                $id,
                "Archived {$ingredientName} from branch {$branchId}"
            );

            return back()->with(
                'success',
                'Item archived from selected branch successfully!'
            );
        }

        // GLOBAL VIEW
        DB::table('laravel.ingredients')
            ->where('id', $id)
            ->update([
                'deleted_at' => now()
            ]);

        $this->logActivity(
            'archived',
            'ingredient',
            $id,
            "Moved inventory item to trash: {$ingredientName}"
        );

        return back()->with(
            'success',
            'Item moved to trash successfully!'
        );
    }

    public function addStock(Request $request, $id) {
        $validated = $request->validate([
            'branch_id'   => 'required|exists:pgsql.laravel.branches,id',
            'quantity'    => 'required|numeric|min:0.01',
            'unit_type'   => 'required|in:primary,secondary',
            'unit_price'  => 'required|numeric|min:0',
            'fund_source' => 'required|string',
            'remarks'     => 'nullable|string|max:255'
        ]);

        $ingredient = DB::table('laravel.ingredients')
            ->where('id', $id)
            ->first();

        if (!$ingredient) {
            return back()->with('error', 'Ingredient not found!');
        }

        $addedQuantityPrimary =
            $validated['unit_type'] === 'primary'
                ? $validated['quantity']
                : ($validated['quantity'] / $ingredient->conversion_factor);

        $actualTotalCost = $addedQuantityPrimary * $validated['unit_price'];

        if ($validated['fund_source'] === 'cash_in_hand' && $actualTotalCost > 0) {

            $branchId = $validated['branch_id'];

            $totalBranchCashIn = DB::table('laravel.orders')
                ->where('payment_method', 'cash')
                ->where('payment_status', 'paid')
                ->where('branch_id', $branchId)
                ->sum('total_amount');

            $totalBranchCashSpent = DB::table('laravel.expenses')
                ->where('fund_source', 'cash_in_hand')
                ->where('branch_id', $branchId)
                ->sum('total_amount');

            $availableBranchCash = $totalBranchCashIn - $totalBranchCashSpent;

            if ($actualTotalCost > $availableBranchCash) {
                return back()->with(
                    'error',
                    'Insufficient Branch Cash! Available at branch: ₱' .
                    number_format($availableBranchCash, 2)
                );
            }
        }

        DB::beginTransaction();

        try {
            $remarks =
                $validated['remarks']
                ?: "Restocked {$ingredient->name}";

            /*
            * 1. Create the restock expense FIRST.
            */
            $expenseId = null;

            if ($actualTotalCost > 0) {

                $expenseId = DB::table('laravel.expenses')->insertGetId([
                    'expense_type' => 'restock',
                    'fund_source' => $validated['fund_source'],
                    'branch_id' => $validated['branch_id'],
                    'total_amount' => $actualTotalCost,
                    'description' => $remarks,
                    'created_at' => now()
                ]);

                if ($validated['fund_source'] === 'cash_in_hand') {

                    DB::table('laravel.cash_transactions')->insert([
                        'transaction_type' => 'expense',
                        'fund_source' => 'cash_in_hand',
                        'amount' => $actualTotalCost,
                        'branch_id' => $validated['branch_id'],
                        'description' => $remarks,
                        'created_at' => now(),
                    ]);
                }

                $this->logActivity(
                    'created',
                    'expense',
                    $expenseId,
                    "Restock expense for: {$ingredient->name}"
                );
            }

            /*
            * 2. Tell PostgreSQL this inventory change
            *    is caused by a RESTOCK.
            */
            DB::statement(
                "SELECT set_config('app.stock_source_type', ?, true)",
                ['restock']
            );

            DB::statement(
                "SELECT set_config('app.stock_source_id', ?, true)",
                [$expenseId ? (string) $expenseId : '']
            );

            DB::statement(
                "SELECT set_config('app.stock_order_id', ?, true)",
                ['']
            );

            DB::statement(
                "SELECT set_config('app.stock_expense_id', ?, true)",
                [$expenseId ? (string) $expenseId : '']
            );

            DB::statement(
                "SELECT set_config('app.stock_remarks', ?, true)",
                [$remarks]
            );

            /*
            * 3. Get this specific branch's inventory.
            */
            $branchInventory = DB::table('laravel.branch_inventory')
                ->where('ingredient_id', $id)
                ->where('branch_id', $validated['branch_id'])
                ->first();

            if (!$branchInventory) {

                /*
                * New branch inventory record.
                *
                * log_opening_stock trigger handles the stock_logs row.
                */
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            } else {

                /*
                * Existing branch inventory.
                *
                * UPDATE fires log_stock_update automatically.
                */
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

            DB::table('laravel.ingredients')
                ->where('id', $id)
                ->update([
                    'updated_at' => now()
                ]);

            $this->logActivity(
                'updated',
                'ingredient_stock',
                $id,
                "Added stock to branch {$validated['branch_id']} for: {$ingredient->name}"
            );

            DB::commit();

            return back()->with(
                'success',
                'Stock added successfully to Branch!'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public function reduceStock(Request $request, $id) {
        $validated = $request->validate([
            'branch_id' => 'required|exists:pgsql.laravel.branches,id',
            'quantity'  => 'required|numeric|gt:0',
            'unit_type' => 'required|in:primary,secondary',
            'reason'    => 'required|in:expired,spoiled,damaged,spilled,contaminated,overproduction,other',
            'remarks'   => 'required|string|max:1000'
        ]);

        $remarks = trim($validated['remarks']);

        if ($remarks === '') {
            return back()->withInput()->withErrors([
                'remarks' => 'Remarks are required.',
            ]);
        }

        $ingredient = DB::table('laravel.ingredients as ingredients')
            ->leftJoin(
                'laravel.units as units',
                'ingredients.primary_unit_id',
                '=',
                'units.id'
            )
            ->whereNull('ingredients.deleted_at')
            ->where('ingredients.id', $id)
            ->select(
                'ingredients.id',
                'ingredients.name',
                'ingredients.conversion_factor',
                'units.abbreviation as primary_unit_abbr'
            )
            ->first();

        if (!$ingredient || (float) $ingredient->conversion_factor <= 0) {
            return back()->withInput()->with(
                'error',
                'Ingredient not found or has an invalid conversion factor.'
            );
        }

        $reducedQuantityPrimary = round(
            $validated['unit_type'] === 'primary'
                ? (float) $validated['quantity']
                : (float) $validated['quantity'] / (float) $ingredient->conversion_factor,
            4
        );

        if ($reducedQuantityPrimary <= 0) {
            return back()->withInput()->with(
                'error',
                'The quantity is too small to record.'
            );
        }

        DB::beginTransaction();

        try {
            $branchInventory = DB::table('laravel.branch_inventory')
                ->where('ingredient_id', $id)
                ->where('branch_id', $validated['branch_id'])
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$branchInventory) {
                DB::rollBack();

                return back()->withInput()->with(
                    'error',
                    'This branch does not have an active inventory record for this ingredient.'
                );
            }

            $currentStock = (float) $branchInventory->stock_quantity;

            if ($reducedQuantityPrimary > $currentStock) {
                DB::rollBack();

                return back()->withInput()->with(
                    'error',
                    'Waste quantity cannot exceed the available branch stock.'
                );
            }

            $unitCost = max(0, (float) $branchInventory->purchase_price);
            $totalCost = round($reducedQuantityPrimary * $unitCost, 2);
            $reasonLabel = ucfirst(str_replace('_', ' ', $validated['reason']));
            $stockRemarks = "Waste - {$reasonLabel}: {$remarks}";

            $wasteId = DB::table('laravel.inventory_waste')->insertGetId([
                'branch_id' => $validated['branch_id'],
                'ingredient_id' => $id,
                'quantity' => $reducedQuantityPrimary,
                'unit_abbreviation' => $ingredient->primary_unit_abbr ?: 'unit',
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reason' => $validated['reason'],
                'remarks' => $remarks,
                'recorded_by' => auth()->id(),
                'created_at' => now(),
            ]);

            DB::statement(
                "SELECT set_config('app.stock_source_type', ?, true)",
                ['manual_reduction']
            );

            DB::statement(
                "SELECT set_config('app.stock_source_id', ?, true)",
                [(string) $wasteId]
            );

            DB::statement(
                "SELECT set_config('app.stock_order_id', ?, true)",
                ['']
            );

            DB::statement(
                "SELECT set_config('app.stock_expense_id', ?, true)",
                ['']
            );

            DB::statement(
                "SELECT set_config('app.stock_remarks', ?, true)",
                [$stockRemarks]
            );

            $updatedRows = DB::update(
                'UPDATE laravel.branch_inventory
                SET stock_quantity = stock_quantity - ?
                WHERE id = ? AND stock_quantity >= ?',
                [
                    $reducedQuantityPrimary,
                    $branchInventory->id,
                    $reducedQuantityPrimary,
                ]
            );

            if ($updatedRows !== 1) {
                throw new \RuntimeException('Branch stock changed before the waste reduction was applied.');
            }

            $this->logActivity(
                'updated',
                'ingredient_stock',
                $id,
                "Recorded {$reducedQuantityPrimary} {$ingredient->primary_unit_abbr} as {$reasonLabel} waste for {$ingredient->name} at branch {$validated['branch_id']}. Remarks: {$remarks}"
            );

            DB::commit();

            return redirect()->route('admin.inventory.index')->with(
                'success',
                'Waste recorded and branch stock updated.'
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()->withInput()->with(
                'error',
                'Waste could not be recorded. No changes were saved.'
            );
        }
    }
}
