<?php

namespace App\Console\Commands;

use App\Mail\ReportsEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class EmailReports extends Command
{
    protected $signature = 'reports:email {period=daily : The report period: daily or monthly}';

    protected $description = 'Email sales and inventory CSV reports';

    public function handle(): int
    {
        $period = $this->argument('period');

        if (! in_array($period, ['daily', 'monthly'], true)) {
            $this->error('The period must be daily or monthly.');

            return self::FAILURE;
        }

        $recipient = config('reports.email_to');

        if (! $recipient) {
            throw new RuntimeException('Set REPORT_EMAIL_TO before sending reports.');
        }

        $reportDate = Carbon::now('Asia/Manila');

        if ($period === 'daily') {
            $reportDate->subDay();
            $start = $reportDate->copy()->startOfDay();
            $end = $reportDate->copy()->endOfDay();
            $dateLabel = $reportDate->format('F j, Y');
        } else {
            $reportDate->subMonthNoOverflow();
            $start = $reportDate->copy()->startOfMonth();
            $end = $reportDate->copy()->endOfMonth();
            $dateLabel = $reportDate->format('F Y');
        }

        $startUtc = $start->copy()->utc();
        $endUtc = $end->copy()->utc();

        $sales = DB::table('laravel.orders as orders')
            ->leftJoin('laravel.branches as branches', 'orders.branch_id', '=', 'branches.id')
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startUtc, $endUtc])
            ->select(
                'orders.receipt_no',
                'orders.created_at',
                'branches.name as branch_name',
                'orders.payment_method',
                'orders.total_amount'
            )
            ->orderBy('orders.created_at')
            ->get();

        $inventory = DB::table('laravel.branch_inventory as bi')
            ->join('laravel.ingredients as i', 'bi.ingredient_id', '=', 'i.id')
            ->join('laravel.branches as b', 'bi.branch_id', '=', 'b.id')
            ->leftJoin('laravel.units as u', 'i.primary_unit_id', '=', 'u.id')
            ->whereNull('bi.deleted_at')
            ->whereNull('i.deleted_at')
            ->select(
                'b.name as branch_name',
                'i.name as ingredient_name',
                'bi.stock_quantity',
                'u.abbreviation as unit',
                'bi.purchase_price',
                'bi.alert_threshold'
            )
            ->orderBy('b.name')
            ->orderBy('i.name')
            ->get();

        $salesRows = $sales->map(fn ($order) => [
            $order->receipt_no,
            Carbon::parse($order->created_at)->setTimezone('Asia/Manila')->format('Y-m-d H:i:s'),
            $order->branch_name,
            $order->payment_method,
            $this->formatPeso($order->total_amount),
        ])->all();

        $salesRows[] = [
            'OVERALL TOTAL',
            '',
            '',
            '',
            $this->formatPeso($sales->sum('total_amount')),
        ];

        $inventoryRows = $inventory->map(fn ($item) => [
            $item->branch_name,
            $item->ingredient_name,
            $item->stock_quantity,
            $item->unit,
            $this->formatPeso($item->purchase_price),
            $this->formatPeso((float) $item->stock_quantity * (float) $item->purchase_price),
            $item->alert_threshold,
            $item->stock_quantity <= $item->alert_threshold ? 'LOW STOCK' : 'OK',
        ])->all();

        $inventoryRows[] = [
            'OVERALL TOTAL',
            '',
            '',
            '',
            '',
            $this->formatPeso($inventory->sum(
                fn ($item) => (float) $item->stock_quantity * (float) $item->purchase_price
            )),
            '',
            '',
        ];

        Mail::to($recipient)->send(new ReportsEmail(
            $period,
            $dateLabel,
            $this->toCsv(
                ['Receipt', 'Date (Manila)', 'Branch', 'Payment Method', 'Sales Total'],
                $salesRows
            ),
            $this->toCsv(
                ['Branch', 'Ingredient', 'Stock Quantity', 'Unit', 'Purchase Price', 'Stock Value', 'Alert Threshold', 'Stock Status'],
                $inventoryRows
            )
        ));

        $this->info("Sent the {$period} report for {$dateLabel} to {$recipient}.");

        return self::SUCCESS;
    }

    private function formatPeso(float|int|string|null $amount): string
    {
        return number_format((float) $amount, 2).' ₱';
    }

    private function toCsv(array $headers, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            throw new RuntimeException('Unable to create report CSV.');
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        if ($csv === false) {
            throw new RuntimeException('Unable to read generated report CSV.');
        }

        return $csv;
    }
}
