<div id="addModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ url('/admin/menu') }}" method="POST" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h2>Add New Dish</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <label for="name">Dish Name</label>
                        <input type="text" name="name" id="name" placeholder="e.g., Bulalo" required>
                    </div>

                    <div class="input-group">
                        <label for="category">Category</label>
                        <select name="category_id" id="category" class="unit-selector" placeholder="Select category..." required>
                            <option value="" class="d-none"></option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="branch-select-wrapper">
                        <div class="input-group">
                            <label for="dish_branches">Available In Branches</label>
                            <select name="branch_ids[]" id="dish_branches" placeholder="Select branches..." multiple required>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" selected>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select the branches where this dish will be sold.</small>
                        </div>
                    </div>
                </div>
                
                <div class="tab-container">
                    <div class="row tab-titles mb-3">
                        <h3 class="tab-links active-link underline-fullwidth" onclick="openTab(event, 'recipe')">Recipe & Costing</h3>
                        <h3 class="tab-links underline-fullwidth" onclick="openTab(event, 'others')">Others</h3>
                    </div>
                        
                    <div class="container table-container modal-table border mb-1">
                        <table>
                            <thead>
                                <tr class="border-b">
                                    <th class="border-r">S.N</th>
                                    <th class="border-r">Ingredient Item Name</th>
                                    <th class="border-r">Quantity Per Serve</th>
                                    <th class="border-r">Unit Cost</th>
                                    <th>Cost Per Serve</th>
                                </tr>
                            </thead>

                            <tbody id="recipe-list">
                                <tr class="recipe-row">
                                    <td><span class="serial-number">1</span></td>
                                    <td>
                                        <select name="ingredients[0][ingredient_id]" class="recipe-select" required>
                                            <option value="" class="d-none"></option>
                                            @foreach($ingredients as $ingredient)
                                                <option value="{{ $ingredient->id }}"
                                                  data-punitid="{{ $ingredient->primary_unit_id }}"
                                                  data-sunitid="{{ $ingredient->secondary_unit_id }}" 
                                                  data-pcost="{{ $ingredient->purchase_price }}" 
                                                  data-scost="{{ $ingredient->s_unit_price }}" 
                                                  data-pabbr="{{ $ingredient->primary_unit_abbr }}" 
                                                  data-sabbr="{{ $ingredient->secondary_unit_abbr }}">
                                                    {{ $ingredient->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="ingredients[0][quantity_used]" class="recipe-qty" step="0.01" min="0" value="" required>

                                            <select name="ingredients[0][unit_id]"  class="unit-toggle">
                                                <option value="" class="d-none">Unit</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="unit-cost-display">₱0.00</span>
                                        <input type="hidden" class="active-unit-cost" value="0">
                                    </td>
                                    <td>
                                        <div>
                                            <span class="line-cost-display">&#8369;0.00</span>
                                            <button type="button" class="remove-row">
                                                <span class="icon-wrapper">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-120q-33 0-56.5-23.5T200-200v-520q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800h160q0-17 11.5-28.5T400-840h160q17 0 28.5 11.5T600-800h160q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM428.5-291.5Q440-303 440-320v-280q0-17-11.5-28.5T400-640q-17 0-28.5 11.5T360-600v280q0 17 11.5 28.5T400-280q17 0 28.5-11.5Zm160 0Q600-303 600-320v-280q0-17-11.5-28.5T560-640q-17 0-28.5 11.5T520-600v280q0 17 11.5 28.5T560-280q17 0 28.5-11.5ZM280-720v520-520Z"/></svg>
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot class="border-t">
                                <tr>
                                    <td colspan="3" class="border-r">
                                        <button class="btn" type="button" id="addIngredientBtn">
                                            <span class="icon-wrapper">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
                                            </span>
                                            <span>Add Ingredient</span>
                                        </button>
                                    </td>
                                    <td><span>Sub-Total</span></td>
                                    <td>
                                        <span id="sub-total-display">&#8369;0.00</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="tab-contents active-tab" id="recipe">
                        <div class="row">
                            <div class="input-group">
                                <span for="q-factor">Q-Factor (10%):</span>
                                <data id="q-factor-display" class="format-peso">0.00</data>
                            </div>

                             <div class="input-group">
                                <span>Suggested Price:</span>
                                <data id="suggested-price-display" class="format-peso">0.00</data>
                            </div>
                        </div>

                        <div class="row">
                           <div class="input-group">
                                <label for="margin">Target Margin (%)</label>
                                <input type="number" name="margin" id="margin" required value="30">
                            </div>

                            <div class="input-group">
                                <label for="final-price">Your Price</label>
                                <span class="icon-wrapper currency-symbol">&#8369;</span>
                                <input type="number" name="final_price" id="final-price" required>
                            </div>
                        </div>
                    </div>

                    <div id="others" class="tab-contents">
                        <div class="row">
                            <div class="input-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" placeholder="Enter Description..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                            <label for="image">Add Image</label>
                                <div id="upload-box" class="image-upload">
                                    <input type="file" accept="image/png,.png,image/jpeg,.jpeg,image/jpg,.jpg" name="img_url" id="image" style="display: none;">

                                    <span id="upload-icon" class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M771.5-691.5Q760-703 760-720v-40h-40q-17 0-28.5-11.5T680-800q0-17 11.5-28.5T720-840h40v-40q0-17 11.5-28.5T800-920q17 0 28.5 11.5T840-880v40h40q17 0 28.5 11.5T920-800q0 17-11.5 28.5T880-760h-40v40q0 17-11.5 28.5T800-680q-17 0-28.5-11.5ZM440-260q75 0 127.5-52.5T620-440q0-75-52.5-127.5T440-620q-75 0-127.5 52.5T260-440q0 75 52.5 127.5T440-260Zm0-80q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM120-120q-33 0-56.5-23.5T40-200v-480q0-33 23.5-56.5T120-760h126l50-54q11-12 26.5-19t32.5-7h205q17 0 28.5 11.5T600-800v60q0 25 17.5 42.5T660-680h20v20q0 25 17.5 42.5T740-600h60q17 0 28.5 11.5T840-560v360q0 33-23.5 56.5T760-120H120Z"/></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span>Cancel</span>
                </button>

                <button type="submit" class="btn">
                <span>Add Menu Dish</span>
            </button>
            </div>
        </form>
    </div>
</div>