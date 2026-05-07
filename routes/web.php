<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

/* | [PROSEDUR] | Pengaturan Bahasa Sistem */
Route::get('lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

/* | [PROSEDUR] | Halaman Selamat Datang saat pertama kali dibuka */
Route::get('/', function () {
    return view('welcome');
});

/* | [PROSEDUR DARURAT] | 
   | Jalankan URL '/perbaiki-database' jika terjadi error "Column description not found" 
   | Kegunaan: Mengubah nama kolom 'nama_asset' menjadi 'description' secara otomatis.
*/
Route::get('/perbaiki-database', function() {
    try {
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'nama_asset')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE products CHANGE nama_asset description TEXT NULL");
            return "BERHASIL! Kolom nama_asset sudah diubah menjadi description. Silakan coba import lagi.";
        }
        return "Database sudah dalam kondisi benar atau kolom nama_asset tidak ditemukan.";
    } catch (\Exception $e) {
        return "Gagal perbaiki: " . $e->getMessage();
    }
});

/* | [PROSEDUR DARURAT] | 
   | Jalankan URL '/tambah-kolom-karyawan' untuk menambahkan kolom detail karyawan.
   | Kegunaan: Menambahkan kolom job_position, job_level, join_date, phone_number ke tabel users.
*/

/* | [PROSEDUR DARURAT] | 
   | Jalankan URL '/tambah-kolom-sttb' untuk menambahkan kolom STTB.
   | Kegunaan: Menambahkan batch_id, quantity, pihak_pertama_id, tanggal_pinjam ke product_histories.
*/
Route::get('/tambah-kolom-sttb', function() {
    $results = [];
    $columns = [
        'batch_id'          => "ALTER TABLE `product_histories` ADD COLUMN `batch_id` VARCHAR(36) NULL AFTER `id`",
        'quantity'          => "ALTER TABLE `product_histories` ADD COLUMN `quantity` INT NOT NULL DEFAULT 1 AFTER `jenis_transaksi`",
        'pihak_pertama_id'  => "ALTER TABLE `product_histories` ADD COLUMN `pihak_pertama_id` BIGINT UNSIGNED NULL AFTER `quantity`",
        'tanggal_pinjam'    => "ALTER TABLE `product_histories` ADD COLUMN `tanggal_pinjam` DATE NULL AFTER `pihak_pertama_id`",
    ];
    foreach ($columns as $col => $sql) {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('product_histories', $col)) {
            try {
                \Illuminate\Support\Facades\DB::statement($sql);
                $results[] = "✅ Kolom '$col' berhasil ditambahkan.";
            } catch (\Exception $e) {
                $results[] = "❌ Gagal '$col': " . $e->getMessage();
            }
        } else {
            $results[] = "ℹ️ Kolom '$col' sudah ada.";
        }
    }
    return implode("<br>", $results) . "<br><br><b>Selesai! Fitur STTB siap digunakan.</b>";
});
Route::get('/tambah-kolom-karyawan', function() {
    $db = \Illuminate\Support\Facades\DB::connection()->getPdo();
    $results = [];

    $columns = [
        'job_position' => "ALTER TABLE `users` ADD COLUMN `job_position` VARCHAR(255) NULL AFTER `role`",
        'job_level'    => "ALTER TABLE `users` ADD COLUMN `job_level` VARCHAR(255) NULL AFTER `job_position`",
        'join_date'    => "ALTER TABLE `users` ADD COLUMN `join_date` DATE NULL AFTER `job_level`",
        'phone_number' => "ALTER TABLE `users` ADD COLUMN `phone_number` VARCHAR(20) NULL AFTER `join_date`",
    ];

    foreach ($columns as $col => $sql) {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', $col)) {
            try {
                \Illuminate\Support\Facades\DB::statement($sql);
                $results[] = "✅ Kolom '$col' berhasil ditambahkan.";
            } catch (\Exception $e) {
                $results[] = "❌ Gagal menambahkan '$col': " . $e->getMessage();
            }
        } else {
            $results[] = "ℹ️ Kolom '$col' sudah ada, dilewati.";
        }
    }

    return implode("<br>", $results) . "<br><br><b>Selesai! Silakan coba import karyawan lagi.</b>";
});

Route::get('/tambah-status-user', function() {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active' AFTER `role`");
        return "✅ Kolom 'status' berhasil ditambahkan ke tabel users.";
    }
    return "ℹ️ Kolom 'status' sudah ada.";
});

/* | [PROSEDUR] | Halaman Dashboard utama setelah Admin login */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/* | [GRUP ROUTE] | Rute yang memerlukan login (Authentication) */
Route::middleware('auth')->group(function () {
    /* | [PROSEDUR] | Mengelola profil pribadi admin yang sedang login */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // [RESOURCE] | Dashboard
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

    // [RESOURCE] | Manajemen Permintaan Aset (Asset Requests)
    // Rute GET statis harus didefinisikan SEBELUM resource agar tidak teroverride
    Route::get('asset-requests/{assetRequest}/print', [\App\Http\Controllers\AssetRequestController::class, 'print'])->name('asset-requests.print');
    Route::resource('asset-requests', \App\Http\Controllers\AssetRequestController::class)->middleware('permission:requests.view');
    // [RESOURCE] | Role & Hak Akses
    Route::middleware('permission:roles.manage')->group(function() {
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::get('roles/{role}/detail', [\App\Http\Controllers\RoleController::class, 'detail'])->name('roles.detail');
        Route::put('roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions');
    });
    // Approval & Rejection: dicek berdasarkan permission, bukan slug role
    Route::middleware('permission:requests.approve_stage_1|requests.approve_stage_2|requests.approve_stage_3|requests.approve_stage_4')->group(function() {
        Route::post('asset-requests/{assetRequest}/approve', [\App\Http\Controllers\AssetRequestController::class, 'approve'])->name('asset-requests.approve');
        Route::post('asset-requests/{assetRequest}/finalize', [\App\Http\Controllers\AssetRequestController::class, 'finalize'])->name('asset-requests.finalize');
        Route::post('asset-requests/{assetRequest}/reject', [\App\Http\Controllers\AssetRequestController::class, 'reject'])->name('asset-requests.reject');
    });
});

/* | [GRUP ROUTE] | AKSES DINAMIS (Berdasarkan Permission) */
Route::middleware(['auth'])->group(function () {
    // === [ MANAJEMEN ASET ] ===
    Route::get('/products', [AsetController::class, 'index'])->name('products.index')->middleware('permission:products.view');
    Route::get('/products/create', [AsetController::class, 'create'])->name('products.create')->middleware('permission:products.create');
    Route::post('/products', [AsetController::class, 'store'])->name('products.store')->middleware('permission:products.create');
    
    Route::get('/products-export', [AsetController::class, 'export'])->name('products.export')->middleware('permission:products.export');
    Route::post('/products-import', [AsetController::class, 'import'])->name('products.import')->middleware('permission:products.import');
    
    Route::get('/products/next-nomor/{department_id}', [AsetController::class, 'getNextNomor'])->name('products.nextNomor')->middleware('permission:products.create|products.edit');
    
    // Wildcard routes ditaruh di bawah agar tidak menabrak rute statis (seperti /products/create)
    Route::get('/products/{product}', [AsetController::class, 'show'])->name('products.show')->middleware('permission:products.view');
    Route::get('/products/{product}/edit', [AsetController::class, 'edit'])->name('products.edit')->middleware('permission:products.edit');
    Route::put('/products/{product}', [AsetController::class, 'update'])->name('products.update')->middleware('permission:products.edit');
    Route::get('/products/{product}/qr', [AsetController::class, 'printQr'])->name('products.qr')->middleware('permission:products.view');

    // === [ TRANSAKSI & STTB ] ===
    Route::get('/transfer/pinjam', [AsetController::class, 'pinjam'])->name('products.meminjam')->middleware('permission:transactions.transfer');
    Route::get('/transfer/kembali', [AsetController::class, 'kembali'])->name('products.kembali')->middleware('permission:transactions.transfer');
    Route::post('/transfer/store', [AsetController::class, 'storeTransfer'])->name('products.storeTransfer')->middleware('permission:transactions.transfer');
    Route::get('/sttb/{batch_id}', [AsetController::class, 'showSttb'])->name('sttb.show')->middleware('permission:transactions.transfer');

    // === [ DATA EMPLOYEE ] ===
    Route::get('/employees/data', [UserController::class, 'employeeData'])->name('employees.data')->middleware('permission:users.view_employee_data');
    Route::get('/employees/create', [UserController::class, 'createEmployee'])->name('employees.create')->middleware('permission:users.create_employee');
    Route::post('/employees/store', [UserController::class, 'storeEmployee'])->name('employees.store')->middleware('permission:users.create_employee');
    Route::get('/employees/{user}/edit-employee', [UserController::class, 'editEmployee'])->name('employees.edit')->middleware('permission:users.edit_employee');
    Route::put('/employees/{user}/update-employee', [UserController::class, 'updateEmployee'])->name('employees.update')->middleware('permission:users.edit_employee');
    Route::get('/employees/export', [UserController::class, 'export'])->name('employees.export')->middleware('permission:users.export');
    Route::post('/employees/import', [UserController::class, 'import'])->name('employees.import')->middleware('permission:users.import');
    Route::get('/employees/{user}/details', [UserController::class, 'details'])->name('employees.details')->middleware('permission:users.view_employee_data');

    // === [ MANAGEMENT ACCOUNT ] ===
    Route::get('/accounts', [UserController::class, 'accountManagement'])->name('accounts.index')->middleware('permission:users.view');
    Route::get('/accounts/create', [UserController::class, 'create'])->name('accounts.create')->middleware('permission:users.create');
    Route::post('/accounts', [UserController::class, 'store'])->name('accounts.store')->middleware('permission:users.create');
    Route::get('/accounts/{user}/edit', [UserController::class, 'edit'])->name('accounts.edit')->middleware('permission:users.edit');
    Route::put('/accounts/{user}', [UserController::class, 'update'])->name('accounts.update')->middleware('permission:users.edit');
    Route::get('/accounts/resigned', [UserController::class, 'resignedIndex'])->name('accounts.resigned')->middleware('permission:users.view_resigned');
    Route::post('/accounts/{user}/resign', [UserController::class, 'resign'])->name('accounts.resign')->middleware('permission:users.resign');
    Route::post('/accounts/{user}/make-admin', [UserController::class, 'makeAdmin'])->name('accounts.make-admin')->middleware('permission:users.edit_role');
    Route::delete('/employees/{user}', [UserController::class, 'destroy'])->name('employees.destroy')->middleware('permission:users.delete');

    // === [ MASTER DATA ] ===
    Route::resource('offices', \App\Http\Controllers\OfficeController::class)->middleware('permission:master.offices');
    Route::resource('classifications', \App\Http\Controllers\ClassificationController::class)->middleware('permission:master.classifications');
    Route::resource('departments', \App\Http\Controllers\DepartmentController::class)->middleware('permission:master.departments');

    // === [ PENGHAPUSAN ASET (TEMPAT SAMPAH) ] ===
    Route::delete('/products/{product}', [AsetController::class, 'destroy'])->name('products.destroy')->middleware('permission:products.delete');
    Route::get('/products-trash', [AsetController::class, 'trashIndex'])->name('products.trash')->middleware('permission:products.trash');
    Route::post('/products-trash/{id}/restore', [AsetController::class, 'restore'])->name('products.restore')->middleware('permission:products.trash');
    Route::delete('/products-trash/{id}/force', [AsetController::class, 'forceDelete'])->name('products.forceDelete')->middleware('permission:products.trash');

    // === [ RIWAYAT MUTASI ] ===
    Route::get('/history', [AsetController::class, 'historyIndex'])->name('history.index')->middleware('permission:transactions.edit_history');
    Route::get('/history/{productHistory}/edit', [AsetController::class, 'editHistory'])->name('history.edit')->middleware('permission:transactions.edit_history');
    Route::put('/history/{productHistory}', [AsetController::class, 'updateHistory'])->name('history.update')->middleware('permission:transactions.edit_history');
    Route::post('/history/{productHistory}/quick-return', [AsetController::class, 'quickReturn'])->name('history.quickReturn')->middleware('permission:transactions.edit_history');
    Route::delete('/history/{productHistory}/cancel', [AsetController::class, 'cancelHistory'])->name('history.cancel')->middleware('permission:transactions.edit_history');
});

/* | [GRUP ROUTE] | AKSES SCAN QR (Akses Publik via HP) */
// [PROSEDUR] | Menampilkan info lengkap aset
Route::get('/scan/{token}', [ScanController::class, 'show'])->name('scan.show');
// [PROSEDUR] | Menampilkan KHUSUS spesifikasi teknis (QR Spek)
Route::get('/scan/{token}/specs', [ScanController::class, 'showSpecs'])->name('scan.specs');
// [PROSEDUR] | Menampilkan KHUSUS info pemegang & kantor (QR Pemilik)
Route::get('/scan/{token}/owner', [ScanController::class, 'showOwner'])->name('scan.owner');
// [FUNGSI] | API untuk kebutuhan data sistem lain (JSON)
Route::get('/api/scan/{token}', [ScanController::class, 'apiShow'])->name('api.scan.show');
// [PROSEDUR] | Memperbarui info lokasi saat sedang di lapangan (Perlu Login)
Route::post('/scan/{token}/update', [ScanController::class, 'update'])->middleware('auth')->name('scan.update');

require __DIR__.'/auth.php';
