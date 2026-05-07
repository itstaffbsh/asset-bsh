<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

/* |--------------------------------------------------------------------------
   | [CONTROLLER DEPARTEMEN]
   |--------------------------------------------------------------------------
   | Kegunaan: Mengelola data departemen (IT, HR, ACCT, dll).
   */

class DepartmentController extends Controller
{
    /* | [PROSEDUR] | Menampilkan daftar seluruh departemen */
    public function index()
    {
        $departments = Department::all();
        return view('departments.index', compact('departments'));
    }

    /* | [PROSEDUR] | Menampilkan form tambah departemen */
    public function create()
    {
        return view('departments.create');
    }

    /* | [PROSEDUR] | Menyimpan data departemen baru ke database */
    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'kode_asset' => 'required|string|max:10',
        ]);
        Department::create($request->all());
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'kode_asset' => 'required|string|max:10',
        ]);
        $department->update($request->all());
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diupdate.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
