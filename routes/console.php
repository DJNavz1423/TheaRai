<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $cutoffDate = now()->subDays(14);

    foreach ([
        'branch_inventory',
        'menu_items',
        'activity_logs',
        'ingredients',
        'menu_categories',
        'ingredient_categories',
        'units',
        'tables',
        'expenses',
        'users',
    ] as $table) {
        DB::table("laravel.{$table}")
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', $cutoffDate)
            ->delete();
    }
})->everyMinute();

Schedule::call(function () {
    $now = now('Asia/Manila');

    if ($now->hour < 7) {
        return;
    }

    $today = $now->toDateString();
    $cacheKey = 'reports.daily.last_sent_date';

    if (Cache::get($cacheKey) === $today) {
        return;
    }

    if (Artisan::call('reports:email', ['period' => 'daily']) !== 0) {
        throw new RuntimeException('The daily report email command failed.');
    }

    Cache::forever($cacheKey, $today);
})
    ->everyMinute()
    ->timezone('Asia/Manila');

Schedule::call(function () {
    $now = now('Asia/Manila');

    if ($now->hour < 7) {
        return;
    }

    $monthKey = $now->format('Y-m');
    $cacheKey = 'reports.monthly.last_sent_month';

    if (Cache::get($cacheKey) === $monthKey) {
        return;
    }

    if (Artisan::call('reports:email', ['period' => 'monthly']) !== 0) {
        throw new RuntimeException('The monthly report email command failed.');
    }

    Cache::forever($cacheKey, $monthKey);
})
    ->everyMinute()
    ->timezone('Asia/Manila');
