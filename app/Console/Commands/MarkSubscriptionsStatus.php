<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class MarkSubscriptionsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:mark-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark subscriptions as expiring or expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $expiringThreshold = $now->copy()->addDays(7);

        // Mark already expired
        $expiredCount = Subscription::where('end_date', '<', $now)
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        // Mark those about to expire in next 7 days
        $expiringCount = Subscription::whereBetween('end_date', [$now, $expiringThreshold])
            ->where('status', '!=', 'expiring')
            ->update(['status' => 'expiring']);

        $expiredLabel  = Str::plural('subscription', $expiredCount);
        $expiringLabel = Str::plural('subscription', $expiringCount);

        $this->info("✅ Marked {$expiredCount} {$expiredLabel} as expired.");
        $this->info("⏳ Marked {$expiringCount} {$expiringLabel} as expiring soon (within 7 days).");

        $summary = "Subscriptions updated: {$expiredCount} expired, {$expiringCount} expiring soon.";

        $admin = User::role('super_admin')->first();

        Notification::make()
            ->title('Subscription Status Update')
            ->body($summary)
            ->info()
            ->sendToDatabase($admin);

        $this->info("🔔 Notification sent to {$admin->name} dashboard.");
    }
}
