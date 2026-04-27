@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.cashier')
@section('title', 'QR Orders')

@section('content')
<div class="container">
    <h1 class="heading mb-4">QR Orders - {{ $activeBranch ? $activeBranch->name : 'Unknown Branch' }}</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-grid">
        @forelse($qrOrders as $order)
            <div class="order-card">
                <div class="card-header border-b">
                    <div>
                        <h2>Table {{ $order->table_number }}</h2>
                        <p>Receipt: {{ $order->receipt_no }}</p>
                        <p>Paid via: {{ strtoupper($order->payment_method) }}</p>
                    </div>
                    <div>
                        <form action="{{ url('/' . (auth()->user()->role === 'admin' ? 'admin' : 'cashier') . '/qr-orders/' . $order->id . '/serve') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn">Mark as Served</button>
                        </form>
                    </div>
                </div>
                
                <ul class="order-list">
                    @foreach($order->items as $item)
                        <li>
                          <p>
                           <strong>{{ $item->quantity }}x</strong>  {{ $item->name }}
                          </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p>No pending QR orders at the moment.</p>
        @endforelse
    </div>
</div>
@endsection

@once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/sectionHeading.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pos/qrOrders.css') }}">
    @endpush
@endonce