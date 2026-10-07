<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (){
    $cutoffDate = now()->subDays(14);

    DB::table('laravel.ingredients')
        ->where('deleted_at', '<', $cutoffDate)
        ->delete();

    DB::table('laravel.menu_items')
        ->where('deleted_at', '<', $cutoffDate)
        ->delete();

    DB::table('laravel.users')
        ->where('deleted_at', '<', $cutoffDate)
        ->delete();
})->dailyAt('00:00');

Schedule::command('reports:email daily')
    ->dailyAt('07:00')
    ->timezone('Asia/Manila');

Schedule::command('reports:email monthly')
    ->monthlyOn(1, '07:00')
    ->timezone('Asia/Manila');