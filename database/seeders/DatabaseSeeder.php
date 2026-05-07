<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $office1 = \App\Models\Office::create(['nama_kantor' => 'Kantor Pusat', 'alamat' => 'Jl. Jend. Sudirman No 1']);
        $office2 = \App\Models\Office::create(['nama_kantor' => 'Kantor Cabang', 'alamat' => 'Jl. Merdeka No 2']);

        $deptHR = \App\Models\Department::create(['nama_departemen' => 'Human Resource', 'kode_asset' => 'HR']);
        $deptIT = \App\Models\Department::create(['nama_departemen' => 'Information Technology', 'kode_asset' => 'IT']);
        $deptAC = \App\Models\Department::create(['nama_departemen' => 'Accounting', 'kode_asset' => 'AC']);

        // Akun Shinji (Managing Director)
        User::create([
            'employee_id' => '00001',
            'name' => 'Shinji',
            'email' => 'shinji@bsh.com',
            'password' => bcrypt('password123'),
            'role' => 'managing_director',
            'office_id' => $office1->id,
            'department_id' => $deptIT->id,
            'status' => 'active',
        ]);

        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'office_id' => $office1->id,
            'department_id' => $deptIT->id,
            'status' => 'active',
        ]);

        \App\Models\Classification::create(['nama_klasifikasi' => 'Laptop']);
        \App\Models\Classification::create(['nama_klasifikasi' => 'Smartphone']);
        \App\Models\Classification::create(['nama_klasifikasi' => 'Monitor']);

        // Jalankan seeder permission
        $this->call([
            PermissionSeeder::class,
        ]);
    }
}
