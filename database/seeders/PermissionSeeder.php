<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Assets
            ['name' => 'View Assets', 'slug' => 'assets.view', 'feature' => 'Assets'],
            ['name' => 'Create Assets', 'slug' => 'assets.create', 'feature' => 'Assets'],
            ['name' => 'Edit Assets', 'slug' => 'assets.edit', 'feature' => 'Assets'],
            ['name' => 'Delete Assets', 'slug' => 'assets.delete', 'feature' => 'Assets'],
            ['name' => 'Export Assets', 'slug' => 'assets.export', 'feature' => 'Assets'],
            ['name' => 'Import Assets', 'slug' => 'assets.import', 'feature' => 'Assets'],
            ['name' => 'Print QR', 'slug' => 'assets.qr', 'feature' => 'Assets'],

            // Transactions
            ['name' => 'Loan Asset', 'slug' => 'transactions.loan', 'feature' => 'Transactions'],
            ['name' => 'Return Asset', 'slug' => 'transactions.return', 'feature' => 'Transactions'],
            ['name' => 'View History', 'slug' => 'transactions.history', 'feature' => 'Transactions'],
            ['name' => 'Print STTB', 'slug' => 'transactions.sttb', 'feature' => 'Transactions'],

            // Asset Requests (EWTR)
            ['name' => 'View Requests', 'slug' => 'requests.view', 'feature' => 'Asset Requests'],
            ['name' => 'Create Requests', 'slug' => 'requests.create', 'feature' => 'Asset Requests'],
            ['name' => 'Approval Tahap 1 (HR)', 'slug' => 'requests.approve_stage_1', 'feature' => 'Asset Requests'],
            ['name' => 'Approval Tahap 2 (Dept)', 'slug' => 'requests.approve_stage_2', 'feature' => 'Asset Requests'],
            ['name' => 'Approval Tahap 3 (IT)', 'slug' => 'requests.approve_stage_3', 'feature' => 'Asset Requests'],
            ['name' => 'Approval Tahap 4 (MD)', 'slug' => 'requests.approve_stage_4', 'feature' => 'Asset Requests'],

            // STTB Parties
            ['name' => 'Pihak Pertama (Peminjaman)', 'slug' => 'sttb.pihak_pertama', 'feature' => 'STTB'],
            ['name' => 'Pihak Kedua (Pengembalian)', 'slug' => 'sttb.pihak_kedua', 'feature' => 'STTB'],

            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'feature' => 'Users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'feature' => 'Users'],
            ['name' => 'Edit Basic Profile (Name, Email, Phone)', 'slug' => 'users.edit_profile', 'feature' => 'Users'],
            ['name' => 'Edit Placement (Office & Dept)', 'slug' => 'users.edit_placement', 'feature' => 'Users'],
            ['name' => 'Edit Job Info (ID, Level, Position)', 'slug' => 'users.edit_job', 'feature' => 'Users'],
            ['name' => 'Edit Role & Access', 'slug' => 'users.edit_role', 'feature' => 'Users'],
            ['name' => 'Resign Workflow', 'slug' => 'users.resign', 'feature' => 'Users'],

            // Master Data
            ['name' => 'Manage Offices', 'slug' => 'master.offices', 'feature' => 'Master Data'],
            ['name' => 'Manage Departments', 'slug' => 'master.departments', 'feature' => 'Master Data'],
            ['name' => 'Manage Classifications', 'slug' => 'master.classifications', 'feature' => 'Master Data'],

            // System
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'feature' => 'System'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Create Super Admin Role
        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'superadmin'],
            ['name' => 'Super Admin', 'description' => 'Akses penuh ke seluruh sistem']
        );

        // Sync all permissions to Super Admin
        $superAdminRole->permissions()->sync(Permission::all());

        // Update current Super Admin user to use this role_id
        $user = User::where('email', 'superadmin@admin.com')->first();
        if ($user) {
            $user->update(['role_id' => $superAdminRole->id]);
        }

        // Create Managing Director Role
        $mdRole = Role::updateOrCreate(
            ['slug' => 'managing_director'],
            ['name' => 'Managing Director', 'description' => 'Akses eksekutif untuk melihat dan menyetujui']
        );
        // MD also gets all permissions based on user request "sama powernya dengan super admin"
        $mdRole->permissions()->sync(Permission::all());

        // Update Shinji to Managing Director
        $shinji = User::where('name', 'Shinji')->first();
        if ($shinji) {
            $shinji->update(['role_id' => $mdRole->id]);
        }
    }
}
