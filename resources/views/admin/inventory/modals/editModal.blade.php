<div id="editModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h2>Edit Item</h2>

                <button type="button" class="btn close-btn" onclick="document.getElementById('editModal').style.display='none'">
                    <span class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                    </span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <label for="edit_name">Item Name</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                </div>

                <div class="row">
                    <div class="input-group">
                        <label for="edit_category">Item Category</label>
                        <select name="category_id" id="edit_category" class="unit-selector" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="edit_item_code">Item Code</label>
                        <input type="text" name="item_code" id="edit_item_code">
                    </div>
                </div>

                <div class="row tab-container">
                    <div class="row tab-titles mt-4 mb-1">
                        <h3 class="tab-title">Stock Details</h3>
                        <h3 class="tab-title">Others</h3>
                    </div>

                    <div class="tab-contents active-tab" id="edit_stocks">
                        <div class="row">
                            <div class="input-group">
                                <label for="edit_primary_unit">Primary Unit</label>
                                <select name="primary_unit_id" id="edit_primary_unit" class="unit-selector" required>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" data-abbr="{{ $unit->abbreviation }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="edit_secondary_unit">Secondary Unit</label>
                                <select id="edit_secondary_unit" name="secondary_unit_id" class="unit-selector">
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>   
                            </div>

                            <div class="input-group">
                                <label for="edit_conversion_factor">Conversion Rate</label>
                                <input type="number" step="0.01" min="0" name="conversion_factor" id="edit_conversion_factor" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="edit_stock_qty">Stock Quantity</label>
                                <input type="number" step="0.01" min="0" name="stock_quantity" id="edit_stock_qty">
                            </div>

                            <div class="input-group">
                                <label for="edit_purchase_price">Purchase Price</label>
                                <span class="icon-wrapper">&#8369;</span>
                                <input type="number" step="0.01" min="0" name="purchase_price" id="edit_purchase_price" required>
                                <span class="text-muted" id="edit_purchase_price_unit"></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="edit_threshold">Low Stock Alert</label>
                                <input type="number" step="0.01" min="0" name="alert_threshold" id="edit_threshold">
                            </div>
                        </div>
                    </div>

                        <div class="tab-contents" id="edit_others">
                            <div class="row">
                                <div class="input-group">
                                    <label for="edit_description">Description</label>
                                    <textarea name="description" id="edit_description"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-group">
                                    <label for="edit_image">Update Image</label>
                                    <div class="image-upload">
                                        <input type="file" accept="image/png,.png,image/jpeg,.jpeg,image/jpg,.jpg" name="img_url" id="edit-image" style="display: none;">
                                        <span class="icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440ZM120-120q-33 0-56.5-23.5T40-200v-480q0-33 23.5-56.5T120-760h126l50-54q11-12 26.5-19t32.5-7h165q17 0 28.5 11.5T560-800q0 17-11.5 28.5T520-760H355l-73 80H120v480h640v-320q0-17 11.5-28.5T800-560q17 0 28.5 11.5T840-520v320q0 33-23.5 56.5T760-120H120Zm640-640h-40q-17 0-28.5-11.5T680-800q0-17 11.5-28.5T720-840h40v-40q0-17 11.5-28.5T800-920q17 0 28.5 11.5T840-880v40h40q17 0 28.5 11.5T920-800q0 17-11.5 28.5T880-760h-40v40q0 17-11.5 28.5T800-680q-17 0-28.5-11.5T760-720v-40ZM440-260q75 0 127.5-52.5T620-440q0-75-52.5-127.5T440-620q-75 0-127.5 52.5T260-440q0 75 52.5 127.5T440-260Zm0-80q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29Z"/></svg>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="modal-footer">
                <button type="button" class="btn" onclick="document.getElementById('editModal').style.display='none'">
                    <span>Cancel</span>
                </button>

                <button type="submit" class="btn">
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
 </div>