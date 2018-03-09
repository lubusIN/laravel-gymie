<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Roles
        $roles = [
            [
                'name'  =>  'Gymie',
            ],
            [
                'name'  =>  'Admin',
            ],
            [
                'name'  =>  'Manager',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
