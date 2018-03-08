<?php

use Illuminate\Database\Seeder;
use App\Role_user;

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
        Role_user::create([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
