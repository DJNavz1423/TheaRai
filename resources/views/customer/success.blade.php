<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Order Successful</title>
    <style>
        :root { --primary: #e63946; --bg: #f4f6f8; --text: #2b2d42; --light: #ffffff; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        
        .success-header { text-align: center; margin-bottom: 20px; margin-top: 20px; }
        .success-header h1 { color: #2a9d8f; margin-bottom: 10px; font-size: 1.8rem; }
        .success-header p { color: #6c757d; font-size: 0.9rem; max-width: 400px; margin: 0 auto; line-height: 1.5; }

        /* The Digital Ticket Design */
        .receipt-card { background: var(--light); width: 100%; max-width: 400px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; position: relative; margin-bottom: 20px; }
        
        /* Zig-zag / Dashed top and bottom edges */
        .receipt-card::before { content: ""; position: absolute; top: -2px; left: 0; right: 0; border-top: 4px dashed #ccc; }
        .receipt-card::after { content: ""; position: absolute; bottom: -2px; left: 0; right: 0; border-bottom: 4px dashed #ccc; }

        .receipt-header { text-align: center; border-bottom: 1px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px; }
        .receipt-header h2 { margin: 0 0 5px 0; color: var(--primary); font-size: 1.4rem;}
        
        .order-meta { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; font-size: 0.85rem; color: #555; margin-bottom: 15px; border-bottom: 1px dashed #ccc; padding-bottom: 15px; }
        .meta-block { display: flex; flex-direction: column; }
        .meta-label { font-weight: bold; color: #888; text-transform: uppercase; font-size: 0.7rem; margin-bottom: 2px; }
        .meta-value { font-weight: bold; color: var(--text); font-size: 1.1rem; }

        .receipt-items { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 0.9rem; }
        .receipt-items th { text-align: left; padding-bottom: 8px; color: #888; border-bottom: 1px solid #eee; text-transform: uppercase; font-size: 0.75rem;}
        .receipt-items td { padding: 10px 0; vertical-align: top; border-bottom: 1px solid #f9f9f9; }
        .receipt-items .amt { text-align: right; font-weight: bold;}

        .receipt-total { border-top: 1px dashed #ccc; padding-top: 15px; margin-top: 10px; display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 900; color: var(--primary); }

        .btn { display: block; width: 100%; max-width: 400px; background: var(--primary); color: white; text-align: center; padding: 16px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-bottom: 10px; border: none; cursor: pointer; font-size: 1rem; box-sizing: border-box; }
        .btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); }
    </style>
</head>
<body>
    <div class="success-header">
        <h1>✅ Your order is placed!</h1>
        <p>Thank you for choosing us! Just a heads up — your final bill will be confirmed at the counter and may vary slightly from the estimated total shown. We can't wait to serve you!</p>
    </div>

    <div class="receipt-card">
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

    <a href="{{ url('/qr-menu?branch='.$order->branch_id.'&table='.$order->table_id) }}" class="btn">Go to menus</a>
    
    <button class="btn btn-outline" onclick="window.print()">Save Receipt Image</button>

</body>
</html>