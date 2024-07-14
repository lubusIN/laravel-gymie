<?php

use App\Permission;
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
        // Create permissions
        $permissions = [
          [
            'name' => 'manage-gymie',
            'display_name' => 'Manage Gymie',
            'group_key' => 'Global',
          ],
          [
            'name' => 'view-dashboard-quick-stats',
            'display_name' => 'View quick stats on dashboard',
            'group_key' => 'Dashboard',
          ],
          [
            'name' => 'view-dashboard-charts',
            'display_name' => 'View charts on dashboard',
            'group_key' => 'Dashboard',
          ],
          [
            'name' => 'view-dashboard-members-tab',
            'display_name' => 'View members tab on dashboard',
            'group_key' => 'Dashboard',
          ],
          [
            'name' => 'view-dashboard-enquiries-tab',
            'display_name' => 'View enquiries tab on dashboard',
            'group_key' => 'Dashboard',
          ],
          [
            'name' => 'add-member',
            'display_name' => 'Add member',
            'group_key' => 'Members',
          ],
          [
            'name' => 'view-member',
            'display_name' => 'View member details',
            'group_key' => 'Members',
          ],
          [
            'name' => 'edit-member',
            'display_name' => 'Edit member details',
            'group_key' => 'Members',
          ],
          [
            'name' => 'delete-member',
            'display_name' => 'Delete member',
            'group_key' => 'Members',
          ],
          [
            'name' => 'add-plan',
            'display_name' => 'Add plans',
            'group_key' => 'Plans',
          ],
          [
            'name' => 'view-plan',
            'display_name' => 'View plan details',
            'group_key' => 'Plans',
          ],
          [
            'name' => 'edit-plan',
            'display_name' => 'Edit plan details',
            'group_key' => 'Plans',
          ],
          [
            'name' => 'delete-plan',
            'display_name' => 'Delete plans',
            'group_key' => 'Plans',
          ],
          [
            'name' => 'add-subscription',
            'display_name' => 'Add subscription',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'edit-subscription',
            'display_name' => 'Edit subscription details',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'renew-subscription',
            'display_name' => 'Renew subscription',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'view-invoice',
            'display_name' => 'View invoice',
            'group_key' => 'Invoices',
          ],
          [
            'name' => 'add-payment',
            'display_name' => 'Add payments',
            'group_key' => 'Payments',
          ],
          [
            'name' => 'view-subscription',
            'display_name' => 'View subscription details',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'view-payment',
            'display_name' => 'View payment details',
            'group_key' => 'Payments',
          ],
          [
            'name' => 'edit-payment',
            'display_name' => 'Edit payment details',
            'group_key' => 'Payments',
          ],
          [
            'name' => 'manage-members',
            'display_name' => 'Manage members',
            'group_key' => 'Members',
          ],
          [
            'name' => 'manage-plans',
            'display_name' => 'Manage plans',
            'group_key' => 'Plans',
          ],
          [
            'name' => 'manage-subscriptions',
            'display_name' => 'Manage subscriptions',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'manage-invoices',
            'display_name' => 'Manage invoices',
            'group_key' => 'Invoices',
          ],
          [
            'name' => 'manage-payments',
            'display_name' => 'Manage payments',
            'group_key' => 'Payments',
          ],
          [
            'name' => 'manage-users',
            'display_name' => 'Manage users',
            'group_key' => 'Users',
          ],
          [
            'name' => 'add-enquiry',
            'display_name' => 'Add enquiry',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'view-enquiry',
            'display_name' => 'View enquiry details',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'edit-enquiry',
            'display_name' => 'Edit enquiry details',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'add-enquiry-followup',
            'display_name' => 'Add enquiry followup',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'edit-enquiry-followup',
            'display_name' => 'Edit enquiry followup',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'transfer-enquiry',
            'display_name' => 'Transfer enquiry',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'manage-enquiries',
            'display_name' => 'Manage enquiries',
            'group_key' => 'Enquiries',
          ],
          [
            'name' => 'add-expense',
            'display_name' => 'Add expense',
            'group_key' => 'Expenses',
          ],
          [
            'name' => 'view-expense',
            'display_name' => 'View expense details',
            'group_key' => 'Expenses',
          ],
          [
            'name' => 'edit-expense',
            'display_name' => 'Edit expense details',
            'group_key' => 'Expenses',
          ],
          [
            'name' => 'manage-expenses',
            'display_name' => 'Manage expenses',
            'group_key' => 'Expenses',
          ],
          [
            'name' => 'add-expenseCategory',
            'display_name' => 'Add expense category',
            'group_key' => 'Expense Categories',
          ],
          [
            'name' => 'view-expenseCategory',
            'display_name' => 'View expense categories',
            'group_key' => 'Expense Categories',
          ],
          [
            'name' => 'edit-expenseCategory',
            'display_name' => 'Edit expense category details',
            'group_key' => 'Expense Categories',
          ],
          [
            'name' => 'delete-expenseCategory',
            'display_name' => 'Delete expense category',
            'group_key' => 'Expense Categories',
          ],
          [
            'name' => 'manage-expenseCategories',
            'display_name' => 'Manage expense categories',
            'group_key' => 'Expense Categories',
          ],
          [
            'name' => 'manage-settings',
            'display_name' => 'Manage settings',
            'group_key' => 'Global',
          ],
          [
            'name' => 'cancel-subscription',
            'display_name' => 'Cancel subscription',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'manage-services',
            'display_name' => 'Manage services',
            'group_key' => 'Services',
          ],
          [
            'name' => 'add-service',
            'display_name' => 'Add services',
            'group_key' => 'Services',
          ],
          [
            'name' => 'edit-service',
            'display_name' => 'Edit service details',
            'group_key' => 'Services',
          ],
          [
            'name' => 'view-service',
            'display_name' => 'View service details',
            'group_key' => 'Services',
          ],
          [
            'name' => 'manage-sms',
            'display_name' => 'Manage SMS',
            'group_key' => 'SMS',
          ],
          [
            'name' => 'pagehead-stats',
            'display_name' => 'View pagehead counts',
            'group_key' => 'Global',
          ],
          [
            'name' => 'view-dashboard-expense-tab',
            'display_name' => 'View expenses tab on dashboard',
            'group_key' => 'Dashboard',
          ],
          [
            'name' => 'print-invoice',
            'display_name' => 'Print invoices',
            'group_key' => 'Invoices',
          ],
          [
            'name' => 'delete-invoice',
            'display_name' => 'Delete invoices',
            'group_key' => 'Invoices',
          ],
          [
            'name' => 'delete-subscription',
            'display_name' => 'Delete subscriptions',
            'group_key' => 'Subscriptions',
          ],
          [
            'name' => 'delete-payment',
            'display_name' => 'Delete payment transactions',
            'group_key' => 'Payments',
          ],
          [
            'name' => 'delete-expense',
            'display_name' => 'Delete expense details',
            'group_key' => 'Expenses',
          ],
          [
            'name' => 'delete-service',
            'display_name' => 'Delete Service details',
            'group_key' => 'Services',
          ],
          [
            'name' => 'add-discount',
            'display_name' => 'Add discount on a invoice',
            'group_key' => 'Invoices',
          ],
          [
            'name' => 'change-subscription',
            'display_name' => 'Upgrade or downgrade a subscription',
            'group_key' => 'Subscriptions',
          ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
