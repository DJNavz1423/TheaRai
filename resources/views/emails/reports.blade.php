<!DOCTYPE html>
<html lang="en">
<body>
    <p>Hello,</p>
    <p>Attached are the {{ $period }} sales report for {{ $dateLabel }} and the current inventory snapshot.</p>
    <p>The CSV attachments can be opened in Excel.</p>
    <p>— {{ config('app.name') }}</p>
</body>
</html>
