<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Office;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Validasi: lewati baris jika email tidak ada
        if (empty($row['email'])) return null;

        // Cari office ID berdasarkan nama
        $office = Office::where('nama_kantor', $row['office'] ?? '')->first();
        
        // Cari department ID berdasarkan nama
        $dept = Department::where('nama_departemen', $row['department'] ?? '')->first();

        // Gunakan updateOrCreate agar data yang sudah ada diperbarui,
        // bukan dimasukkan ulang (mencegah error duplicate entry)
        $user = User::updateOrCreate(
            // Kondisi pencarian (cari berdasarkan email)
            ['email' => $row['email']],
            // Data yang akan dibuat atau diperbarui
            [
                'employee_id'   => $row['employee_id'] ?? null,
                'name'          => $row['full_name'] ?? $row['name'] ?? '-',
                'role'          => strtolower($row['role'] ?? 'user'),
                'job_position'  => $row['job_position'] ?? null,
                'job_level'     => $row['job_level'] ?? null,
                'join_date'     => isset($row['join_date']) ? $this->transformDate($row['join_date']) : null,
                'phone_number'  => $row['phone_number'] ?? null,
                'office_id'     => $office ? $office->id : 1,
                'department_id' => $dept ? $dept->id : null,
            ]
        );

        // Kembalikan null karena kita sudah simpan manual via updateOrCreate
        return null;
    }

    /**
     * Mengonversi data tanggal dari Excel (baik berupa angka serial atau teks) 
     * menjadi objek Carbon/DateTime yang valid.
     */
    private function transformDate($value)
    {
        if (empty($value)) return null;
        
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
