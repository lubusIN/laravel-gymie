<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Scheduler.
 *
 * When tenancy is enabled, run maintenance across all gyms.
 * Otherwise, run the single-tenant commands.
 */
if ((bool) config('gymie-tenancy.enabled', false)) {
    // Mark subscriptions expired every day at 00:00
    Schedule::command('gymie:tenants:subscriptions')
        ->dailyAt('00:00');

    // Mark invoices overdue every day at 00:00
    Schedule::command('gymie:tenants:invoices --mark-overdue')
        ->dailyAt('00:00');
} else {
    // Mark subscriptions expired every day at 00:00
    Schedule::command('gymie:subscriptions')
        ->dailyAt('00:00');

    // Mark invoices overdue every day at 00:00
    Schedule::command('gymie:invoices --mark-overdue')
        ->dailyAt('00:00');
}
