<div id="addModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form action="{{ url('/admin/inventory') }}" method="POST" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h2>Add New Item</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('addModal').style.display='none'">
                    <span class="icon-wrapper close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">   
                    <div class="input-group">
                        <label for="name">Item Name</label>
                        <input type="text" name="name" id="name" placeholder="e.g., Chicken" required>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="category">Item Category</label>
                        <select name="category_id" id="category" class="unit-selector" placeholder="Select a category..." required>
                            <option value="" class="d-none"></option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                            <label for="item-code">Item Code</label>
                            <input type="text" name="item_code" id="item-code" placeholder="Enter Code (Optional)">
                    </div>
                </div>
                
                <div class="tab-container">
                    <div class="tab-titles mt-4 mb-3">
                        <h3 class="tab-links active-link underline-fullwidth" onclick="openTab(event, 'stocks')">Stock Details</h3>
                        <h3 class="tab-links underline-fullwidth" onclick="openTab(event, 'others')">Others</h3>
                    </div>

                    <div class="tab-contents active-tab" id="stocks">
                        <div class="row">
                            <div class="input-group">
                               <label for="primary-unit">Primary Unit</label>
                                <select id="primary_unit" name="primary_unit_id" class="unit-selector" placeholder="e.g., Kilogram" required>
                                    <option value="" class="d-none"></option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" data-abbr="{{ $unit->abbreviation }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="secondary-unit">Secondary Unit</label>
                                <select id="secondary_unit" name="secondary_unit_id" class="unit-selector" placeholder="e.g., Grams" required>
                                    <option value="" class="d-none"></option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>   
                            </div>

                            <div class="input-group">
                                <label for="conversion-factor">Conversion Rate</label>
                                <input type="number" step="0.01" min="0" name="conversion_factor" id="conversion-factor" placeholder="e.g., 1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="branch_id">Assign Initial Stock To Branch (If applicable)</label>
                                <select name="branch_id" class="unit-selector" required>
                                    <option value="" selected disabled>Select Branch...</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="stock-qty">Opening Stock</label>
                                <input type="number" step="0.01" min="0" name="stock_quantity" id="stock-qty">
                                <span id="add_opening_stock_unit" class="icon-wrapper unit-abbr text-muted"></span>
                            </div>

                            <div class="input-group">
                                <label for="purchase-price">Purchase Price</label>
                                <span class="icon-wrapper currency-symbol">&#8369;</span>
                                <input type="number" step="0.01" min="0" name="purchase_price" id="purchase-price" required>
                                <span class="text-muted icon-wrapper unit-abbr" id="add_purchase_price_unit"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="fund-source">Payment Source</label>
                                <select name="fund_source" id="fund-source" class="unit-selector" required>
                                    <option value="cash_in_hand">System Cash</option>
                                    <option value="external_cash">External Cash</option>
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="threshold">Low Stock Alert</label>
                                <input type="number" step="0.01" min="0" name="alert_threshold" id="threshold" placeholder="Enter Stock Quantity">
                                <span class="p_unit_abbreviation"></span>
                            </div>
                        </div>
                    </div>

                    <div class="tab-contents" id="others">
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
                    <span>Add Item</span>
                </button>
            </div>
        </form>
    </div>
 </div>