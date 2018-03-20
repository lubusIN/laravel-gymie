<?php

use App\RoleUser;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Role User
        RoleUser::create([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
