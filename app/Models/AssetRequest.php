<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'hr_manager_id',
        'criticality_level',
        'reason',
        'status',
        'hr_comment',
        'dept_comment',
        'it_comment',
        'md_comment',
        'admin_notes',
    ];

    /* | [RELASI] | Karyawan yang mengajukan */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* | [RELASI] | Departemen pemohon */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /* | [RELASI] | Manager HR yang mensponsori */
    public function hrManager()
    {
        return $this->belongsTo(User::class, 'hr_manager_id');
    }

    /* | [RELASI] | Daftar barang yang diminta */
    public function items()
    {
        return $this->hasMany(AssetRequestItem::class);
    }

    /* | [RELASI] | Riwayat persetujuan */
    public function approvals()
    {
        return $this->hasMany(AssetRequestApproval::class);
    }
}
