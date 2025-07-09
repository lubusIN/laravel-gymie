<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mark subscriptions expired every day at 00:00
Schedule::command('gymie:subscriptions --mark-expired')
    ->everyMinute();

// Mark subscriptions expiring soon every day at 00:05
Schedule::command('gymie:subscriptions --mark-expiring')
    ->everyMinute();
