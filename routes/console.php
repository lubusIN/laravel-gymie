<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mark subscriptions expired every day at 00:00
Schedule::command('gymie:subscriptions --mark-expired')
    ->dailyAt('00:00');

// Mark subscriptions expiring soon every day at 00:05
Schedule::command('gymie:subscriptions --mark-expiring')
    ->dailyAt('00:00');

// Mark invoices overdue every day at 00:00
Schedule::command('gymie:invoices --mark-overdue')
    ->dailyAt('00:00');
