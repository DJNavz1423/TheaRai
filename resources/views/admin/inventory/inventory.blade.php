@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Inventory</h1>
        <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addModal">
            <svg xmlns="http://www.w3.org" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
            Add Ingredient
        </button>
    </div>

    <!-- Table logic remains the same, but add "Edit/Delete" buttons in a new <td> -->
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

<!-- Add Modal -->
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
@endsection