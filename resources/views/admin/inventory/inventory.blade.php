@extends('layouts.admin')

@section('content')
<section id="inventory">
 <div class="container">
    <div class="row mb-4">
        <h1 class="heading">Item List (1)</h1>

        <button id="addButton" class="btn" type="button">

        <span class="icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg>
        </span>
        <span>Add New Item</span>
        </button>
    </div>
 </div>

 <div class="container">
    <div class="row table-controls mb-3">
        <div class="searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>

            <input type="text" id="searchBar" class="border" placeholder="Search products...">
        </div>
    </div>

    <div class="container table-container border">
        <table>
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Purchase Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>

            <tbody>
                @foreach($ingredients as $item)
                <tr>
                    <td>
                        @empty($item->item_code)
                        --
                        @else
                        <span class="item-data">{{ $item->item_code }}</span>
                        @endempty
                    </td>
                    <td>
                        <div class="d-flex item-group">
                            <span class="item-img"></span>
                            <span class="item-data">
                                {{ $item->name }}
                            </span>
                        </div>
                    </td>
                    <td><span class="item-data">{{ $item->category_name }}</span></td>
                    <td>
                        <div class="d-flex item-group">
                            <span class="item-data">&#8369;{{ $item->purchase_price }} / </span>
                            <span class="unit">{{ $item->primary_unit_abbr }}</span>
                        </div>
                    </td>
                    <td><span class="item-data">{{ $item->stock_quantity }}</span></td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </div>


<!-- 

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Inventory</h1>
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModal">
            <svg xmlns="http://www.w3.org" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
            Add Ingredient
        </button>
    </div>

     Table logic remains the same, but add "Edit/Delete" buttons in a new <td>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Stock (Conversion)</th>
                        <th>Costing</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ingredients as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category_name }}</td>
                        <td>
                            {{ number_format($item->stock_quantity, 2) }} {{ $item->primary_unit_abbr }}
                            <div class="small text-muted">1 {{ $item->primary_unit_abbr }} = {{ $item->conversion_factor }} {{ $item->secondary_unit_abbr }}</div>
                        </td>
                        <td>
                            ₱{{ number_format($item->purchase_price, 2) }} /{{ $item->primary_unit_abbr }}
                        </td>
                        <td class="text-end">
                            <form action="{{ url('/admin/inventory/'.$item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($errors->any())
    <div class="alert alert-danger pb-0">
        <ul class="small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        </div>
    </div>
</div>

Add Modal 
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ url('/admin/inventory') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5>Add New Ingredient</h5></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6"><label>Item Code</label><input type="text" name="item_code" class="form-control" required></div>
                    <div class="col-6"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-12">
                        <label>Category</label>
                        <select name="category_id" class="form-select">
                            @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Buy Unit (Primary)</label>
                        <select name="primary_unit_id" class="form-select">
                            @foreach($units as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Use Unit (Secondary)</label>
                        <select name="secondary_unit_id" class="form-select">
                            @foreach($units as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-4"><label>Factor</label><input type="number" step="0.01" name="conversion_factor" class="form-control" value="1"></div>
                    <div class="col-4"><label>Buy Price</label><input type="number" step="0.01" name="purchase_price" class="form-control" required></div>
                    <div class="col-4"><label>Opening Stock</label><input type="number" step="0.01" name="stock_quantity" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Ingredient</button>
            </div>
        </form>
    </div>
</div>
-->
</section>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard/inventory.css') }}">
    @endpush
@endonce