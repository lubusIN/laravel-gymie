<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Permission Types
         *
         */
        // $Permissionitems = [
        //     [
        //         'name'        => 'Can View Users',
        //         'slug'        => 'view.users',
        //         'description' => 'Can view users',
        //         'model'       => 'Permission',
        //     ],
        //     [
        //         'name'        => 'Can Create Users',
        //         'slug'        => 'create.users',
        //         'description' => 'Can create new users',
        //         'model'       => 'Permission',
        //     ],
        //     [
        //         'name'        => 'Can Edit Users',
        //         'slug'        => 'edit.users',
        //         'description' => 'Can edit users',
        //         'model'       => 'Permission',
        //     ],
        //     [
        //         'name'        => 'Can Delete Users',
        //         'slug'        => 'delete.users',
        //         'description' => 'Can delete users',
        //         'model'       => 'Permission',
        //     ],
        // ];

        $Permissionitems = [
            [
              'name' => 'manage-gymie',
              'slug' => 'Manage Gymie',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-dashboard-quick-stats',
              'slug' => 'View quick stats on dashboard',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-dashboard-charts',
              'slug' => 'View charts on dashboard',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-dashboard-members-tab',
              'slug' => 'View members tab on dashboard',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-dashboard-enquiries-tab',
              'slug' => 'View enquiries tab on dashboard',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-member',
              'slug' => 'Add member',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-member',
              'slug' => 'View member details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-member',
              'slug' => 'Edit member details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-member',
              'slug' => 'Delete member',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-plan',
              'slug' => 'Add plans',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-plan',
              'slug' => 'View plan details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-plan',
              'slug' => 'Edit plan details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-plan',
              'slug' => 'Delete plans',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-subscription',
              'slug' => 'Add subscription',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-subscription',
              'slug' => 'Edit subscription details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'renew-subscription',
              'slug' => 'Renew subscription',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-invoice',
              'slug' => 'View invoice',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-payment',
              'slug' => 'Add payments',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-subscription',
              'slug' => 'View subscription details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-payment',
              'slug' => 'View payment details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-payment',
              'slug' => 'Edit payment details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-members',
              'slug' => 'Manage members',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-plans',
              'slug' => 'Manage plans',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-subscriptions',
              'slug' => 'Manage subscriptions',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-invoices',
              'slug' => 'Manage invoices',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-payments',
              'slug' => 'Manage payments',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-users',
              'slug' => 'Manage users',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-enquiry',
              'slug' => 'Add enquiry',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-enquiry',
              'slug' => 'View enquiry details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-enquiry',
              'slug' => 'Edit enquiry details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-enquiry-followup',
              'slug' => 'Add enquiry followup',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-enquiry-followup',
              'slug' => 'Edit enquiry followup',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'transfer-enquiry',
              'slug' => 'Transfer enquiry',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-enquiries',
              'slug' => 'Manage enquiries',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-expense',
              'slug' => 'Add expense',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-expense',
              'slug' => 'View expense details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-expense',
              'slug' => 'Edit expense details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-expenses',
              'slug' => 'Manage expenses',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-expenseCategory',
              'slug' => 'Add expense category',
              'description' => '',
              'model' => 'Permission Categories',
            ],
            [
              'name' => 'view-expenseCategory',
              'slug' => 'View expense categories',
              'description' => '',
              'model' => 'Permission Categories',
            ],
            [
              'name' => 'edit-expenseCategory',
              'slug' => 'Edit expense category details',
              'description' => '',
              'model' => 'Permission Categories',
            ],
            [
              'name' => 'delete-expenseCategory',
              'slug' => 'Delete expense category',
              'description' => '',
              'model' => 'Permission Categories',
            ],
            [
              'name' => 'manage-expenseCategories',
              'slug' => 'Manage expense categories',
              'description' => '',
              'model' => 'Permission Categories',
            ],
            [
              'name' => 'manage-settings',
              'slug' => 'Manage settings',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'cancel-subscription',
              'slug' => 'Cancel subscription',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-services',
              'slug' => 'Manage services',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-service',
              'slug' => 'Add services',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'edit-service',
              'slug' => 'Edit service details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-service',
              'slug' => 'View service details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'manage-sms',
              'slug' => 'Manage SMS',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'pagehead-stats',
              'slug' => 'View pagehead counts',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'view-dashboard-expense-tab',
              'slug' => 'View expenses tab on dashboard',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'print-invoice',
              'slug' => 'Print invoices',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-invoice',
              'slug' => 'Delete invoices',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-subscription',
              'slug' => 'Delete subscriptions',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-payment',
              'slug' => 'Delete payment transactions',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-expense',
              'slug' => 'Delete expense details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'delete-service',
              'slug' => 'Delete Service details',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'add-discount',
              'slug' => 'Add discount on a invoice',
              'description' => '',
              'model' => 'Permission',
            ],
            [
              'name' => 'change-subscription',
              'slug' => 'Upgrade or downgrade a subscription',
              'description' => '',
              'model' => 'Permission',
            ],
          ];

        /*
         * Add Permission Items
         *
         */
        foreach ($Permissionitems as $Permissionitem) {
            $newPermissionitem = config('roles.models.permission')::where('slug', '=', $Permissionitem['slug'])->first();
            if ($newPermissionitem === null) {
                $newPermissionitem = config('roles.models.permission')::create([
                    'name'          => $Permissionitem['name'],
                    'slug'          => $Permissionitem['slug'],
                    'description'   => $Permissionitem['description'],
                    'model'         => $Permissionitem['model'],
                ]);
            }
        }
    }
}
