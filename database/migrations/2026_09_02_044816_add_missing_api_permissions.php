<?php

use Illuminate\Database\Migrations\Migration;
use BezhanSalleh\FilamentShield\Support\Utils;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissionModel = Utils::getPermissionModel();

        $permissions = [
            ['name' => 'ViewAny:Analytics', 'guard_name' => 'web'],
            ['name' => 'Update:Settings', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            $permissionModel::firstOrCreate($permission);
        }
        
        $roleModel = Utils::getRoleModel();
        $superAdmin = $roleModel::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(['ViewAny:Analytics', 'Update:Settings']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionModel = Utils::getPermissionModel();

        $permissionModel::whereIn('name', ['ViewAny:Analytics', 'Update:Settings'])->delete();
    }
};
