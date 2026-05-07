<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\ProductHistory;
use Illuminate\Http\Request;

/* |--------------------------------------------------------------------------
   | [KONTROLLER SCAN QR]
   |--------------------------------------------------------------------------
   | Kegunaan: Menangani permintaan saat seseorang melakukan scan kode QR.
   */

class ScanController extends Controller
{
    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman informasi publik setelah QR di-scan.
    */
    public function show($token)
    {
        $product = Aset::where('url_token', $token)
            ->with(['department', 'classification', 'histories.sender', 'histories.receiver.office'])
            ->firstOrFail();

        $currentHistory = $product->histories->sortByDesc('created_at')->first();

        return view('scan.show', compact('product', 'currentHistory'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan KHUSUS Spesifikasi Teknis Aset (Processor, RAM, dll).
    */
    public function showSpecs($token)
    {
        $product = Aset::where('url_token', $token)->with(['classification'])->firstOrFail();
        return view('scan.specs', compact('product'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan KHUSUS Informasi Pemilik & Lokasi Kantor Aset.
    */
    public function showOwner($token)
    {
        $product = Aset::where('url_token', $token)
            ->with(['department', 'histories.receiver.office'])
            ->firstOrFail();

        $currentHistory = $product->histories->sortByDesc('created_at')->first();
        return view('scan.owner', compact('product', 'currentHistory'));
    }

    /* | [FUNGSI] | 
       | Kegunaan: API yang mengembalikan data spek aset dalam format JSON untuk kebutuhan eksternal.
    */
    public function apiShow($token)
    {
        $product = Aset::where('url_token', $token)
            ->with(['department', 'classification'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data'   => $product
        ]);
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Memperbarui data pemilik/lokasi secara cepat melalui halaman scan (jika login).
    */
    public function update(Request $request, $token)
    {
        $product = Aset::where('url_token', $token)->firstOrFail();

        $request->validate([
            'pemilik_kantor' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
        ]);

        // [PROSEDUR] | Mencatat riwayat perubahan lokasi ke database
        ProductHistory::create([
            'product_id' => $product->id,
            'dioper_oleh' => auth()->id(),
            'diterima_oleh' => null,
            'lokasi_baru' => $request->lokasi,
        ]);

        // [PROSEDUR] | Memperbarui informasi aset secara langsung
        $product->update([
            'pemilik_kantor' => $request->pemilik_kantor,
            'lokasi' => $request->lokasi,
        ]);

        return redirect()->route('scan.show', $token)->with('success', 'Data produk berhasil diupdate.');
    }
}
