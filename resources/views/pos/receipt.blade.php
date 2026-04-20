<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .border-top { border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 3px 0; }
        .bold { font-weight: bold; }
  </style>

  <title>{{ $order->branch_name ?? 'TheaRai Eatery' }} | Order Receipt</title>
</head>

<body>
  <div class="text-center border-bottom">
        <h2 style="margin: 5px 0;">{{ $order->branch_name ?? 'TheaRai Eatery' }}</h2>
        <p style="margin: 2px 0;">{{ $order->branch_address ?? 'Davao City' }}</p>
        <p style="margin: 2px 0;">Tel: 0912 345 6789</p>
    </div>

    <div style="margin-bottom: 10px;">
        <p style="margin: 2px 0;">Date: {{ \Carbon\Carbon::parse($order->created_at)->format('m/d/Y h:i A') }}</p>
        <p style="margin: 2px 0;">Order #: {{ $order->receipt_no }}</p>
        <p style="margin: 2px 0;">Payment: {{ ucfirst($order->payment_method) }}</p>
    </div>

    <table class="border-bottom border-top">
        <thead>
            <tr>
                <th style="text-align: left;">Qty</th>
                <th style="text-align: left;">Item</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td style="vertical-align: top;">{{ $item->quantity }}x</td>
                <td style="vertical-align: top;">{{ $item->name }}</td>
                <td class="text-right" style="vertical-align: top;">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="bold">Total Due:</td>
            <td class="text-right bold">P{{ number_format($order->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Cash Tendered:</td>
            <td class="text-right">P{{ number_format($order->cash_tendered, 2) }}</td>
        </tr>
        <tr>
            <td>Change:</td>
            <td class="text-right">P{{ number_format($order->change_amount, 2) }}</td>
        </tr>
    </table>

    <div class="text-center border-top" style="margin-top: 15px;">
        <p style="margin: 5px 0;">Thank you for your order!</p>
        <p style="margin: 5px 0;">Please come again.</p>
    </div>
</body>
</html>