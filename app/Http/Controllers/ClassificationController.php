<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;

/* |--------------------------------------------------------------------------
   | [CONTROLLER KLASIFIKASI]
   |--------------------------------------------------------------------------
   | Kegunaan: Mengelola kategori barang (Laptop, Monitor, HP, dll).
   */

class ClassificationController extends Controller
{
    /* | [PROSEDUR] | Menampilkan daftar seluruh kategori barang */
    public function index()
    {
        $classifications = Classification::all();
        return view('classifications.index', compact('classifications'));
    }

    /* | [PROSEDUR] | Menampilkan form tambah kategori baru */
    public function create()
    {
        return view('classifications.create');
    }

    /* | [PROSEDUR] | Menyimpan kategori baru ke database */
    public function store(Request $request)
    {
        $request->validate([
            'nama_klasifikasi' => 'required|string|max:255',
        ]);
        Classification::create($request->all());
        return redirect()->route('classifications.index')->with('success', __('Klasifikasi berhasil ditambahkan.'));
    }

    public function edit(Classification $classification)
    {
        return view('classifications.edit', compact('classification'));
    }

    public function update(Request $request, Classification $classification)
    {
        $request->validate([
            'nama_klasifikasi' => 'required|string|max:255',
        ]);
        $classification->update($request->all());
        return redirect()->route('classifications.index')->with('success', __('Klasifikasi berhasil diupdate.'));
    }

    public function destroy(Classification $classification)
    {
        $classification->delete();
        return redirect()->route('classifications.index')->with('success', __('Klasifikasi berhasil dihapus.'));
    }
}
