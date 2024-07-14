<?php

use App\PermissionRole;
use Illuminate\Database\Seeder;

class PermissionsRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Permission roles
        $permissions_role = [
          [
            'permission_id' => '1',
            'role_id' => '1',
          ],
          [
            'permission_id' => '1',
            'role_id' => '2',
          ],
          [
            'permission_id' => '2',
            'role_id' => '3',
          ],
          [
            'permission_id' => '3',
            'role_id' => '3',
          ],
          [
            'permission_id' => '4',
            'role_id' => '3',
          ],
          [
            'permission_id' => '5',
            'role_id' => '3',
          ],
          [
            'permission_id' => '24',
            'role_id' => '3',
          ],
          [
            'permission_id' => '25',
            'role_id' => '3',
          ],
          [
            'permission_id' => '26',
            'role_id' => '3',
          ],
          [
            'permission_id' => '27',
            'role_id' => '3',
          ],
          [
            'permission_id' => '28',
            'role_id' => '3',
          ],
          [
            'permission_id' => '36',
            'role_id' => '3',
          ],
          [
            'permission_id' => '40',
            'role_id' => '3',
          ],
          [
            'permission_id' => '45',
            'role_id' => '3',
          ],
          [
            'permission_id' => '54',
            'role_id' => '3',
          ],
          [
            'permission_id' => '55',
            'role_id' => '3',
          ],
          [
            'permission_id' => '56',
            'role_id' => '3',
          ],
        ];

        foreach ($permissions_role as $permission_role) {
            PermissionRole::create($permission_role);
        }
    }
}
