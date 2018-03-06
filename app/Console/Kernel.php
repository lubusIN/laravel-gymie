<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\SetExpired::class,
        \App\Console\Commands\SmsEvent::class,
        \App\Console\Commands\SmsExpiring::class,
        \App\Console\Commands\ExpenseAlert::class,
        \App\Console\Commands\PendingInvoice::class,
        \App\Console\Commands\FollowupSms::class,
        \App\Console\Commands\SmsExpired::class,
        \App\Console\Commands\RepeatExpense::class,
        \App\Console\Commands\BirthdaySms::class,
        \App\Console\Commands\SmsStatus::class,
        \App\Console\Commands\ReshootOfflineSms::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        $schedule->command('reshoot:offlineSms')
                 ->hourly();

        $schedule->command('birthday:sms')
                 ->dailyAt('00:01');

        $schedule->command('set:expired')
                 ->dailyAt('00:05');

        $schedule->command('sms:event')
                 ->dailyAt('09:00');

        $schedule->command('sms:expiring')
                 ->dailyAt('10:00');

        $schedule->command('expense:alert')
                 ->dailyAt('10:30')
                 ->when(function () {
                    return (\Utilities::getSetting('primary_contact') != null);
                });

        $schedule->command('pending:invoice')
                 ->dailyAt('11:00');

        $schedule->command('followup:sms')
                 ->dailyAt('11:30');

        $schedule->command('sms:expired')
                 ->dailyAt('11:45');

        $schedule->command('repeat:expense')
                 ->dailyAt('23:00');

        $schedule->command('sms:status')
                 ->dailyAt('23:45');
    }
}
