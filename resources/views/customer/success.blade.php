<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <link rel="stylesheet" href="{{  asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/success.css') }}">
    
    @stack('styles')

    <style>
        @font-face {
            font-family: 'MeriendaCanvas';
            src: url('{{ asset("fonts/merienda/Merienda-Black.ttf") }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
    </style>
    
    <title>Order Successful</title>
</head>
<body>
    @include('loader')

    <div class="success-header">
        <h1>
            <span class="icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m424-424-86-86q-11-11-28-11t-28 11q-11 11-11 28t11 28l114 114q12 12 28 12t28-12l226-226q11-11 11-28t-11-28q-11-11-28-11t-28 11L424-424ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Z"/></svg>
            </span>
            Your order is placed!
        </h1>

        <p>Thank you for choosing us! Just a heads up — your final bill will be confirmed at the counter and may vary slightly from the estimated total shown. We can't wait to serve you!</p>
    </div>

    <div class="receipt-card" id="receiptToDownload">
        <div class="receipt-header">
            <h2>{{ $order->branch_name }}</h2>
            <p style="margin:0; font-size:0.9rem; color:#666;">Table <strong>{{ $order->table_number ?? $order->table_id }}</strong></p>
        </div>

        <div class="order-meta">
            <div class="meta-block">
                <span class="meta-label">Your Order ID</span>
                <span class="meta-value">{{ $order->receipt_no }}</span>
            </div>
            <div class="meta-block" style="text-align: right;">
                <span class="meta-label">Payment Mode</span>
                <span class="meta-value">{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="meta-block" style="width: 100%;">
                <span class="meta-label">Date</span>
                <span class="meta-value">{{ \Carbon\Carbon::parse($order->created_at)->timezone('Asia/Manila')->format('d/m/Y h:i A') }}</span>
            </div>
        </div>

        <table class="receipt-items">
            <thead>
                <tr>
                    <th>Details</th>
                    <th class="amt">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->quantity }}x {{ $item->name }}</td>
                    <td class="amt">₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-total">
            <span>Total amount</span>
            <span>₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>

    <button class="btn btn-outline" id="downloadBtn" onclick="downloadReceipt()">Download Receipt Image</button>

    <a href="{{ url('/qr-menu?branch='.$order->branch_id.'&table='.$order->table_id) }}" class="btn">Go to menus</a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
    
    <script>
async function downloadReceipt() {
    const btn = document.getElementById('downloadBtn');
    const receipt = document.getElementById('receiptToDownload');

    const originalText = btn.innerText;
    btn.innerText = "Generating...";
    btn.disabled = true;

    try {
        if (document.fonts) {
            await document.fonts.ready;
        }

        // --- THE FIX: The "Dummy" Pass ---
        // This forces the browser to fetch and cache the custom fonts
        await htmlToImage.toPng(receipt, { cacheBust: true });

        // --- The Real Pass ---
        // Now that the font is cached, we take the real screenshot
        const dataUrl = await htmlToImage.toPng(receipt, {
            backgroundColor: '#ffffff',
            pixelRatio: 2,
            style: {
                margin: '0', 
                transform: 'none'
            }
        });

        const link = document.createElement("a");
        link.href = dataUrl;
        link.download = `TheaRai_Receipt_{{ $order->receipt_no }}.png`;
        link.click();

    } catch (err) {
        console.error(err);
        alert("Failed to download receipt.");
    }

    btn.innerText = originalText;
    btn.disabled = false;
}
</script>
    
    @stack('scripts')
</body>
</html>