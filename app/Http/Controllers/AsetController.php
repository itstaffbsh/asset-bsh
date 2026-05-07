<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Department;
use App\Models\Aset;
use App\Models\ProductHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

/* |--------------------------------------------------------------------------
   | [KONTROLLER MANAJEMEN ASET]
   |--------------------------------------------------------------------------
   | Kegunaan: Sebagai pusat pengaturan (Otak) untuk mengelola data Aset.
   */
class AsetController extends Controller
{
    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan daftar semua aset yang ada.
    */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('products.view'), 403);
        $searchTerm    = $request->get('q');
        $filterDept    = $request->get('department_id');
        $filterKantor  = $request->get('office_id');
        $filterKategori = $request->get('classification_id');

        $query = Aset::with(['department', 'classification', 'histories.receiver.office']);

        // [KHUSUS ROLE USER] | Hanya boleh melihat barang yang tidak sedang dipinjam
        if (auth()->user()->role === 'user') {
            $query->whereDoesntHave('histories', function($q) {
                $q->whereIn('id', function($sub) {
                    $sub->selectRaw('MAX(id)')->from('product_histories')->groupBy('product_id');
                })->whereNotNull('diterima_oleh');
            });
        }

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhere('nomor_unik', 'like', "%{$searchTerm}%")
                  ->orWhere('serial_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('classification', fn($cq) => $cq->where('nama_klasifikasi', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('department', fn($dq) => $dq->where('nama_departemen', 'like', "%{$searchTerm}%"))
                  ->orWhereHas('histories.receiver', fn($rq) => $rq->where('name', 'like', "%{$searchTerm}%"));
            });
        }
        if ($filterDept)     $query->where('department_id', $filterDept);
        if ($filterKategori) $query->where('classification_id', $filterKategori);
        if ($filterKantor) {
            // Filter berdasarkan kantor pemegang saat ini
            $query->whereHas('histories', function($q) use ($filterKantor) {
                $q->whereIn('id', function($sub) {
                    $sub->selectRaw('MAX(id)')->from('product_histories')->groupBy('product_id');
                })->whereHas('receiver', fn($r) => $r->where('office_id', $filterKantor));
            });
        }

        $products       = $query->paginate(25)->withQueryString();
        $departments    = Department::orderBy('nama_departemen')->get();
        $offices        = \App\Models\Office::orderBy('nama_kantor')->get();
        $classifications = Classification::orderBy('nama_klasifikasi')->get();

        return view('products.index', compact('products', 'searchTerm', 'filterDept', 'filterKantor', 'filterKategori', 'departments', 'offices', 'classifications'));
    }

    /* | [FUNGSI] | 
       | Kegunaan: Mengunduh data aset ke file Excel.
    */
    public function export()
    {
        abort_if(!auth()->user()->hasPermission('products.export'), 403);
        return Excel::download(new ProductsExport, 'data_aset_it.xlsx');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mengambil data dari Excel dan memasukkannya ke database.
    */
    public function import(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('products.import'), 403);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new ProductsImport, $request->file('file'));
        return back()->with('success', __('Data aset berhasil diimport.'));
    }

    /* | [PROSEDUR] | \n       | Kegunaan: Menampilkan form untuk menambah aset baru.\n    */
    public function create()
    {
        abort_if(!auth()->user()->hasPermission('products.create'), 403);
        $departments = Department::all();
        $classifications = Classification::all();
        return view('products.create', compact('departments', 'classifications'));
    }

    /* | [FUNGSI API] | 
       | Kegunaan: Mengembalikan nomor urut berikutnya untuk suatu departemen.
       | Digunakan oleh JavaScript di form tambah aset untuk auto-fill nomor.
    */
    public function getNextNomor($departmentId)
    {
        // Ambil nomor unik tertinggi di departemen ini (termasuk yang soft-deleted)
        $last = Aset::withTrashed()
            ->where('department_id', $departmentId)
            ->orderByRaw('CAST(nomor_unik AS UNSIGNED) DESC')
            ->value('nomor_unik');

        $nextNumber = $last ? ((int) $last) + 1 : 1;

        return response()->json([
            'next_nomor' => str_pad($nextNumber, 5, '0', STR_PAD_LEFT), // Format: 00001, 00002, ...
        ]);
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menyimpan data aset baru ke database.
    */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('products.create'), 403);
        /* | [ARRAY] | Daftar aturan validasi input */
        $request->validate([
            'description'      => 'required|string|max:255',
            'classification_id'=> 'required|exists:classifications,id',
            'department_id'    => 'required|exists:departments,id',
            'nomor_unik'       => 'required|string',
            'harga'            => 'nullable|string',
        ]);

        $harga = $request->harga ? (int) str_replace('.', '', $request->harga) : null;

        Aset::create([
            'description'       => $request->description,
            'classification_id' => $request->classification_id,
            'department_id'     => $request->department_id,
            'nomor_unik'        => strtoupper($request->nomor_unik),
            'harga'             => $harga,
            'url_token'         => Str::random(40),
            // Menyimpan spek teknis
            'serial_number'     => $request->serial_number,
            'processor'         => $request->processor,
            'ram'               => $request->ram,
            'new_ram'           => $request->new_ram,
            'ssd'               => $request->ssd,
            'new_ssd'           => $request->new_ssd,
            'os'                => $request->os,
            'screen_id'         => $request->screen_id,
            'imei'              => $request->imei,
            'mac_address'       => $request->mac_address,
            'device_class'      => $request->device_class,
        ]);

        return redirect()->route('products.index')->with('success', __('Aset berhasil ditambahkan.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan detail lengkap dari satu aset.
    */
    public function show($id)
    {
        abort_if(!auth()->user()->hasPermission('products.view'), 403);
        $product = Aset::with(['department', 'classification', 'histories.sender', 'histories.receiver.office'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman untuk mengubah data aset.
    */
    public function edit($id)
    {
        abort_if(!auth()->user()->hasPermission('products.edit'), 403);
        $product = Aset::findOrFail($id);
        $departments = Department::all();
        $classifications = Classification::all();
        return view('products.edit', compact('product', 'departments', 'classifications'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menyimpan perubahan data aset ke database.
    */
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->hasPermission('products.edit'), 403);
        $product = Aset::findOrFail($id);
        /* | [ARRAY] | Aturan validasi pembaruan data */
        $request->validate([
            'description'      => 'required|string|max:255',
            'classification_id'=> 'required|exists:classifications,id',
            'department_id'    => 'required|exists:departments,id',
            'nomor_unik'       => 'required|string',
        ]);

        $harga = $request->harga ? (int) str_replace('.', '', $request->harga) : null;

        $product->update([
            'description'       => $request->description,
            'classification_id' => $request->classification_id,
            'department_id'     => $request->department_id,
            'nomor_unik'        => strtoupper($request->nomor_unik),
            'harga'             => $harga,
            // Menyimpan perubahan spek teknis
            'serial_number'     => $request->serial_number,
            'processor'         => $request->processor,
            'ram'               => $request->ram,
            'new_ram'           => $request->new_ram,
            'ssd'               => $request->ssd,
            'new_ssd'           => $request->new_ssd,
            'os'                => $request->os,
            'screen_id'         => $request->screen_id,
            'imei'              => $request->imei,
            'mac_address'       => $request->mac_address,
            'device_class'      => $request->device_class,
        ]);

        return redirect()->route('products.index')->with('success', __('Aset berhasil diperbarui.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menghapus aset (Soft Delete / Pindah ke Sampah).
    */
    public function destroy(Request $request, $id)
    {
        abort_if(!auth()->user()->hasPermission('products.delete'), 403);
        $product = Aset::findOrFail($id);
        $product->update([
            'deletion_reason' => $request->deletion_reason,
            'selling_price'   => $request->selling_price ? (int) str_replace('.', '', $request->selling_price) : null,
        ]);
        $product->delete();
        return redirect()->route('products.index')->with('success', __('Aset dipindahkan ke sampah.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan daftar aset yang sudah dihapus (Sampah).
    */
    public function trashIndex()
    {
        abort_if(!auth()->user()->hasPermission('products.trash'), 403);
        $trashedProducts = Aset::onlyTrashed()->with(['department', 'classification'])->get();
        return view('products.trash', compact('trashedProducts'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mengembalikan aset dari tempat sampah ke daftar aktif.
    */
    public function restore($id)
    {
        abort_if(!auth()->user()->hasPermission('products.trash'), 403);
        Aset::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('products.trash')->with('success', __('Aset dikembalikan.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menghapus aset secara permanen dari database.
    */
    public function forceDelete($id)
    {
        abort_if(!auth()->user()->hasPermission('products.trash'), 403);
        $product = Aset::onlyTrashed()->findOrFail($id);
        $product->histories()->delete();
        $product->forceDelete();
        return redirect()->route('products.trash')->with('success', __('Aset dihapus permanen.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan dan mencetak kode QR aset.
    */
    public function printQr($id)
    {
        abort_if(!auth()->user()->hasPermission('products.view'), 403);
        $product = Aset::findOrFail($id);
        $url = route('scan.show', $product->url_token);
        $qrCode = QrCode::size(300)->generate($url);
        return view('products.qr', compact('product', 'qrCode', 'url'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan seluruh riwayat transaksi (Master Riwayat).
    */
    public function historyIndex(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('transactions.view_history'), 403);
        $searchTerm     = $request->get('q');
        $filterDept     = $request->get('department_id');
        $filterKantor   = $request->get('office_id');
        $filterKategori = $request->get('classification_id');

        $query = ProductHistory::with(['product.department', 'product.classification', 'sender', 'receiver.office'])->latest();

        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('product', fn($pq) =>
                    $pq->where('description', 'like', "%{$searchTerm}%")
                       ->orWhere('nomor_unik', 'like', "%{$searchTerm}%")
                )
                ->orWhereHas('receiver', fn($rq) => $rq->where('name', 'like', "%{$searchTerm}%"))
                ->orWhereHas('sender',   fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"));
            });
        }
        if ($filterDept)     $query->whereHas('product', fn($q) => $q->where('department_id', $filterDept));
        if ($filterKategori) $query->whereHas('product', fn($q) => $q->where('classification_id', $filterKategori));
        if ($filterKantor)   $query->whereHas('receiver', fn($q) => $q->where('office_id', $filterKantor));

        $histories       = $query->paginate(25)->withQueryString();
        $departments     = Department::orderBy('nama_departemen')->get();
        $offices         = \App\Models\Office::orderBy('nama_kantor')->get();
        $classifications = Classification::orderBy('nama_klasifikasi')->get();

        return view('products.history_index', compact(
            'histories', 'searchTerm', 'filterDept', 'filterKantor', 'filterKategori',
            'departments', 'offices', 'classifications'
        ));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman edit riwayat tertentu.
    */
    public function editHistory(ProductHistory $productHistory)
    {
        abort_if(!auth()->user()->hasPermission('transactions.edit_history'), 403);
        $users = User::where('role', 'user')->get();
        return view('products.edit_history', compact('productHistory', 'users'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Memperbarui data riwayat.
    */
    public function updateHistory(Request $request, ProductHistory $productHistory)
    {
        abort_if(!auth()->user()->hasPermission('transactions.edit_history'), 403);
        $request->validate(['diterima_oleh' => 'required|exists:users,id']);
        $productHistory->update(['diterima_oleh' => $request->diterima_oleh]);
        return redirect()->route('products.show', $productHistory->product_id)->with('success', __('Riwayat diupdate.'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Membatalkan sebuah transaksi riwayat.
    */
    public function cancelHistory(ProductHistory $productHistory)
    {
        abort_if(!auth()->user()->hasPermission('transactions.edit_history'), 403);
        $productHistory->delete();
        return back()->with('success', __('Riwayat dibatalkan.'));
    }

    public function quickReturn(ProductHistory $productHistory)
    {
        abort_if(!auth()->user()->hasPermission('transactions.edit_history'), 403);
        // Validasi: pastikan record ini adalah peminjaman terakhir yang aktif
        $latest = ProductHistory::where('product_id', $productHistory->product_id)->latest()->first();
        if ($latest->id !== $productHistory->id || $latest->jenis_transaksi !== 'meminjam') {
            return response()->json(['success' => false, 'message' => __('Aset ini sudah tidak aktif dipinjam atau sudah dikembalikan.')]);
        }

        ProductHistory::create([
            'product_id'       => $productHistory->product_id,
            'dioper_oleh'      => auth()->id(),
            'diterima_oleh'    => null,
            'jenis_transaksi'  => 'mengembalikan',
            'pihak_pertama_id' => $productHistory->diterima_oleh,
        ]);

        return response()->json(['success' => true, 'message' => __('Aset berhasil dikembalikan ke kantor.')]);
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman untuk meminjamkan aset.
    */
    public function pinjam(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('transactions.transfer'), 403);
        return $this->handleTransfer($request, 'meminjam');
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman untuk mengembalikan aset.
    */
    public function kembali(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('transactions.transfer'), 403);
        return $this->handleTransfer($request, 'kembali');
    }

    /* | [FUNGSI INTERNAL] | 
       | Kegunaan: Mengolah pencarian aset untuk dipinjam atau dikembalikan.
    */
    private function handleTransfer(Request $request, $type)
    {
        $query = $request->get('q', '');

        // Selalu tampilkan semua aset, filter jika ada query pencarian
        $asetQuery = Aset::with(['department', 'classification', 'histories' => function($q) {
            $q->latest()->limit(1);
        }, 'histories.receiver.office']);

        if ($query) {
            $asetQuery->where(function($q) use ($query) {
                $q->where('description', 'like', "%{$query}%")
                  ->orWhere('nomor_unik', 'like', "%{$query}%")
                  ->orWhereHas('department', function($dq) use ($query) {
                      $dq->where('kode_asset', 'like', "%{$query}%")
                         ->orWhere('nama_departemen', 'like', "%{$query}%");
                  });
            });
        }

        $results = $asetQuery->orderBy('department_id')->orderByRaw('CAST(nomor_unik AS UNSIGNED)')->get();

        // Untuk mode 'kembali': hanya tampilkan yang sedang dipinjam
        if ($type === 'kembali') {
            $results = $results->filter(function($p) {
                $last = $p->histories->first();
                return $last && $last->diterima_oleh;
            });
        }

        $users = User::with('office')->get();

        $holderMap = [];
        foreach ($results as $p) {
            $last = $p->histories->first();
            $holderMap[$p->id] = $last ? [
                'id'     => $last->diterima_oleh,
                'name'   => $last->receiver?->name ?? '-',
                'office' => $last->receiver?->office?->nama_kantor ?? '-',
                'date'   => $last->created_at->format('d M Y'),
            ] : null;
        }

        // Artha (HR/IT) selalu menjadi Pihak Pertama dalam dokumen STTB (Loan & Return)
        $artha = User::getPihakPertama()->first();

        return view('products.transfer', compact('query', 'results', 'users', 'holderMap', 'type', 'artha'));
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Mencatat transaksi peminjaman atau pengembalian aset ke database.
       | Untuk mode 'meminjam': mendukung banyak barang sekaligus (batch) dan menghasilkan STTB.
    */
    public function storeTransfer(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('transactions.transfer'), 403);
        $request->validate([
            'jenis_transaksi' => 'required|in:meminjam,kembali',
        ]);

        // ── MODE MEMINJAM & KEMBALI (Batch STTB) ───────────────────────────────
        $request->validate([
            'product_ids'      => 'required|array|min:1',
            'product_ids.*'    => 'exists:products,id',
            'quantities'       => 'required|array',
            'quantities.*'     => 'integer|min:1',
            'pihak_pertama_id' => 'required|exists:users,id',
            'diterima_oleh'    => $request->jenis_transaksi === 'meminjam' ? 'required|exists:users,id' : 'nullable|exists:users,id',
        ]);

        // Generate satu batch_id unik untuk semua barang dalam STTB ini
        $batchId = (string) \Illuminate\Support\Str::uuid();
        $tanggal = $request->tanggal_pinjam ?? now()->toDateString();
        $type    = $request->jenis_transaksi === 'meminjam' ? 'meminjam' : 'mengembalikan';

        foreach ($request->product_ids as $index => $productId) {
            ProductHistory::create([
                'product_id'       => $productId,
                'dioper_oleh'      => auth()->id(),
                'diterima_oleh'    => $type === 'mengembalikan' ? null : $request->diterima_oleh,
                'jenis_transaksi'  => $type,
                'batch_id'         => $batchId,
                'quantity'         => $request->quantities[$index] ?? 1,
                'pihak_pertama_id' => $type === 'mengembalikan' ? $request->diterima_oleh : $request->pihak_pertama_id,
                'tanggal_pinjam'   => $tanggal,
            ]);
        }

        // Redirect ke halaman STTB yang siap cetak
        $msg = $type === 'meminjam' ? __('Peminjaman berhasil! STTB sudah siap dicetak.') : __('Pengembalian berhasil! STTB sudah siap dicetak.');
        return redirect()->route('sttb.show', $batchId)->with('success', $msg);
    }

    /* | [PROSEDUR] | 
       | Kegunaan: Menampilkan halaman STTB berdasarkan batch_id.
    */
    public function showSttb($batchId)
    {
        abort_if(!auth()->user()->hasPermission('transactions.transfer'), 403);
        // Ambil semua riwayat dalam satu batch, beserta relasi yang dibutuhkan
        $histories = ProductHistory::with(['product.department', 'receiver', 'pihakPertama'])
            ->where('batch_id', $batchId)
            ->get();

        if ($histories->isEmpty()) {
            abort(404, 'STTB tidak ditemukan.');
        }

        // Data STTB diambil dari record pertama (semua punya batch yang sama)
        $first        = $histories->first();
        $jenis        = $first->jenis_transaksi; // 'meminjam' atau 'mengembalikan'

        // Logika Tukar Posisi: Pihak I = Yang Menyerahkan, Pihak II = Yang Menerima
        if ($jenis === 'mengembalikan') {
            // Saat pengembalian: Karyawan (pihakPertama) adalah penyerah (I), Admin (sender) adalah penerima (II)
            $pihakPertama = $first->pihakPertama;
            $pihakKedua   = $first->sender;
        } else {
            // Saat peminjaman: Admin (pihakPertama) adalah penyerah (I), Karyawan (receiver) adalah penerima (II)
            $pihakPertama = $first->pihakPertama;
            $pihakKedua   = $first->receiver;
        }

        $tanggal      = $first->tanggal_pinjam ?? $first->created_at->toDateString();

        return view('products.sttb', compact('histories', 'pihakPertama', 'pihakKedua', 'tanggal', 'batchId', 'jenis'));
    }
}
