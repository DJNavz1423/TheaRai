<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(): View{
        $menuItems = DB::table('laravel.menu_items as mi')
            ->leftJoin('laravel.menu_categories as mc', 'mi.category_id', '=', 'mc.id')
            ->select('mi.*', 'mc.name as category_name')
            ->orderBy('mi.id', 'desc')
            ->get();

        $categories = DB::table('laravel.menu_categories')
            ->orderBy('name')
            ->get();
            
        $branches = DB::table('laravel.branches')->get();

        $ingredients = DB::table('laravel.admin_global_inventory as agi')
            ->select(
                'agi.id', 
                'agi.name', 
                'agi.purchase_price',
                'agi.primary_unit_abbr', 
                'agi.secondary_unit_abbr',
                DB::raw('(agi.purchase_price / NULLIF(agi.conversion_factor, 0)) as s_unit_price')
            )
            ->get();

        return view('admin.menu.menu', compact('menuItems', 'categories', 'ingredients', 'branches'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'final_price' => 'required|numeric|min:0',
            'branch_ids' => 'required|array|min:1', 
            'branch_ids.*' => 'integer|exists:pgsql.laravel.branches,id',
            
            'ingredients' => 'nullable|array',
            'ingredients.*.ingredient_id' => 'required|integer',
            'ingredients.*.quantity_used' => 'required|numeric|min:0.01',
            'description'       => 'nullable|string|max:1000',
            'img_url'           => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if($request->hasFile('img_url')){
            $file = $request->file('img_url');
            $path = $file->store('images', 'supabase');
            $validated['img_url'] = Storage::disk('supabase')->url($path);
        } else {
            $validated['img_url'] = null;
        }

        DB::beginTransaction();
        try{
            $menuItemId = DB::table('laravel.menu_items')->insertGetId([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'final_price' => $validated['final_price'],
                'description' => $validated['description'] ?? null,
                'img_url' => $validated['img_url'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $branchMenuData = [];
            foreach($validated['branch_ids'] as $branchId) {
                $branchMenuData[] = [
                    'branch_id' => $branchId,
                    'menu_item_id' => $menuItemId,
                    'is_available' => true,
                ];
            }
            DB::table('laravel.branch_menu_items')->insert($branchMenuData);

            if(!empty($validated['ingredients'])){
                $pivotData = [];
                foreach($validated['ingredients'] as $item){
                    $pivotData[] = [
                        'menu_item_id' => $menuItemId,
                        'ingredient_id' => $item['ingredient_id'],
                        'quantity_used' => $item['quantity_used']
                    ];
                }
                DB::table('laravel.menu_item_ingredient')->insert($pivotData);
            }

            $this->logActivity('created', 'menu_item', $menuItemId, "Created new dish: {$validated['name']}");

            DB::commit();
            return back()->with('success', 'Menu item added and assigned to branches successfully!');
        }catch(\Exception $e){
            DB::rollback();
            return back()->with('error', 'Error saving menu item: '. $e->getMessage());
        }
    }



    // Fetches the data for the modal via JavaScript
    public function getBranchPricing($id)
    {
        $branchData = DB::table('laravel.branch_menu_items')
            ->join('laravel.branches', 'branch_menu_items.branch_id', '=', 'branches.id')
            ->where('branch_menu_items.menu_item_id', $id)
            ->select('branches.name', 'branch_menu_items.branch_id', 'branch_menu_items.branch_price', 'branch_menu_items.is_available')
            ->get();

        return response()->json($branchData);
    }

    // Saves the updated prices and toggles from the modal
    public function updateBranchPricing(Request $request, $id)
    {
        $validated = $request->validate([
            'branch_data' => 'required|array',
            'branch_data.*.branch_id' => 'required|integer',
            'branch_data.*.branch_price' => 'nullable|numeric|min:0',
            'branch_data.*.is_available' => 'nullable' // Checkboxes send 'on' or nothing
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['branch_data'] as $data) {
                // If the checkbox is checked, it sends a value. If unchecked, it's missing.
                $isAvailable = isset($data['is_available']) ? true : false;
                
                DB::table('laravel.branch_menu_items')
                    ->where('menu_item_id', $id)
                    ->where('branch_id', $data['branch_id'])
                    ->update([
                        'branch_price' => $data['branch_price'], // Stays null if they leave it empty
                        'is_available' => $isAvailable
                    ]);
            }
            DB::commit();
            return back()->with('success', 'Branch pricing and availability updated!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating branches: ' . $e->getMessage());
        }
    }
}
