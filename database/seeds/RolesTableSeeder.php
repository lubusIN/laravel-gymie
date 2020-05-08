<?php

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
        /*
         * Role Types
         *
         */
        // $RoleItems = [
        //     [
        //         'name'        => 'Admin',
        //         'slug'        => 'admin',
        //         'description' => 'Admin Role',
        //         'level'       => 5,
        //     ],
        //     [
        //         'name'        => 'User',
        //         'slug'        => 'user',
        //         'description' => 'User Role',
        //         'level'       => 1,
        //     ],
        //     [
        //         'name'        => 'Unverified',
        //         'slug'        => 'unverified',
        //         'description' => 'Unverified Role',
        //         'level'       => 0,
        //     ],
        // ];

        $RoleItems = [
            [
                'name'  => 'Gymie',
                'slug'  => 'gymie',
                'description' => '',
                'level' => 1
            ],
            [
                'name'  => 'Admin',
                'slug'  => 'admin',
                'description' => '',
                'level' => 1
            ],
            [
                'name'  => 'Manager',
                'slug'  => 'manager',
                'description' => '',
                'level' => 1
            ],
        ];

        /*
         * Add Role Items
         *
         */
        foreach ($RoleItems as $RoleItem) {
            $newRoleItem = config('roles.models.role')::where('slug', '=', $RoleItem['slug'])->first();
            if ($newRoleItem === null) {
                $newRoleItem = config('roles.models.role')::create([
                    'name'          => $RoleItem['name'],
                    'slug'          => $RoleItem['slug'],
                    'description'   => $RoleItem['description'],
                    'level'         => $RoleItem['level'],
                ]);
            }
        }
    }
}
