<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Office;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Aturan validasi untuk setiap baris di Excel
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'full_name' => 'nullable|string|max:255',
            // Kita tidak pakai unique:users di sini karena kita ingin mendukung update data
        ];
    }

    /**
     * Custom error messages (Opsional)
     */
    public function customValidationMessages()
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ];
    }

    public function model(array $row)
    {
        // Validasi: lewati baris jika email tidak ada
        if (empty($row['email'])) return null;

        // Cari office ID berdasarkan nama
        $office = Office::where('nama_kantor', $row['office'] ?? '')->first();
        
        // Cari department ID berdasarkan nama
        $dept = Department::where('nama_departemen', $row['department'] ?? '')->first();

        // Pad employee_id jika berupa angka (contoh: 9 -> 00009)
        $employeeId = $row['employee_id'] ?? null;
        if ($employeeId && is_numeric($employeeId)) {
            $employeeId = str_pad($employeeId, 5, '0', STR_PAD_LEFT);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $row['email'])->first();

        // Cek jika employee_id sudah dipakai oleh email LAIN
        if ($employeeId) {
            $existingIdOwner = User::where('employee_id', $employeeId)
                ->where('email', '!=', $row['email'])
                ->first();
            if ($existingIdOwner) {
                // Berikan pesan error spesifik jika ID sudah dipakai orang lain
                throw new \Exception("ID Karyawan '{$employeeId}' sudah digunakan oleh {$existingIdOwner->name} ({$existingIdOwner->email}).");
            }
        }

        if ($user) {
            // Update data tanpa mengubah role (agar admin tidak turun jadi employee)
            $user->update([
                'employee_id'   => $employeeId,
                'name'          => $row['full_name'] ?? $row['name'] ?? $user->name,
                'job_position'  => $row['job_position'] ?? $user->job_position,
                'job_level'     => $row['job_level'] ?? $user->job_level,
                'join_date'     => isset($row['join_date']) ? $this->transformDate($row['join_date']) : $user->join_date,
                'phone_number'  => $row['phone_number'] ?? $user->phone_number,
                'office_id'     => $office ? $office->id : $user->office_id,
                'department_id' => $dept ? $dept->id : $user->department_id,
            ]);
        } else {
            // Buat user baru dengan role default 'employee'
            User::create([
                'email'         => $row['email'],
                'employee_id'   => $employeeId,
                'name'          => $row['full_name'] ?? $row['name'] ?? '-',
                'role'          => 'employee',
                'job_position'  => $row['job_position'] ?? null,
                'job_level'     => $row['job_level'] ?? null,
                'join_date'     => isset($row['join_date']) ? $this->transformDate($row['join_date']) : null,
                'phone_number'  => $row['phone_number'] ?? null,
                'office_id'     => $office ? $office->id : 1,
                'department_id' => $dept ? $dept->id : null,
                'password'      => bcrypt('Balibsh@1234'), // Default password baru untuk user baru
            ]);
        }

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
