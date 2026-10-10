@extends('layouts.admin')

@section('title', 'Stock Audit')

@section('content')

<div class="container stock-audit-page">

    <div class="row mb-3">
        <div>
            <h1 class="heading">Stock Audit</h1>
            <p class="stock-audit-subtitle">
                Inventory movements and stock changes
            </p>
        </div>

        <div class="row heading-btn-row">

            <button id="stockAuditDownloadBtn" type="button" class="btn leftBtn" data-download-url="{{ route('admin.inventory.stock-audit.download') }}">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px"
                        fill="#e3e3e3">
                        <path d="M200-160h560q17 0 28.5 11.5T800-120q0 17-11.5 28.5T760-80H200q-17 0-28.5-11.5T160-120q0-17 11.5-28.5T200-160Zm262.5-109q-8.5-4-14.5-12L250-535q-15-20-4-42.5t36-22.5h78v-240q0-17 11.5-28.5T400-880h160q17 0 28.5 11.5T600-840v240h78q25 0 36 22.5t-4 42.5L512-281q-6 8-14.5 12t-17.5 4q-9 0-17.5-4Z"/>
                    </svg>
                </span>

                <span>Download CSV</span>
            </button>

        </div>
    </div>


    {{-- SUMMARY CARDS --}}
    <div class="stock-audit-summary">

        <div class="audit-summary-card border">
            <span class="audit-summary-label">
                Total Logs
            </span>

            <strong class="audit-summary-value">
                {{ number_format($summary->total_logs ?? 0) }}
            </strong>
        </div>


        <div class="audit-summary-card border audit-summary-in">
            <span class="audit-summary-label">
                Stock Added
            </span>

            <strong class="audit-summary-value">
                +{{ number_format($summary->total_stock_in ?? 0, 2) }}
            </strong>
        </div>


        <div class="audit-summary-card border audit-summary-out">
            <span class="audit-summary-label">
                Stock Used
            </span>

            <strong class="audit-summary-value">
                -{{ number_format($summary->total_stock_out ?? 0, 2) }}
            </strong>
        </div>


        <div class="audit-summary-card border">
            <span class="audit-summary-label">
                New Ingredients
            </span>

            <strong class="audit-summary-value">
                {{ number_format($summary->new_ingredients ?? 0) }}
            </strong>
        </div>


        <div class="audit-summary-card border">
            <span class="audit-summary-label">
                Restocks
            </span>

            <strong class="audit-summary-value">
                {{ number_format($summary->restocks ?? 0) }}
            </strong>
        </div>


        <div class="audit-summary-card border">
            <span class="audit-summary-label">
                Paid Orders
            </span>

            <strong class="audit-summary-value">
                {{ number_format($summary->orders ?? 0) }}
            </strong>
        </div>


        <div class="audit-summary-card border">
            <span class="audit-summary-label">
                Reductions
            </span>

            <strong class="audit-summary-value">
                {{ number_format($summary->reductions ?? 0) }}
            </strong>
        </div>

    </div>


    {{-- FILTERS --}}
    <div
        class="stock-audit-filters mb-3 row gap-2"
        data-filter-url="{{ route('admin.inventory.stock-audit') }}"
    >
      <div class="searchbox stock-audit-searchbox">
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M380-320q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l224 224q11 11 11 28t-11 28q-11 11-28 11t-28-11L532-372q-30 24-69 38t-83 14Zm0-80q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </span>
            <input
                type="text"
                id="search"
                class="border searchBar"
                value="{{ request('search') }}"
                placeholder="Search stock audit"
                aria-label="Search stock audit"
            >
        </div>

        <div class="filters stock-audit-filter-controls">
            <div class="audit-filter-group">
                <select id="branch_id" name="branch_id" class="ts-filter">

                    <option value="all" {{ request()->filled('branch_id') ? '' : 'selected' }}>
                        Branches
                    </option>

                    @foreach($branches as $branch)

                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>

                    @endforeach

                </select>
            </div>

            <div class="audit-filter-group">
                <select id="source_type" name="source_type" class="ts-filter">

                    <option value="all" {{ $sourceType === 'all' ? 'selected' : '' }}>
                        Movements
                    </option>

                    <option value="new_ingredient" {{ $sourceType === 'new_ingredient' ? 'selected' : '' }}>
                        New Ingredient
                    </option>

                    <option value="restock" {{ $sourceType === 'restock' ? 'selected' : '' }}>
                        Restock
                    </option>

                    <option value="order" {{ $sourceType === 'order' ? 'selected' : '' }}>
                        Paid Order
                    </option>

                    <option value="refund" {{ $sourceType === 'refund' ? 'selected' : '' }}>
                        Refund
                    </option>

                    <option value="manual_reduction" {{ $sourceType === 'manual_reduction' ? 'selected' : '' }}>
                        Manual Reduction
                    </option>

                    <option value="unclassified" {{ $sourceType === 'unclassified' ? 'selected' : '' }}>
                        Unclassified
                    </option>
                </select>
            </div>

            <form
                method="GET"
                action="{{ route('admin.inventory.stock-audit') }}"
                class="stock-audit-date-filter-form"
            >
                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                <input type="hidden" name="source_type" value="{{ $sourceType }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="audit-filter-group">
                    <label for="date_from">From date</label>
                    <input type="date" id="date_from" name="date_from" class="stock-audit-date-input" value="{{ $dateFrom }}" required>
                </div>

                <div class="audit-filter-group">
                    <label for="date_to">To date</label>
                    <input type="date" id="date_to" name="date_to" class="stock-audit-date-input" value="{{ $dateTo }}" required>
                </div>

                <button type="submit" class="btn stock-audit-date-apply">
                    Apply Dates
                </button>
            </form>
        </div>
    </div>

    {{-- AUDIT TABLE --}}
    <div class="container table-container border stock-audit-table-container">

        <table role="table">

            <thead>

                <tr>

                    <th>Date</th>
                    <th>Movement</th>
                    <th>Ingredient</th>
                    <th>Branch</th>
                    <th>Change</th>
                    <th>Balance</th>
                    <th>Reference</th>
                    <th>Remarks</th>

                </tr>

            </thead>


            <tbody role="rowgroup">

                @forelse($stockLogs as $log)

                    <tr class="stock-audit-row">

                        {{-- DATE --}}
                        <td role="cell">

                            <div class="audit-date">

                                <strong>
                                    {{ \Carbon\Carbon::parse($log->created_at)
                                        ->setTimezone('Asia/Manila')
                                        ->format('M j, Y') }}
                                </strong>

                                <span>
                                    {{ \Carbon\Carbon::parse($log->created_at)
                                        ->setTimezone('Asia/Manila')
                                        ->format('g:i A') }}
                                </span>

                            </div>

                        </td>


                        {{-- MOVEMENT --}}
                        <td role="cell">

                            @php

                                $movementType = $log->source_type === 'refund'
                                    || (
                                        $log->payment_status === 'refunded'
                                        && $log->refund_condition === 'wasted'
                                    )
                                    ? 'refund'
                                    : $log->source_type;

                                $movementLabel = match($movementType) {

                                    'new_ingredient'
                                        => 'New Ingredient',

                                    'restock'
                                        => 'Restock',

                                    'refund'
                                        => 'Refund',

                                    'order'
                                        => 'Paid Order',

                                    'manual_reduction'
                                        => 'Reduction',

                                    default
                                        => 'Unclassified',

                                };

                                $movementClass = match($movementType) {

                                    'new_ingredient'
                                        => 'movement-new',

                                    'restock'
                                        => 'movement-restock',

                                    'refund'
                                        => 'movement-refund',

                                    'order'
                                        => 'movement-order',

                                    'manual_reduction'
                                        => 'movement-reduction',

                                    default
                                        => 'movement-unknown',

                                };

                            @endphp

                            <span class="movement-badge {{ $movementClass }}">
                                {{ $movementLabel }}
                            </span>

                        </td>


                        {{-- INGREDIENT --}}
                        <td role="cell">

                            <div class="audit-ingredient">

                                <strong>
                                    {{ Str::title($log->ingredient_name ?? 'Unknown Ingredient') }}
                                </strong>

                                @if($log->unit)
                                    <span>
                                        Unit: {{ $log->unit }}
                                    </span>
                                @endif

                            </div>

                        </td>


                        {{-- BRANCH --}}
                        <td role="cell">

                            {{ $log->branch_name ?? 'Unknown Branch' }}

                        </td>


                        {{-- CHANGE --}}
                        <td role="cell">

                            @if($log->quantity_change > 0)

                                <span class="audit-quantity quantity-in">
                                    +{{ number_format($log->quantity_change, 2) }}
                                    {{ $log->unit }}
                                </span>

                            @else

                                <span class="audit-quantity quantity-out">
                                    {{ number_format($log->quantity_change, 2) }}
                                    {{ $log->unit }}
                                </span>

                            @endif

                        </td>


                        {{-- RUNNING BALANCE --}}
                        <td role="cell">

                            <span class="audit-balance">
                                {{ number_format($log->running_balance ?? 0, 2) }}
                                {{ $log->unit }}
                            </span>

                        </td>


                        {{-- REFERENCE --}}
                        <td role="cell">

                            @if($log->order_id && $movementType === 'refund')

                                <div class="audit-reference">

                                    <strong>
                                        {{ $log->receipt_no ?? 'Order #' . $log->order_id }}
                                    </strong>

                                    <span>
                                        Refund
                                        @if($log->refund_condition)
                                            · {{ $log->refund_condition === 'resellable' ? 'Resellable' : 'Wasted' }}
                                        @endif
                                    </span>

                                </div>

                            @elseif($log->source_type === 'order' && $log->order_id)

                                <div class="audit-reference">

                                    <strong>
                                        {{ $log->receipt_no ?? 'Order #' . $log->order_id }}
                                    </strong>

                                    <span>
                                        {{ ucwords($log->payment_method ?? 'Paid') }}
                                    </span>

                                </div>

                            @elseif($log->source_type === 'restock' && $log->expense_id)

                                <div class="audit-reference">

                                    <strong>
                                        Expense #{{ $log->expense_id }}
                                    </strong>

                                    @if($log->expense_description)
                                        <span>
                                            {{ $log->expense_description }}
                                        </span>
                                    @endif

                                </div>

                            @elseif($log->source_type === 'new_ingredient')

                                <div class="audit-reference">

                                    <strong>
                                        Ingredient #{{ $log->ingredient_id }}
                                    </strong>

                                    @if($log->expense_id)
                                        <span>
                                            Opening Expense #{{ $log->expense_id }}
                                        </span>
                                    @endif

                                </div>

                            @elseif($log->source_type === 'manual_reduction')

                                <span class="audit-reference-muted">
                                    Manual adjustment
                                </span>

                            @else

                                <span class="audit-reference-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- REMARKS --}}
                        <td role="cell">

                            <span class="audit-remarks">
                                {{ $log->remarks ?? '—' }}
                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-muted"
                            style="text-align: center; padding: 30px;"
                        >
                            No stock activity found for the selected filters.

                        </td>

                    </tr>

                @endforelse

                <tr class="stock-audit-search-empty" style="display: none;">
                    <td
                        colspan="8"
                        class="text-muted"
                        style="text-align: center; padding: 30px;"
                    >
                        No stock activity matches your search.
                    </td>
                </tr>

            </tbody>

        </table>


        @if($stockLogs->hasPages())

            <div class="stock-audit-pagination">

                {{ $stockLogs->links() }}

            </div>

        @endif

    </div>

</div>

@endsection


@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/tableControls.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelect.css') }}">
        <link rel="stylesheet" href="{{ asset('css/tomSelect/tomSelectCssConfig.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/filters.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/inventory/stockAudit.css') }}">
    @endpush
@endonce

@once
    @push('scripts')
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelect.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/tomSelect/tomSelectConfig.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/filters/tsStockAuditFilter.js') }}" defer></script>
        <script type="text/javascript" src="{{ asset('js/dashboard/inventory/stockAuditDownload.js') }}" defer></script>
    @endpush
@endonce