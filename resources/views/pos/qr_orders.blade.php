@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.cashier')
@section('title', 'QR Orders')

@section('content')
<div class="container">
    <h2>QR Orders - {{ $activeBranch ? $activeBranch->name : 'Unknown Branch' }}</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-grid">
        @forelse($qrOrders as $order)
            <div class="order-card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; color: #ff6b6b;">Table {{ $order->table_number }}</h3>
                        <small>Receipt: {{ $order->receipt_no }} | Paid via: {{ strtoupper($order->payment_method) }}</small>
                    </div>
                    <div>
                        <form action="{{ url('/' . (auth()->user()->role === 'admin' ? 'admin' : 'cashier') . '/qr-orders/' . $order->id . '/serve') }}" method="POST">
                            @csrf
                            <button type="submit" style="background: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                                Mark as Served
                            </button>
                        </form>
                    </div>
                </div>
                
                <hr>
                
                <ul style="list-style: none; padding: 0;">
                    @foreach($order->items as $item)
                        <li style="font-size: 16px;"><strong>{{ $item->quantity }}x</strong> {{ $item->name }}</li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p>No pending QR orders at the moment.</p>
        @endforelse
    </div>
</div>
@endsection