<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/* |--------------------------------------------------------------------------
   | [CONTROLLER USER / KARYAWAN]
   |--------------------------------------------------------------------------
   | Kegunaan: Mengelola data karyawan dan hak akses admin.
   */

class UserController extends Controller
{
    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan daftar seluruh karyawan (kecuali admin yang sedang login).
    */
    public function index(Request $request)
    {
        $searchTerm   = $request->get('q');
        $filterDept   = $request->get('department_id');
        $filterKantor = $request->get('office_id');
        $filterStatus = $request->get('status', 'active');

        $query = User::with(['office', 'department'])->where('id', '!=', auth()->id());

        // Cek apakah kolom status sudah ada di database (untuk mencegah error sebelum patch dijalankan)
        if ($filterStatus && \Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
            $query->where('status', $filterStatus);
        }

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('employee_id', 'like', "%{$searchTerm}%")
                  ->orWhere('job_position', 'like', "%{$searchTerm}%");
            });
        }
        if ($filterDept)   $query->where('department_id', $filterDept);
        if ($filterKantor) $query->where('office_id', $filterKantor);

        $users        = $query->paginate(25)->withQueryString();
        $departments  = \App\Models\Department::orderBy('nama_departemen')->get();
        $offices      = \App\Models\Office::orderBy('nama_kantor')->get();

        return view('users.index', compact('users', 'searchTerm', 'filterDept', 'filterKantor', 'filterStatus', 'departments', 'offices'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan form untuk menambah karyawan baru.
    */
    public function create()
    {
        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();
        return view('users.create', compact('offices', 'departments'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menyimpan data karyawan baru ke database.
    */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'   => 'nullable|string|max:255|unique:users',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'role'          => 'required|in:user,admin,super_admin',
            'office_id'     => 'required|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_position'  => 'nullable|string|max:255',
            'job_level'     => 'nullable|string|max:255',
            'join_date'     => 'nullable|date',
            'phone_number'  => 'nullable|string|max:20',
            'password'      => 'required_if:role,admin,super_admin|nullable|min:8',
        ]);

        User::create([
            'employee_id'   => $request->employee_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => $request->password ? bcrypt($request->password) : null,
            'role'          => $request->role,
            'office_id'     => $request->office_id,
            'department_id' => $request->department_id,
            'job_position'  => $request->job_position,
            'job_level'     => $request->job_level,
            'join_date'     => $request->join_date,
            'phone_number'  => $request->phone_number,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun karyawan berhasil dibuat.');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman untuk mengedit data karyawan.
    */
    public function edit(User $user)
    {
        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();
        return view('users.edit', compact('user', 'offices', 'departments'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menyimpan perubahan data karyawan ke database.
    */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'employee_id'   => 'nullable|string|max:255|unique:users,employee_id,' . $user->id,
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role'          => 'required|in:user,admin,super_admin',
            'office_id'     => 'required|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_position'  => 'nullable|string|max:255',
            'job_level'     => 'nullable|string|max:255',
            'join_date'     => 'nullable|date',
            'phone_number'  => 'nullable|string|max:20',
            'password'      => 'nullable|min:8',
        ]);

        $data = [
            'employee_id'   => $request->employee_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'office_id'     => $request->office_id,
            'department_id' => $request->department_id,
            'job_position'  => $request->job_position,
            'job_level'     => $request->job_level,
            'join_date'     => $request->join_date,
            'phone_number'  => $request->phone_number,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mempromosikan karyawan biasa menjadi Admin sistem.
    */
    public function makeAdmin(User $user)
    {
        if ($user->role !== 'admin') {
            $user->update(['role' => 'admin']);
            return back()->with('success', 'User berhasil dijadikan Admin.');
        }
        return back()->with('error', 'User sudah menjadi Admin.');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menghapus data user dari database (dengan proteksi akun sendiri).
    */
    public function details(User $user)
    {
        // Ambil peminjaman aktif (terakhir diterima oleh user ini dan belum dikembalikan)
        // Logika aktif: Cari record terakhir untuk setiap product_id. Jika diterima_oleh == user_id dan jenis_transaksi == 'meminjam'
        $activeBorrowings = \App\Models\ProductHistory::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')->from('product_histories')->groupBy('product_id');
        })->where('diterima_oleh', $user->id)
          ->where('jenis_transaksi', 'meminjam')
          ->with('product.department')
          ->get();

        $history = \App\Models\ProductHistory::where('diterima_oleh', $user->id)
            ->orWhere('pihak_pertama_id', $user->id)
            ->with(['product', 'sender', 'receiver'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'user' => $user,
            'active_borrowings' => $activeBorrowings,
            'history' => $history
        ]);
    }

    public function resign(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa meresign diri sendiri.');
        }

        // Cek apakah masih ada barang yang dipinjam
        $activeCount = \App\Models\ProductHistory::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')->from('product_histories')->groupBy('product_id');
        })->where('diterima_oleh', $user->id)
          ->where('jenis_transaksi', 'meminjam')
          ->count();

        if ($activeCount > 0) {
            return back()->with('error', 'Gagal Resign: Karyawan masih memegang ' . $activeCount . ' aset. Kembalikan semua aset terlebih dahulu.');
        }

        $user->update(['status' => 'resigned']);
        return redirect()->route('users.index')->with('success', 'Karyawan ' . $user->name . ' telah berhasil di-resign.');
    }

    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri agar tidak terkunci keluar
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mengekspor daftar karyawan ke format Excel.
    */
    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersExport, 'data-karyawan.xlsx');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mengimpor data karyawan dari file Excel.
    */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UsersImport, $request->file('file'));

        return back()->with('success', 'Data karyawan berhasil diimpor.');
    }
}
