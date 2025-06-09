<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Nnjeim\World\Actions\SeedAction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SeedAction::class,
            ServiceSeeder::class,
            EnquirySeeder::class,
            ServiceSeeder::class,
            FollowUpSeeder::class,
            PlanSeeder::class,
            UserSeeder::class,
        ]);
    }
}
