<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily Sales Report - runs every evening at 6:00 PM
Schedule::command('sales:report')
    ->dailyAt('18:00')
    ->timezone('UTC');

// Low Stock Check - runs every hour to check for low stock products
Schedule::command('stock:check-low')
    ->hourly()
    ->timezone('UTC');
