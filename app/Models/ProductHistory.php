<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* |--------------------------------------------------------------------------
   | [MODEL RIWAYAT ASET]
   |--------------------------------------------------------------------------
   | Kegunaan: Mencatat setiap transaksi (peminjaman atau pengembalian).
   */

class ProductHistory extends Model
{
    use HasFactory;
    
    const TRANS_TYPES = [
        'meminjam'     => 'Meminjam',
        'mengembalikan' => 'Mengembalikan'
    ];

    /* | [ARRAY] | Kolom yang mencatat detail transaksi */
    protected $fillable = [
        'product_id',
        'dioper_oleh',
        'diterima_oleh',
        'lend_date',
        'returned_date',
        'description',
        'remarks',
        'jenis_transaksi',
        'batch_id',         // ID unik untuk mengelompokkan banyak barang dalam 1 STTB
        'quantity',         // Jumlah barang yang dipinjam
        'pihak_pertama_id', // Admin yang menyerahkan barang (PIHAK PERTAMA di STTB)
        'tanggal_pinjam',   // Tanggal resmi peminjaman di STTB
    ];

    /* | [FUNGSI] | Relasi ke Aset yang sedang ditransaksikan */
    public function product()
    {
        return $this->belongsTo(Aset::class, 'product_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'dioper_oleh');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /* | [FUNGSI] | Relasi ke User sebagai PIHAK PERTAMA (yang menyerahkan di STTB) */
    public function pihakPertama()
    {
        return $this->belongsTo(User::class, 'pihak_pertama_id');
    }
}
