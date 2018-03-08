<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create User
        User::create( [
            'name' => 'Gymie',
            'email' => 'admin@gymie.in',
            'password' => bcrypt('password'),
            'status' => '1',
        ]);
    }
}
