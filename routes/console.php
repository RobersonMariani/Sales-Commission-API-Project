<?php

declare(strict_types=1);

use App\Api\Modules\Sale\Jobs\SendDailyAdminSummaryJob;
use App\Api\Modules\Sale\Jobs\SendDailySellerCommissionJob;
use App\Models\Seller;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $date = now()->toDateString();

    Seller::query()->each(function (Seller $seller) use ($date) {
        SendDailySellerCommissionJob::dispatch($seller, $date);
    });

    SendDailyAdminSummaryJob::dispatch($date);
})->dailyAt('23:59')->name('send-daily-commission-emails');
