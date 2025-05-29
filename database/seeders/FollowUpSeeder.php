<?php

namespace Database\Seeders;

use App\Models\FollowUp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FollowUp::factory()->count(5)->create();
    }
}
