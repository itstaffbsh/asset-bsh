<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            // Products
            ['name' => 'View Products', 'slug' => 'products.view', 'feature' => 'Products'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'feature' => 'Products'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'feature' => 'Products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'feature' => 'Products'],
            ['name' => 'Export Products', 'slug' => 'products.export', 'feature' => 'Products'],
            ['name' => 'Import Products', 'slug' => 'products.import', 'feature' => 'Products'],
            ['name' => 'Manage Trash', 'slug' => 'products.trash', 'feature' => 'Products'],
            
            // Transactions
            ['name' => 'Transfer Asset', 'slug' => 'transactions.transfer', 'feature' => 'Transactions'],
            ['name' => 'View History', 'slug' => 'transactions.view_history', 'feature' => 'Transactions'],
            ['name' => 'Edit History', 'slug' => 'transactions.edit_history', 'feature' => 'Transactions'],
            
            // Requests
            ['name' => 'Finalize Request', 'slug' => 'requests.finalize', 'feature' => 'Requests'],
            
            // Users Management
            ['name' => 'View User List', 'slug' => 'users.view', 'feature' => 'Users'],
            ['name' => 'Create User Account', 'slug' => 'users.create', 'feature' => 'Users'],
            ['name' => 'Edit User Full', 'slug' => 'users.edit', 'feature' => 'Users'],
            ['name' => 'Edit User Profile', 'slug' => 'users.edit_profile', 'feature' => 'Users'],
            ['name' => 'Edit User Placement', 'slug' => 'users.edit_placement', 'feature' => 'Users'],
            ['name' => 'Edit User Job Info', 'slug' => 'users.edit_job', 'feature' => 'Users'],
            ['name' => 'Edit User Role', 'slug' => 'users.edit_role', 'feature' => 'Users'],
            ['name' => 'Edit User Credentials', 'slug' => 'users.edit_credentials', 'feature' => 'Users'],
            ['name' => 'Resign Employee', 'slug' => 'users.resign', 'feature' => 'Users'],
            ['name' => 'View Employee Data', 'slug' => 'users.view_employee_data', 'feature' => 'Users'],
            ['name' => 'Create Employee Profile', 'slug' => 'users.create_employee', 'feature' => 'Users'],
            ['name' => 'Edit Employee Profile', 'slug' => 'users.edit_employee', 'feature' => 'Users'],
            ['name' => 'View Resigned List', 'slug' => 'users.view_resigned', 'feature' => 'Users'],
            ['name' => 'Export Users', 'slug' => 'users.export', 'feature' => 'Users'],
            ['name' => 'Import Users', 'slug' => 'users.import', 'feature' => 'Users'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }

    public function down(): void
    {
        $slugs = [
            'products.view', 'products.create', 'products.edit', 'products.delete', 'products.export', 'products.import', 'products.trash',
            'transactions.transfer', 'transactions.edit_history',
            'requests.finalize',
            'users.view', 'users.create', 'users.edit', 'users.edit_profile', 'users.edit_placement', 'users.edit_job', 'users.edit_role', 'users.edit_credentials', 'users.resign',
            'users.view_employee_data', 'users.create_employee', 'users.edit_employee', 'users.view_resigned', 'users.export', 'users.import'
        ];
        DB::table('permissions')->whereIn('slug', $slugs)->delete();
    }
};
