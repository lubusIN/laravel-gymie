<?php

namespace App\Console\Commands;

use App\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and sets expired subscription';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Subscription::where('end_date', '<', Carbon::today())->where('status', '=', \constSubscription::onGoing)->update(['status' => \constSubscription::Expired]);
    }
}
