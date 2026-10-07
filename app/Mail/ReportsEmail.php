<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $period,
        public string $dateLabel,
        public string $salesCsv,
        public string $inventoryCsv
    ) {}

    public function build(): static
    {
        return $this
            ->subject(ucfirst($this->period) . " Sales and Inventory Report - {$this->dateLabel}")
            ->view('emails.reports')
            ->attachData($this->salesCsv, "{$this->period}-sales.csv", [
                'mime' => 'text/csv; charset=UTF-8',
            ])
            ->attachData($this->inventoryCsv, "{$this->period}-inventory.csv", [
                'mime' => 'text/csv; charset=UTF-8',
            ]);
    }
}
