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
                <svg xmlns="http://www.w3.org/2000/svg"
                    height="24px"
                    viewBox="0 -960 960 960"
                    width="24px"
                    fill="#e3e3e3">
                    <path d="M346-160H240q-33 0-56.5-23.5T160-240v-106l-77-78q-11-12-17-26.5T60-480q0-15 6-29.5T83-536l77-78v-106q0-33 23.5-56.5T240-800h106l78-77q12-11 26.5-17t29.5-6q15 0 29.5 6t26.5 17l78 77h106q33 0 56.5 23.5T800-720v106l77 78q11 12 17 26.5t6 29.5q0 15-6 29.5T877-424l-77 78v106q0 33-23.5 56.5T720-160H614l-78 77q-12 11-26.5 17T480-60q-15 0-29.5-6T424-83l-78-77Zm134-160q33 0 56.5-23.5T560-400q0-33-23.5-56.5T480-480q-33 0-56.5 23.5T400-400q0 33 23.5 56.5T480-320Zm0-80q-8 0-14-6t-6-14q0-8 6-14t14-6q8 0 14 6t6 14q0 8-6 14t-14 6Z"/>
                </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg"
                    height="24px"
                    viewBox="0 -960 960 960"
                    width="24px"
                    fill="#e3e3e3">
                    <path d="M280-640q-33 0-56.5-23.5T200-720v-80q0-33 23.5-56.5T280-880h400q33 0 56.5 23.5T760-800v80q0 33-23.5 56.5T680-640H280Zm40-80h320q16 0 22.5-14.5T680-760q0-17-11.5-28.5T640-800H320q-17 0-28.5 11.5T280-760q11 11 17.5 25.5T320-720ZM160-80q-33 0-56.5-23.5T80-160v-40h800v40q0 33-23.5 56.5T800-80H160ZM80-240l139-313q10-22 30-34.5t43-12.5h376q23 0 43 12.5t30 34.5l139 313H80Zm260-80h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 0h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Zm120 0h40q8 0 14-6t6-14q0-8-6-14t-14-6h-40q-8 0-14 6t-6 14q0 8 6 14t14 6Z"/>
                </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg"
                    height="24px"
                    viewBox="0 -960 960 960"
                    width="24px"
                    fill="#e3e3e3">
                    <path d="M200-120v-120h-80v-80h160v200h-80Zm680 0h-80v-200h160v80h-80v120ZM120-640v-80h80v-120h80v200H120Zm600 0v-200h80v120h80v80H720ZM280-280v-400h400v400H280Zm80-80h240v-240H360v240Zm-240 80v-80h80v80h-80Zm600 0v-80h80v80h-80ZM240-640v-80h80v80h-80Zm400 0v-80h80v80h-80Z"/>
                </svg>
            </span>
        </a>

    </div>

    <div class="row mb-3">
        <h2 class="heading">Pending Orders</h2>
    </div>

    <div class="order-grid">

        @forelse($pendingOrders as $order)

            <div class="order-card">

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
                        <form
                            action="{{ route('waiter.orders.serve', $order->id) }}"
                            method="POST"
                        >
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

</div>
@endsection

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin/dashboard/dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
        <link rel="stylesheet" href="{{ asset('css/pos/qrOrders.css') }}">
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