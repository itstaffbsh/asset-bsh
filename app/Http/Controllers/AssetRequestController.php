<?php

namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AssetRequestItem;
use App\Models\AssetRequestApproval;
use App\Models\Classification;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetRequestController extends Controller
{
    /* | [LOGIKA] | Menampilkan daftar permintaan berdasarkan hak akses */
    public function index()
    {
        $user = auth()->user();
        $query = AssetRequest::with(['user', 'department', 'hrManager']);

        // Filter berdasarkan Role & Permissions
        if ($user->hasPermission('requests.approve_stage_4')) {
            // Managing Director / Superadmin bisa lihat semua
            // (Tidak ada filter tambahan)
        } else {
            $query->where(function($q) use ($user) {
                // User selalu bisa melihat request miliknya sendiri
                $q->where('user_id', $user->id);

                // Stage 1 (HR): Lihat yang pending HR
                if ($user->hasPermission('requests.approve_stage_1')) {
                    $q->orWhere('status', 'pending_hr');
                }

                // Stage 2 (Dept Manager): Lihat yang pending Dept dan departemennya cocok
                if ($user->hasPermission('requests.approve_stage_2')) {
                    $deptId = $user->role_relation?->dept_approval_for ?? $user->department_id;
                    $q->orWhere(function($subQ) use ($deptId) {
                        $subQ->where('status', 'pending_dept')
                             ->where('department_id', $deptId);
                    });
                }

                // Stage 3 (IT): Lihat yang pending IT
                if ($user->hasPermission('requests.approve_stage_3')) {
                    $q->orWhere('status', 'pending_it');
                }
            });
        }

        $requests = $query->latest()->paginate(10);
        return view('asset_requests.index', compact('requests'));
    }

    /* | [LOGIKA] | Halaman buat permintaan baru */
    public function create()
    {
        $classifications = Classification::all();
        return view('asset_requests.create', compact('classifications'));
    }

    /* | [LOGIKA] | Menyimpan permintaan baru (oleh User/Karyawan) */
    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required',
            'criticality_level' => 'required|in:top priority,urgent,normal',
            'items' => 'required|array|min:1',
            'items.*.classification_id' => 'required|exists:classifications,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $assetRequest = AssetRequest::create([
                'user_id' => auth()->id(),
                'department_id' => auth()->user()->department_id,
                'reason' => $request->reason,
                'criticality_level' => $request->criticality_level,
                'status' => 'pending_hr',
            ]);

            foreach ($request->items as $item) {
                AssetRequestItem::create([
                    'asset_request_id' => $assetRequest->id,
                    'classification_id' => $item['classification_id'],
                    'specs' => $item['specs'] ?? null,
                    'qty' => $item['qty'],
                ]);
            }
        });

        return redirect()->route('asset-requests.index')->with('success', __('Permintaan aset berhasil diajukan.'));
    }

    /* | [LOGIKA] | Detail permintaan & form persetujuan */
    public function show(AssetRequest $assetRequest)
    {
        $assetRequest->load(['user', 'department', 'hrManager', 'items.classification', 'items.product.department', 'approvals.user']);
        
        // Ambil produk yang tersedia untuk tiap klasifikasi yang diminta
        $availableProducts = [];
        foreach ($assetRequest->items as $item) {
            $availableProducts[$item->classification_id] = \App\Models\Product::with('department')
                ->where('classification_id', $item->classification_id)
                ->whereNull('deleted_at')
                ->get();
        }

        return view('asset_requests.show', compact('assetRequest', 'availableProducts'));
    }

    /* | [LOGIKA] | Tampilan cetak EWTR */
    public function print(AssetRequest $assetRequest)
    {
        if ($assetRequest->status !== 'approved') {
            return back()->with('error', __('EWTR hanya bisa dicetak setelah semua tahap persetujuan selesai.'));
        }
        $assetRequest->load(['user', 'department', 'hrManager', 'items.classification', 'items.product.department', 'approvals.user']);
        return view('asset_requests.print', compact('assetRequest'));
    }

    /* | [LOGIKA] | Proses Persetujuan Berjenjang */
    public function approve(Request $request, AssetRequest $assetRequest)
    {
        $user = auth()->user();
        $status = $assetRequest->status;
        $itDept = \App\Models\Department::where('kode_asset', 'IT')->first();
        $hrDept = \App\Models\Department::where('kode_asset', 'HR')->first();
        $isItDept = ($assetRequest->department_id == ($itDept->id ?? 0));
        $isHrDept = ($assetRequest->department_id == ($hrDept->id ?? 0));

        $deptCode = $user->department?->kode_asset;

        // 1. TAHAP HR (Stage 1)
        if ($status == 'pending_hr' && $user->hasPermission('requests.approve_stage_1')) {
            $assetRequest->hr_manager_id = $user->id;
            $assetRequest->hr_comment = $request->comment;
            $this->updateItemApprovals($request, $assetRequest, 'hr');

            if ($isItDept || $isHrDept) {
                // Jika pemohon orang IT atau HR, bypass Dept stage
                $assetRequest->status = 'pending_it';
            } else {
                $assetRequest->status = 'pending_dept';
            }
        }
        // 2. TAHAP DEPT (Stage 2)
        elseif ($status == 'pending_dept' && $user->hasPermission('requests.approve_stage_2')) {
            $allowedDeptId = $user->role_relation?->dept_approval_for ?? $user->department_id;
            if ($allowedDeptId == $assetRequest->department_id) {
                $assetRequest->dept_comment = $request->comment;
                $assetRequest->status = 'pending_it';
                $this->updateItemApprovals($request, $assetRequest, 'dept');
            } else {
                return back()->with('error', __('Anda tidak berwenang menyetujui permintaan departemen ini.'));
            }
        }
        // 3. TAHAP IT (Stage 3)
        elseif ($status == 'pending_it' && $user->hasPermission('requests.approve_stage_3')) {
            $assetRequest->it_comment = $request->comment;
            $assetRequest->status = 'pending_md';
            $this->updateItemApprovals($request, $assetRequest, 'it');
        }
        // 4. TAHAP MD (Stage 4)
        elseif ($status == 'pending_md' && $user->hasPermission('requests.approve_stage_4')) {
            $assetRequest->md_comment = $request->comment;
            $assetRequest->status = 'pending_fulfillment';
            $this->updateItemApprovals($request, $assetRequest, 'md');
        } else {
            return back()->with('error', __('Anda tidak memiliki otoritas untuk menyetujui tahap ini.'));
        }

        $assetRequest->save();

        // Catat di tabel history approvals
        AssetRequestApproval::create([
            'asset_request_id' => $assetRequest->id,
            'user_id' => $user->id,
            'level' => $this->getLevelFromStatus($status),
            'status' => 'approved',
            'comment' => $request->comment,
        ]);

        return back()->with('success', __('Persetujuan berhasil diproses.'));
    }

    /* | [LOGIKA] | Finalisasi oleh Admin (Fulfillment) */
    public function finalize(Request $request, AssetRequest $assetRequest)
    {
        $user = auth()->user();
        if (!$user->hasPermission('requests.finalize')) {
            return back()->with('error', __('Hanya user dengan izin khusus yang dapat melakukan finalisasi.'));
        }

        $request->validate([
            'admin_notes' => 'nullable',
            'products' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $assetRequest, $user) {
            $assetRequest->admin_notes = $request->admin_notes;
            $assetRequest->status = 'approved';
            $assetRequest->save();

            // Simpan produk yang dipilih untuk tiap item
            if ($request->has('products')) {
                foreach ($request->products as $itemId => $productId) {
                    if ($productId) {
                        $item = AssetRequestItem::find($itemId);
                        if ($item && $item->asset_request_id == $assetRequest->id) {
                            $item->product_id = $productId;
                            $item->save();
                        }
                    }
                }
            }

            AssetRequestApproval::create([
                'asset_request_id' => $assetRequest->id,
                'user_id' => $user->id,
                'level' => 'executor',
                'status' => 'approved',
                'comment' => $request->admin_notes,
            ]);
        });

        return back()->with('success', __('Permintaan telah difinalisasi dan siap dicetak.'));
    }

    public function reject(Request $request, AssetRequest $assetRequest)
    {
        $currentStatus = $assetRequest->status;
        $level = $this->getLevelFromStatus($currentStatus);

        $assetRequest->status = 'rejected';
        $assetRequest->save();

        // Tandai semua item sebagai 'no' untuk tahap ini
        foreach ($assetRequest->items as $item) {
            $column = $level . '_approval';
            if (\Illuminate\Support\Facades\Schema::hasColumn('asset_request_items', $column)) {
                $item->$column = false;
                $item->save();
            }
        }

        AssetRequestApproval::create([
            'asset_request_id' => $assetRequest->id,
            'user_id' => auth()->id(),
            'level' => $level,
            'status' => 'rejected',
            'comment' => $request->comment,
        ]);

        return back()->with('success', __('Permintaan telah ditolak.'));
    }

    private function updateItemApprovals($request, $assetRequest, $stage)
    {
        if ($request->has('item_approvals')) {
            foreach ($request->item_approvals as $itemId => $value) {
                $item = AssetRequestItem::find($itemId);
                if ($item && $item->asset_request_id == $assetRequest->id) {
                    $column = $stage . '_approval';
                    $item->$column = ($value == 'yes');
                    $item->save();
                }
            }
        }
    }

    private function getLevelFromStatus($status)
    {
        $map = [
            'pending_hr' => 'hr',
            'pending_dept' => 'dept',
            'pending_it' => 'it',
            'pending_md' => 'md',
        ];
        return $map[$status] ?? 'executor';
    }
}
