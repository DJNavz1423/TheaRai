@extends('layouts.waiter')

@section('title', 'Dashboard')

@section('content')
<div class="container">

    <div class="row mb-3">
        <h1 class="heading">Welcome back {{ $user->name }}</h1>
    </div>

    <div class="kpi-grid d-grid mb-4">

        <a class="kpi-card border">
            <div class="kpi-content">
                <h3 class="kpi-label">Pending Orders</h3>

                <data
                    id="pending-orders-count"
                    value="{{ $pendingCount }}"
                    class="kpi-value"
                >
                    {{ $pendingCount }}
                </data>

                <p class="text-muted kpi-description">
                    Orders waiting to be served
                </p>
            </div>

            <span class="icon-wrapper kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m326-90-58-98-110-24q-15-3-24-15.5t-7-27.5l11-113-75-86q-10-11-10-26t10-26l75-86-11-113q-2-15 7-27.5t24-15.5l110-24 58-98q8-13 22-17.5t28 1.5l104 44 104-44q14-6 28-1.5t22 17.5l58 98 110 24q15 3 24 15.5t7 27.5l-11 113 75 86q10 11 10 26t-10 26l-75 86 11 113q2 15-7 27.5T802-212l-110 24-58 98q-8 13-22 17.5T584-74l-104-44-104 44q-14 6-28 1.5T326-90Zm52-72 102-44 104 44 56-96 110-26-10-112 74-84-74-86 10-112-110-24-58-96-102 44-104-44-56 96-110 24 10 112-74 86 74 84-10 114 110 24 58 96Zm102-318Zm28.5 188.5Q520-303 520-320t-11.5-28.5Q497-360 480-360t-28.5 11.5Q440-337 440-320t11.5 28.5Q463-280 480-280t28.5-11.5Zm0-160Q520-463 520-480v-160q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640v160q0 17 11.5 28.5T480-440q17 0 28.5-11.5Z"/></svg>
            </span>
        </a>

        <a class="kpi-card border">
            <div class="kpi-content">
                <h3 class="kpi-label">POS Orders</h3>

                <data
                    value="{{ $posCount }}"
                    class="kpi-value"
                >
                    {{ $posCount }}
                </data>

                <p class="text-muted kpi-description">
                    Counter orders
                </p>
            </div>

            <span class="icon-wrapper kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M280-640q-33 0-56.5-23.5T200-720v-80q0-33 23.5-56.5T280-880h400q33 0 56.5 23.5T760-800v80q0 33-23.5 56.5T680-640H280Zm40-80h320q16 0 22.5-14.5T680-760q0-17-11.5-28.5T640-800H320q-17 0-28.5 11.5T280-760q11 11 17.5 25.5T320-720ZM160-80q-33 0-56.5-23.5T80-160v-40h800v40q0 33-23.5 56.5T800-80H160ZM80-240l139-313q10-22 30-34.5t43-12.5h376q23 0 43 12.5t30 34.5l139 313H80Zm260-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 160h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm0-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Z"/></svg>
            </span>
        </a>

        <a class="kpi-card border">
            <div class="kpi-content">
                <h3 class="kpi-label">QR Orders</h3>

                <data
                    value="{{ $qrCount }}"
                    class="kpi-value"
                >
                    {{ $qrCount }}
                </data>

                <p class="text-muted kpi-description">
                    Table QR orders
                </p>
            </div>

            <span class="icon-wrapper kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-560v-240q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v240q0 17-11.5 28.5T400-520H160q-17 0-28.5-11.5T120-560Zm80-40h160v-160H200v160Zm-80 440v-240q0-17 11.5-28.5T160-440h240q17 0 28.5 11.5T440-400v240q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-40h160v-160H200v160Zm320-360v-240q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v240q0 17-11.5 28.5T800-520H560q-17 0-28.5-11.5T520-560Zm80-40h160v-160H600v160Zm160 480v-80h80v80h-80ZM520-360v-80h80v80h-80Zm80 80v-80h80v80h-80Zm-80 80v-80h80v80h-80Zm80 80v-80h80v80h-80Zm80-80v-80h80v80h-80Zm0-160v-80h80v80h-80Zm80 80v-80h80v80h-80Z"/></svg>
            </span>
        </a>
    </div>

    <div class="row mb-3 mt-3">
        <h2 class="heading">Pending Orders</h2>
    </div>

    <div class="order-grid">

        @forelse($pendingOrders as $order)

            <div class="order-card"
                data-order-source="{{ $order->order_source }}"
                data-order-id="{{ $order->id }}">

                <div class="card-header border-b">

                    <div>
                        @if($order->order_source === 'qr')
                            <h2>
                                Table {{ $order->table_number ?? 'Unknown' }}
                            </h2>
                        @else
                            <h2>POS Order</h2>
                        @endif

                        <p>
                            Receipt: {{ $order->receipt_no }}
                        </p>

                        <p>
                            Order Type:
                            {{ strtoupper($order->order_source) }}
                        </p>

                        <p>
                            Paid via:
                            {{ strtoupper($order->payment_method) }}
                        </p>
                    </div>

                    <div>
                        <form action="{{ route('waiter.orders.serve', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn">
                                Mark as Served
                            </button>
                        </form>
                    </div>
                </div>

                <ul class="order-list">

                    @foreach($order->items as $item)

                        <li>
                            <div class="item-group">

                                <div class="item-image">

                                    @if($item->img_url)

                                        <img
                                            src="{{ $item->img_url }}"
                                            alt="{{ $item->name }}"
                                        >

                                    @else

                                        <span class="placeholder">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </span>

                                    @endif

                                </div>

                                <p>
                                    <strong>
                                        {{ $item->quantity }}x
                                    </strong>
                                    {{ $item->name }}
                                </p>

                            </div>
                        </li>

                    @endforeach

                </ul>

            </div>

        @empty

            <p class="text-muted">
                No pending orders at the moment.
            </p>

        @endforelse

    </div>

    <div class="row mb-3 mt-4">
        <h2 class="heading">Today's Transactions</h2>
    </div>

    <div class="container table-container border mb-5">
        <table role="table">
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Order Type</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Time</th>
                </tr>
            </thead>

            <tbody role="rowgroup">
                @forelse($todayTransactions as $transaction)

                    <tr role="row">

                        <td role="cell">
                            {{ $transaction->receipt_no }}
                        </td>

                        <td role="cell">
                            {{ strtoupper($transaction->order_source ?? 'POS') }}
                        </td>

                        <td role="cell">
                            ₱{{ number_format($transaction->total_amount, 2) }}
                        </td>

                        <td role="cell">
                            {{ ucwords($transaction->payment_method ?? 'Unknown') }}
                        </td>

                        <td role="cell">
                            {{ ucwords($transaction->payment_status ?? 'Unknown') }}
                        </td>

                        <td role="cell">
                            {{ \Carbon\Carbon::parse($transaction->created_at)
                                ->setTimezone('Asia/Manila')
                                ->format('g:i A') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="text-muted">
                            No transactions today.
                        </td>
                    </tr>

                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin/dashboard/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
        <link rel="stylesheet" href="{{ asset('css/pos/qrOrders.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/transaction/transactionDetails.css') }}">

        <style>
            .row{
                justify-content: flex-start;
            }
        </style>
    @endpush
@endonce

@once
    @push('scripts')

        @if(session('error'))
            <script>
                alert("🚨 ERROR: {{ session('error') }}");
            </script>
        @endif

        @if(session('success'))
            <script>
                alert("✅ SUCCESS: {{ session('success') }}");
            </script>
        @endif

    @endpush
@endonce