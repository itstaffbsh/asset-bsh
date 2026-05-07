<?php

namespace App\Imports;

use App\Models\Aset;
use App\Models\User;
use App\Models\Classification;
use App\Models\ProductHistory;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

/* |--------------------------------------------------------------------------
   | [IMPORT DATA EXCEL]
   |--------------------------------------------------------------------------
   | Kegunaan: Mengatur cara sistem membaca file Excel dan menyimpannya ke database.
   */

class ProductsImport implements ToModel, WithHeadingRow
{
    /* | [PROSEDUR] | 
       | Kegunaan: Mengolah setiap baris (row) di file Excel.
    */
    public function model(array $row)
    {
        // ... (sisanya tetap menggunakan Aset::)
        // 1. Find or Create Classification (Asset Category)
        $classification = null;
        if (!empty($row['asset_category'])) {
            $classification = Classification::firstOrCreate(
                ['nama_klasifikasi' => $row['asset_category']],
                ['kode_klasifikasi' => strtoupper(substr($row['asset_category'], 0, 2))]
            );
        }

        // 2. Process ID Barang (Nomor Unik) & Department
        $idBarang = $row['id_barang'] ?? '';
        $prefix = 'HR';
        $number = '00000';
        $department = Department::first(); // Default

        if (str_contains($idBarang, '-')) {
            $parts = explode('-', $idBarang);
            $prefix = $parts[0];
            $number = $parts[1];
            
            // Cari departemen berdasarkan kode prefix
            $findDept = Department::where('kode_asset', $prefix)->first();
            if ($findDept) {
                $department = $findDept;
            }
        }

        // 3. Find or Create User (Holder)
        $user = null;
        $employeeId = $row['employee_id'] ?? null;
        $name = $row['name'] ?? null;

        if ($employeeId && $employeeId !== '-' && $employeeId !== 'Kosong') {
            $user = User::where('employee_id', $employeeId)->first();
            if (!$user && $name && $name !== 'DI KANTOR' && $name !== 'Kosong') {
                $user = User::create([
                    'employee_id' => $employeeId,
                    'name' => $name,
                    'email' => strtolower(Str::slug($name)) . '@bsh.com',
                    'role' => 'user',
                    'office_id' => 1,
                    'department_id' => $department->id,
                ]);
            }
        }

        // 4. Create or Update Product
        $product = Aset::firstOrNew(
            ['nomor_unik' => $number, 'department_id' => $department->id]
        );

        $product->fill([
            'description' => $row['description'] ?? $row['device_name'] ?? 'Asset Baru',
            'classification_id' => $classification?->id,
            'serial_number' => $row['serial_number'] ?? null,
            'processor' => $row['proccessor'] ?? null,
            'ram' => $row['ram'] ?? null,
            'new_ram' => $row['new_ram'] ?? null,
            'ssd' => $row['ssd'] ?? null,
            'new_ssd' => $row['new_ssd'] ?? null,
            'os' => $row['os'] ?? null,
            'screen_id' => $row['screen_id'] ?? null,
            'imei' => $row['imei'] ?? null,
            'mac_address' => $row['mac_address'] ?? null,
            'device_class' => $row['standard_recommendation_device_class'] ?? null,
        ]);

        if (!$product->exists) {
            $product->url_token = Str::random(40);
        }

        $product->save();

        // 5. Create History if there's holder info
        if ($user) {
            $lendDate = null;
            if (!empty($row['lend_date'])) {
                $lendDate = $this->transformDate($row['lend_date']);
            }

            $returnedDate = null;
            if (!empty($row['returned_date'])) {
                $returnedDate = $this->transformDate($row['returned_date']);
            }

            ProductHistory::create([
                'product_id' => $product->id,
                'dioper_oleh' => auth()->id(),
                'diterima_oleh' => $user->id,
                'lend_date' => $lendDate,
                'returned_date' => $returnedDate,
                'description' => $row['description'] ?? null,
                'remarks' => $row['remaks'] ?? null,
                'jenis_transaksi' => 'meminjam',
            ]);
        }

        return null;
    }

    /**
     * Mengonversi data tanggal dari Excel (baik berupa angka serial atau teks) 
     * menjadi objek Carbon/DateTime yang valid.
     */
    private function transformDate($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
