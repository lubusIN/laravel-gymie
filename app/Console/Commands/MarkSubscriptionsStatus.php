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
    protected $signature = 'gymie:subscriptions 
                            {--mark-expired : Mark expired subscriptions} 
                            {--mark-expiring : Mark subscriptions expiring within 7 days}';

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
        $summary = [];

        if ($this->option('mark-expired')) {
            $expiredCount = Subscription::where('end_date', '<', $now)
                ->where('status', '!=', 'expired')
                ->update(['status' => 'expired']);

            $expiredLabel = Str::plural('subscription', $expiredCount);
            $this->info("✅ Marked {$expiredCount} {$expiredLabel} as expired.");
            $summary[] = "{$expiredCount} expired";
        }

        if ($this->option('mark-expiring')) {
            $expiringCount = Subscription::whereBetween('end_date', [$now, $expiringThreshold])
                ->where('status', '!=', 'expiring')
                ->update(['status' => 'expiring']);

            $expiringLabel = Str::plural('subscription', $expiringCount);
            $this->info("⏳ Marked {$expiringCount} {$expiringLabel} as expiring soon (within 7 days).");
            $summary[] = "{$expiringCount} expiring soon";
        }

        if (empty($summary)) {
            $this->warn("⚠️ No options provided. Use --mark-expired or --mark-expiring.");
            return;
        }

        $admin = User::role('super_admin')->first();
        Notification::make()
            ->title('Subscription Status Update')
            ->body('Subscriptions updated: ' . implode(', ', $summary) . '.')
            ->info()
            ->sendToDatabase($admin);

        $this->info("🔔 Notification sent to {$admin->name} dashboard.");
    }
}
