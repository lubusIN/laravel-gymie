<?php

use App\User;
use Illuminate\Database\Seeder;

class ConnectRelationshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get Available Permissions.
         */
        $permissions = config('roles.models.permission')::all();

        /**
         * Attach Permissions to Roles.
         */
        $roleAdmin = config('roles.models.role')::where('name', '=', 'Admin')->first();
        $roleGymie = config('roles.models.role')::where('name', '=', 'Gymie')->first();
        $roleManager = config('roles.models.role')::where('name', '=', 'Manager')->first();
        foreach ($permissions as $permission) {
            $roleAdmin->attachPermission($permission);
            $roleGymie->attachPermission($permission);
            $roleManager->attachPermission($permission);
        }

        $userAdmin = User::find(1);
        $userAdmin->attachRole($roleAdmin);
    }
}
