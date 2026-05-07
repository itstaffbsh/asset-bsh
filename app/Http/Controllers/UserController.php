<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

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
        abort_if(!auth()->user()->hasPermission('users.view_employee_data'), 403);

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
        abort_if(!auth()->user()->hasPermission('users.view'), 403);

        $searchTerm   = $request->get('q');
        $filterDept   = $request->get('department_id');
        $filterKantor = $request->get('office_id');

        $query = User::with(['office', 'department'])->where('id', '!=', auth()->id());

        // Halaman ini hanya untuk akun aktif yang sudah punya password
        $query->whereNotNull('password');
        
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
       | Kegunaan: Menampilkan form untuk menambah akun (memilih dari karyawan).
    */
    public function create()
    {
        abort_if(!auth()->user()->hasPermission('users.create'), 403);

        $employees = User::whereNull('password')->where('status', 'active')->orderBy('name')->get();
        $roles = \App\Models\Role::all(); // Fetch all roles
        return view('users.create', compact('employees', 'roles'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menyimpan akun karyawan (menambahkan password dan role).
    */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('users.create'), 403);

        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'role_id'       => 'required|exists:roles,id',
            'password'      => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $role = \App\Models\Role::find($request->role_id);
        $user = User::findOrFail($request->user_id);

        if ($user->password !== null) {
            return back()->with('error', __('Karyawan ini sudah memiliki akun.'));
        }

        // Security check: Only user with permission can assign Super Admin role
        if ($role->slug === 'superadmin' && !auth()->user()->hasPermission('users.edit_role')) {
            return back()->with('error', __('Hanya user dengan izin khusus yang bisa memberikan role ini.'));
        }

        $user->update([
            'password'      => bcrypt($request->password),
            'role_id'       => $request->role_id,
            'role'          => $role->slug,
        ]);

        return redirect()->route('accounts.index')->with('success', __('Akun karyawan berhasil dibuat.'));
    }

    /* | [PROSEDUR DATA EMPLOYEE] | */
    public function createEmployee()
    {
        abort_if(!auth()->user()->hasPermission('users.create_employee'), 403);

        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();
        return view('users.employee_create', compact('offices', 'departments'));
    }

    public function storeEmployee(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('users.create_employee'), 403);

        $request->validate([
            'employee_id'   => 'nullable|string|max:255|unique:users',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'office_id'     => 'required|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_position'  => 'required|string|max:255',
            'job_level'     => 'required|in:Director,Manager,Assistant Manager,Supervisor,Staff,Assistant Director',
            'join_date'     => 'nullable|date',
            'phone_number'  => 'nullable|string|max:20',
        ]);

        User::create([
            'employee_id'   => $request->employee_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'office_id'     => $request->office_id,
            'department_id' => $request->department_id,
            'job_position'  => $request->job_position,
            'job_level'     => $request->job_level,
            'join_date'     => $request->join_date,
            'phone_number'  => $request->phone_number,
            'role'          => 'employee',
            'status'        => 'active',
        ]);

        return redirect()->route('employees.data')->with('success', __('Data Karyawan berhasil ditambahkan.'));
    }

    public function editEmployee(User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.edit_employee'), 403);

        $offices = \App\Models\Office::all();
        $departments = \App\Models\Department::all();
        return view('users.employee_edit', compact('user', 'offices', 'departments'));
    }

    public function updateEmployee(Request $request, User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.edit_employee'), 403);

        $request->validate([
            'employee_id'   => 'nullable|string|max:255|unique:users,employee_id,' . $user->id,
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'office_id'     => 'required|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_position'  => 'required|string|max:255',
            'job_level'     => 'required|in:Director,Manager,Assistant Manager,Supervisor,Staff,Assistant Director',
            'join_date'     => 'nullable|date',
            'phone_number'  => 'nullable|string|max:20',
        ]);

        $user->update([
            'employee_id'   => $request->employee_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'office_id'     => $request->office_id,
            'department_id' => $request->department_id,
            'job_position'  => $request->job_position,
            'job_level'     => $request->job_level,
            'join_date'     => $request->join_date,
            'phone_number'  => $request->phone_number,
        ]);

        return redirect()->route('employees.data')->with('success', __('Data Karyawan berhasil diperbarui.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman untuk mengedit data karyawan.
    */
    public function edit(User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.edit'), 403);

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
        abort_if(!auth()->user()->hasPermission('users.edit'), 403);

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
        if ($role->slug === 'superadmin' && !auth()->user()->hasPermission('users.edit_role')) {
            return back()->with('error', __('Hanya user dengan izin khusus yang bisa memberikan role ini.'));
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
        abort_if(!auth()->user()->hasPermission('users.resign'), 403);

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
        abort_if(!auth()->user()->hasPermission('users.view_resigned'), 403);

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
        abort_if(!auth()->user()->hasPermission('users.export'), 403);

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersExport, 'data-karyawan.xlsx');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mengimpor data karyawan dari file Excel.
    */
    public function import(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('users.import'), 403);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UsersImport, $request->file('file'));
            return back()->with('success', __('Data karyawan berhasil diimpor.'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMsg = 'Gagal Import: <br>';
            foreach ($failures as $failure) {
                $errorMsg .= "Baris {$failure->row()}: " . implode(', ', $failure->errors()) . "<br>";
            }
            return back()->with('error', $errorMsg);
        } catch (\Exception $e) {
            return back()->with('error', __('Gagal mengimpor data: ') . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        abort_if(!auth()->user()->hasPermission('users.delete'), 403);

        if ($user->id === auth()->id()) {
            return back()->with('error', __('Anda tidak bisa menghapus diri sendiri.'));
        }

        // Cek apakah masih ada barang yang dipinjam
        $activeCount = \App\Models\ProductHistory::whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')->from('product_histories')->groupBy('product_id');
        })->where('diterima_oleh', $user->id)
          ->where('jenis_transaksi', 'meminjam')
          ->count();

        if ($activeCount > 0) {
            return back()->with('error', __('Gagal Hapus: Karyawan masih memegang :count aset. Kembalikan semua aset terlebih dahulu.', ['count' => $activeCount]));
        }

        $user->delete();
        return back()->with('success', __('Karyawan :name berhasil dihapus.', ['name' => $user->name]));
    }
}
