<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

/* |--------------------------------------------------------------------------
   | [CONTROLLER KANTOR]
   |--------------------------------------------------------------------------
   | Kegunaan: Mengelola data lokasi kantor.
   */

class OfficeController extends Controller
{
    public function __construct()
    {
        // Permission check moved to routes/web.php
    }

    /* | [PROSEDUR] | Menampilkan daftar seluruh kantor */
    public function index()
    {
        $offices = Office::all();
        return view('offices.index', compact('offices'));
    }

    /* | [PROSEDUR] | Menampilkan form tambah kantor baru */
    public function create()
    {
        return view('offices.create');
    }

    /* | [PROSEDUR] | Menyimpan data kantor baru ke database */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kantor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);
        Office::create($request->all());
        return redirect()->route('offices.index')->with('success', __('Kantor berhasil ditambahkan.'));
    }

    public function edit(Office $office)
    {
        return view('offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'nama_kantor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);
        $office->update($request->all());
        return redirect()->route('offices.index')->with('success', __('Kantor berhasil diupdate.'));
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return redirect()->route('offices.index')->with('success', __('Kantor berhasil dihapus.'));
    }
}
