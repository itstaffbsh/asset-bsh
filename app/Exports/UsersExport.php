<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::with(['office', 'department'])->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Full Name',
            'Email',
            'Department',
            'Job Position',
            'Job Level',
            'Join Date',
            'Phone Number',
            'Office',
        ];
    }

    public function map($user): array
    {
        return [
            $user->employee_id,
            $user->name,
            $user->email,
            $user->department->nama_departemen ?? '-',
            $user->job_position,
            $user->job_level,
            $user->join_date,
            $user->phone_number,
            $user->office->nama_kantor ?? '-',
        ];
    }
}
