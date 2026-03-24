@extends('layouts.admin')


@section('title', 'Inventory')

@section('content')
<section id="inventory">
 <div class="container">
    <div class="row mb-4">
        <h1 class="heading">Item List ({{ count($ingredients) }})</h1>

        <div class="row heading-btn-row">
            <a href="#" class="btn">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M433-80q-27 0-46.5-18T363-142l-9-66q-13-5-24.5-12T307-235l-62 26q-25 11-50 2t-39-32l-47-82q-14-23-8-49t27-43l53-40q-1-7-1-13.5v-27q0-6.5 1-13.5l-53-40q-21-17-27-43t8-49l47-82q14-23 39-32t50 2l62 26q11-8 23-15t24-12l9-66q4-26 23.5-44t46.5-18h94q27 0 46.5 18t23.5 44l9 66q13 5 24.5 12t22.5 15l62-26q25-11 50-2t39 32l47 82q14 23 8 49t-27 43l-53 40q1 7 1 13.5v27q0 6.5-2 13.5l53 40q21 17 27 43t-8 49l-48 82q-14 23-39 32t-50-2l-60-26q-11 8-23 15t-24 12l-9 66q-4 26-23.5 44T527-80h-94Zm7-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
                </span>
            </a>

            <button id="addButton" class="btn" type="button">
            <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
        </span>
        <span>Add New Item</span>
        </button>
        </div>
    </div>
 </div>

 <div class="container mb-3">
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="ingredientSearch" class="border searchBar" placeholder="Search products...">
        </div>
    </div>

    <div class="container table-container border">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Category</th>
                    <th>Purchase Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>

            <tbody>
                @foreach($ingredients as $item)
                <tr>
                    <td>
                        <div class="d-flex item-group">
                            <span class="item-img">
                                @empty($item->img_url)
                                <span>
                                    {{ $item->name[0] }}
                                </span>
                                @else
                                <img src="{{ $item->img_url }}" alt="Item Image">
                                @endempty
                            </span>
                            <span class="item-data">
                                {{ $item->name }}
                            </span>
                        </div>
                    </td>

                    <td>
                        @empty($item->item_code)
                        --
                        @else
                        <span class="item-data">{{ $item->item_code }}</span>
                        @endempty
                    </td>

                    <td><span class="item-data">{{ $item->category_name }}</span></td>
                    <td>
                        <span class="item-data">&#8369;{{ $item->purchase_price }} / </span>
                        <span class="item-data">{{ $item->primary_unit_abbr }}</span>
                    </td>
                    <td>
                        <div class="d-flex item-group">
                            <span class="item-data">{{ $item->stock_quantity }} {{ $item->primary_unit_abbr }} </span>

                            <button class="more-actions">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-160q-33 0-56.5-23.5T400-240q0-33 23.5-56.5T480-320q33 0 56.5 23.5T560-240q0 33-23.5 56.5T480-160Zm0-240q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm0-240q-33 0-56.5-23.5T400-720q0-33 23.5-56.5T480-800q33 0 56.5 23.5T560-720q0 33-23.5 56.5T480-640Z"/></svg>
                                </span>
                            </button>
                        </div>
                    </td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </div>

 <!-- Add item modal -->
 <div id="addModal" class="modal">
    <div class="modal-dialog">
        <form action="{{ url('/admin/inventory') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Add New Item</h2>

                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
                </span>
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
                        <select name="category_id" id="category" class="unit-selector" placeholder="Search or select a category...">
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
                

                <div class="row tab-container">
                    <div class="tab-titles mt-4 mb-1">
                        <span class="tab-title">Stock Details</span>
                        <span class="tab-title">Others</span>
                    </div>

                    <div class="tab-contents active-tab" id="stocks">
                        <div class="row">
                            <div class="input-group">
                               <label for="primary-unit">Primary Unit</label>
                                <select id="primary_unit" name="primary_unit_id" class="unit-selector" placeholder="Search e.g., Kilogram">
                                    <option value="" class="d-none"></option>

                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="secondary-unit">Secondary Unit</label>
                                <select id="secondary_unit" name="secondary_unit_id"         class="unit-selector" placeholder="Search e.g., Grams">
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
                                <label for="stock-qty">Opening Stock</label>
                                <input type="number" step="0.01" min="0" name="stock_quantity" id="stock-qty" value="0">
                            </div>

                            <div class="input-group">
                                <label for="purchase-price">Purchase Price</label>
                                <span class="icon-wrapper">&#8369;</span>
                                <input type="number" step="0.01" min="0" name="purchase_price" id="purchase-price" required>
                                <span></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label for="threshold">Low Stock Alert</label>
                                <input type="number" step="0.01" min="0" name="alert_threshold" id="threshold" placeholder="Enter Stock Quantity">
                            </div>
                        </div>
                    </div>

                    <div class="tab-contents d-none" id="others">
                        <div class="row">
                            <div class="input-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" placeholder="Enter Description..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                            <label for="image">Add Image</label>
                                <div class="image-upload">
                                    <input type="file" accept="image/png,.png,image/jpeg,.jpeg,image/jpg,.jpg" multiple name="img_url" id="image" class="d-none">

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
                <button type="submit" class="btn">
                    <span>Add Item</span>
                </button>
            </div>
        </form>
    </div>
 </div>
<!-- 

   
                        <td class="text-end">
                            <form action="{{ url('/admin/inventory/'.$item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this?')">Delete</button>
                            </form>
                        </td>
          
            @if ($errors->any())
    <div class="alert alert-danger pb-0">
        <ul class="small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
-->
</section>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dashboard/inventory.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dashboard/table.css') }}">
    @endpush
@endonce

@once
    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/dashboard/searchDataSet.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
        <script>
    document.querySelectorAll('.unit-selector').forEach((el) => {
        new TomSelect(el, {
            create: false,
            maxItems: 1,
            placeholder: el.getAttribute('placeholder'),
            closeAfterSelect: true, 
            
            onItemAdd: function() {
                this.blur(); 
                this.wrapper.classList.remove('is-typing'); 
            },

            onChange: function(value) {
                if (!value || value === "") {
                    this.clear(); 
                    const emptyItem = this.control.querySelector('.item');
                    if (emptyItem) emptyItem.remove();
                    this.wrapper.classList.remove('is-typing');
                }
            },

            onBlur: function() {
                this.wrapper.classList.remove('is-typing');
                this.control_input.value = ''; // Instantly clears leftover text
            },

            onInitialize: function() {
                // 1. NATIVE INSTANT TYPING LISTENER (Bypasses Tom Select's lag)
                this.control_input.addEventListener('input', () => {
                    if (this.control_input.value.length > 0) {
                        this.wrapper.classList.add('is-typing');
                    } else {
                        this.wrapper.classList.remove('is-typing');
                    }
                });

                // 2. FORCE CLOSE if they click the already-selected option
                this.dropdown.addEventListener('click', (e) => {
                    const option = e.target.closest('.option');
                    if (option && option.classList.contains('selected')) {
                        this.blur();
                    }
                });
            },

            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results">No units found for "' + escape(data.input) + '"</div>';
                },
            }
        });
    });
</script>
    @endpush
@endonce