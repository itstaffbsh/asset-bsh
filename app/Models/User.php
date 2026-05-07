<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'role_id', 'office_id', 'department_id', 'employee_id', 'job_position', 'job_level', 'join_date', 'phone_number', 'status', 'resigned_at'])]
#[Hidden(['password', 'remember_token'])]
/* |--------------------------------------------------------------------------
   | [MODEL USER / KARYAWAN]
   |--------------------------------------------------------------------------
   | Kegunaan: Sebagai representasi data orang (Admin atau Karyawan).
   */
class User extends Authenticatable
{
    // [KONSTANTA] | Daftar Role dari Level Terendah ke Tertinggi
    const ROLE_USER              = 'user';
    const ROLE_ADMIN             = 'admin';
    const ROLE_SUPER_ADMIN       = 'superadmin';
    const ROLE_MANAGER           = 'manager';
    const ROLE_DIRECTOR          = 'director';
    const ROLE_MANAGING_DIRECTOR = 'managing_director';

    // [ARRAY] | Pemetaan Level Role (Semakin besar angka, semakin tinggi kekuasaannya)
    protected static $roleLevels = [
        self::ROLE_USER              => 1,
        self::ROLE_ADMIN             => 2,
        self::ROLE_SUPER_ADMIN       => 3,
        self::ROLE_MANAGER           => 4,
        self::ROLE_DIRECTOR          => 5,
        self::ROLE_MANAGING_DIRECTOR => 6,
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'resigned_at' => 'datetime',
    ];

    /**
     * Mengecek apakah user memiliki level role tertentu atau lebih tinggi.
     */
    public function hasRoleLevel($role)
    {
        // Prioritaskan role_id (dynamic role) jika ada, jika tidak pakai string 'role'
        $roleSlug = $this->role_id ? ($this->role_relation?->slug) : $this->role;
        
        // Superadmin bypass
        if ($roleSlug === 'superadmin') return true;

        $userLevel = static::$roleLevels[$roleSlug] ?? 0;
        $requiredLevel = static::$roleLevels[$role] ?? 99;
        return $userLevel >= $requiredLevel;
    }

    public function hasPermission($permissionSlug)
    {
        // Superadmin & Managing Director bypass
        $roleSlug = $this->role_id ? ($this->role_relation?->slug) : $this->role;
        if ($roleSlug === 'superadmin' || $roleSlug === 'managing_director') {
            return true;
        }

        if (!$this->role_id) return false;

        return $this->role_relation->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Mendapatkan angka level role saat ini.
     */
    public function getRoleLevel()
    {
        $roleSlug = $this->role_id ? ($this->role_relation?->slug) : $this->role;
        return static::$roleLevels[$roleSlug] ?? 0;
    }

    /**
     * Mendapatkan daftar role yang bisa diberikan oleh user ini.
     * Aturan: Hanya bisa memberikan role yang levelnya DI BAWAH dirinya.
     */
    public function getAssignableRoles()
    {
        $myLevel = $this->getRoleLevel();
        $roles = [];
        foreach (static::$roleLevels as $role => $level) {
            if ($level < $myLevel) {
                $roles[] = $role;
            }
        }
        // Khusus User, mereka tidak bisa memberi role apapun
        return $roles;
    }

    /* | [ARRAY] | Daftar kolom yang boleh diisi (Mass Assignment) */
    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'office_id',
        'department_id',
        'job_position',
        'job_level',
        'join_date',
        'phone_number',
        'signature_path',
    ];

    /* | [LOGIKA] | Menghasilkan Employee ID otomatis saat membuat user baru */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->employee_id)) {
                $maxId = static::max('employee_id');
                // Mengambil angka terakhir, tambah 1, lalu pad dengan nol di depan (contoh: 00001)
                $nextId = $maxId ? (int)$maxId + 1 : 1;
                $user->employee_id = str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role_relation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function givenHistories()
    {
        return $this->hasMany(ProductHistory::class, 'dioper_oleh');
    }

    public function receivedHistories()
    {
        return $this->hasMany(ProductHistory::class, 'diterima_oleh');
    }

    /* | [FUNGSI] | Relasi ke Kantor (Satu orang bekerja di satu kantor) */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function assetRequests()
    {
        return $this->hasMany(AssetRequest::class);
    }

    public static function getPihakPertama()
    {
        return static::whereHas('role_relation.permissions', function($q) {
            $q->where('slug', 'sttb.pihak_pertama');
        })->get();
    }

    public static function getPihakKedua()
    {
        return static::whereHas('role_relation.permissions', function($q) {
            $q->where('slug', 'sttb.pihak_kedua');
        })->get();
    }

    /* | [RELASI] | Daftar permintaan yang disponsori oleh user ini (sebagai HR) */
    public function sponsoredRequests()
    {
        return $this->hasMany(AssetRequest::class, 'hr_manager_id');
    }
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
