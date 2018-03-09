<?php

use App\Role_user;
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
        Role_user::create([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
