<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cleanup temporary delivery orders older than 24 hours and send reminder SMS
use App\Console\Commands\CleanupUnpaidOrders;
use App\Console\Commands\CheckShipmentAcceptanceWindow;
use App\Console\Commands\EscalateUnassignedShipments;
use App\Console\Commands\HandleTZeroCrisisFallback;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CleanupUnpaidOrders::class)->everyMinute();
Schedule::command(CheckShipmentAcceptanceWindow::class)->everyMinute();
Schedule::command(EscalateUnassignedShipments::class)->everyMinute();
Schedule::command(HandleTZeroCrisisFallback::class)->everyMinute();
