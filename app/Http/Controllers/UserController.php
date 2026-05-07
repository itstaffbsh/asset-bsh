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
       | Kegunaan: Menampilkan daftar karyawan untuk module Data Employee (Import/Export/View).
    */
    public function employeeData(Request $request)
    {
        $searchTerm   = $request->get('q');
        $filterDept   = $request->get('department_id');
        $filterKantor = $request->get('office_id');

        $filterStatus = $request->get('status', 'active');
        $query = User::with(['office', 'department'])->where('id', '!=', auth()->id());

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

        if (!in_array(auth()->user()->role, ['superadmin', 'managing_director'])) {
            $query->whereNotIn('role', ['superadmin', 'managing_director']);
        }

        if ($filterDept)   $query->where('department_id', $filterDept);
        if ($filterKantor) $query->where('office_id', $filterKantor);

        $users        = $query->paginate(25)->withQueryString();
        $departments  = \App\Models\Department::orderBy('nama_departemen')->get();
        $offices      = \App\Models\Office::orderBy('nama_kantor')->get();

        return view('users.employee_data', compact('users', 'searchTerm', 'filterDept', 'filterKantor', 'filterStatus', 'departments', 'offices'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan daftar akun untuk module Management Account (Create/Edit/Resign).
    */
    public function accountManagement(Request $request)
    {
        $searchTerm   = $request->get('q');
        $filterDept   = $request->get('department_id');
        $filterKantor = $request->get('office_id');

        $query = User::with(['office', 'department'])->where('id', '!=', auth()->id());

        // Halaman ini hanya untuk akun aktif
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
            $query->where('status', 'active');
        }

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('employee_id', 'like', "%{$searchTerm}%")
                  ->orWhere('job_position', 'like', "%{$searchTerm}%");
            });
        }

        if (!in_array(auth()->user()->role, ['superadmin', 'managing_director'])) {
            $query->whereNotIn('role', ['superadmin', 'managing_director']);
        }

        if ($filterDept)   $query->where('department_id', $filterDept);
        if ($filterKantor) $query->where('office_id', $filterKantor);

        $users        = $query->paginate(25)->withQueryString();
        $departments  = \App\Models\Department::orderBy('nama_departemen')->get();
        $offices      = \App\Models\Office::orderBy('nama_kantor')->get();

        return view('users.index', compact('users', 'searchTerm', 'filterDept', 'filterKantor', 'departments', 'offices'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan form untuk menambah karyawan baru.
    */
    public function create()
    {
        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();
        $roles = \App\Models\Role::all(); // Fetch all roles
        return view('users.create', compact('offices', 'departments', 'roles'));
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
            'role_id'       => 'required|exists:roles,id',
            'office_id'     => 'required|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_position'  => 'nullable|string|max:255',
            'job_level'     => 'nullable|string|max:255',
            'join_date'     => 'nullable|date',
            'phone_number'  => 'nullable|string|max:20',
            'password'      => 'nullable|min:8',
            'signature'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $role = \App\Models\Role::find($request->role_id);

        // Security check: Only Super Admin can assign Super Admin role
        if ($role->slug === 'superadmin' && !auth()->user()->hasRoleLevel('superadmin')) {
            return back()->with('error', __('Hanya Super Admin yang bisa memberikan role ini.'));
        }

        User::create([
            'employee_id'   => $request->employee_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => $request->password ? bcrypt($request->password) : null,
            'role_id'       => $request->role_id,
            'role'          => $role->slug, // Keep slug for legacy support
            'office_id'     => $request->office_id,
            'department_id' => $request->department_id,
            'job_position'  => $request->job_position,
            'job_level'     => $request->job_level,
            'join_date'     => $request->join_date,
            'phone_number'  => $request->phone_number,
            'signature_path'=> $request->hasFile('signature') ? $request->file('signature')->store('signatures', 'public') : null,
            'status'        => 'active',
        ]);

        return redirect()->route('accounts.index')->with('success', __('Akun karyawan berhasil dibuat.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman untuk mengedit data karyawan.
    */
    public function edit(User $user)
    {
        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();
        $roles = \App\Models\Role::all();
        return view('users.edit', compact('user', 'offices', 'departments', 'roles'));
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
            'role_id'       => 'required|exists:roles,id',
            'office_id'     => 'required|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_position'  => 'nullable|string|max:255',
            'job_level'     => 'nullable|string|max:255',
            'join_date'     => 'nullable|date',
            'phone_number'  => 'nullable|string|max:20',
            'password'      => 'nullable|min:8',
            'signature'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $role = \App\Models\Role::find($request->role_id);

        // Security check
        if ($role->slug === 'superadmin' && !auth()->user()->hasRoleLevel('superadmin')) {
            return back()->with('error', __('Hanya Super Admin yang bisa memberikan role ini.'));
        }

        $data = [];

        // Update Profil Dasar
        if (auth()->user()->hasPermission('users.edit_profile')) {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['phone_number'] = $request->phone_number;
            
            if ($request->hasFile('signature')) {
                // Hapus signature lama jika ada
                if ($user->signature_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->signature_path);
                }
                $data['signature_path'] = $request->file('signature')->store('signatures', 'public');
            }
        }

        // Update Penempatan
        if (auth()->user()->hasPermission('users.edit_placement')) {
            $data['office_id'] = $request->office_id;
            $data['department_id'] = $request->department_id;
        }

        // Update Informasi Pekerjaan
        if (auth()->user()->hasPermission('users.edit_job')) {
            $data['employee_id'] = $request->employee_id;
            $data['job_position'] = $request->job_position;
            $data['job_level'] = $request->job_level;
            $data['join_date'] = $request->join_date;
        }

        // Update Role & Password
        if (auth()->user()->hasPermission('users.edit_role')) {
            $data['role_id'] = $request->role_id;
            $data['role'] = $role->slug;
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }
        }

        if (empty($data)) {
            return back()->with('error', __('Anda tidak memiliki izin untuk mengubah bagian manapun dari akun ini.'));
        }

        $user->update($data);

        return redirect()->route('accounts.index')->with('success', __('Data akun berhasil diperbarui.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mempromosikan karyawan biasa menjadi Admin sistem.
    */
    public function makeAdmin(User $user)
    {
        // Cek apakah admin yang login punya hak untuk menaikkan level ke Admin (level 2)
        // Menurut aturan: Super Admin ke atas bisa melakukan ini.
        if (!auth()->user()->hasRoleLevel('superadmin')) {
            return back()->with('error', __('Maaf, Anda tidak memiliki hak untuk mengubah role user ini.'));
        }

        if ($user->role !== 'admin') {
            $user->update(['role' => 'admin']);
            return back()->with('success', __('User berhasil dijadikan Admin.'));
        }
        return back()->with('error', __('User sudah menjadi Admin.'));
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
        // Pengecekan Akses: Hanya Manager (level 4) ke atas yang boleh mengakses fitur Resign
        if (!auth()->user()->hasRoleLevel('manager')) {
            abort(403, 'Maaf, hanya level Manager ke atas yang bisa melakukan proses Resign.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', __('Anda tidak bisa meresign diri sendiri.'));
        }

        // Cek apakah masih ada barang yang dipinjam
        $activeCount = \App\Models\ProductHistory::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')->from('product_histories')->groupBy('product_id');
        })->where('diterima_oleh', $user->id)
          ->where('jenis_transaksi', 'meminjam')
          ->count();

        if ($activeCount > 0) {
            return back()->with('error', __('Gagal Resign: Karyawan masih memegang :count aset. Kembalikan semua aset terlebih dahulu.', ['count' => $activeCount]));
        }

        $user->update([
            'status' => 'resigned',
            'resigned_at' => now(),
        ]);
        return redirect()->route('accounts.resigned')->with('success', 'Karyawan ' . $user->name . ' telah berhasil di-resign.');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan daftar karyawan yang sudah resign.
    */
    public function resignedIndex(Request $request)
    {
        $searchTerm = $request->get('q');
        $query = User::with(['office', 'department'])->where('status', 'resigned');

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('employee_id', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->latest('resigned_at')->paginate(25)->withQueryString();
        return view('users.resigned', compact('users', 'searchTerm'));
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

        return back()->with('success', __('Data karyawan berhasil diimpor.'));
    }
}
