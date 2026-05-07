<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Detail Permintaan Aset') }}</h2>
            <div class="text-right">
                <span class="text-xs text-gray-400 block uppercase font-bold">{{ __('Status Saat Ini') }}</span>
                @php
                    $statusLabel = [
                        'pending_hr' => ['text' => __('Menunggu HR'), 'class' => 'text-gray-600'],
                        'pending_dept' => ['text' => __('Menunggu Manager Dept'), 'class' => 'text-yellow-700'],
                        'pending_it' => ['text' => __('Menunggu Manager IT'), 'class' => 'text-blue-700'],
                        'pending_md' => ['text' => __('Menunggu Managing Director'), 'class' => 'text-purple-700'],
                        'approved' => ['text' => __('Selesai / Approved'), 'class' => 'text-green-700'],
                        'rejected' => ['text' => __('Ditolak'), 'class' => 'text-red-700'],
                    ];
                    $s = $statusLabel[$assetRequest->status] ?? ['text' => $assetRequest->status, 'class' => 'text-gray-600'];
                @endphp
                <span class="font-bold {{ $s['class'] }}">{{ $s['text'] }}</span>
            </div>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap');
        
        .signature-box {
            position: relative;
            padding: 10px;
            border-left: 2px solid #10b981;
            border-bottom: 2px solid #10b981;
            border-bottom-left-radius: 8px;
            background: rgba(16, 185, 129, 0.03);
            min-height: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .signature-font {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            color: #1e293b;
            line-height: 1;
        }
        .signature-img {
            max-height: 60px;
            object-fit: contain;
            mix-blend-mode: multiply;
        }
        .signed-badge {
            position: absolute;
            top: -8px;
            left: 10px;
            background: white;
            padding: 0 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>

    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: INFO UTAMA --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Data Pengajuan --}}
                <div class="bg-white rounded-xl border border-[#e5e0d8] overflow-hidden shadow-sm">
                    <div class="bg-[#f4f1ea] px-6 py-3 border-b border-[#e5e0d8] flex justify-between items-center">
                        <h3 class="font-serif font-bold text-[#4a554a]">{{ __('Informasi Pengajuan') }}</h3>
                        <span class="text-xs font-bold px-2 py-1 bg-white rounded border border-[#e5e0d8] text-[#a47b53]">
                            {{ __($assetRequest->criticality_level) }}
                        </span>
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs uppercase font-bold mb-1">{{ __('Nama Pemohon') }}</p>
                            <p class="font-bold text-[#4a554a]">{{ $assetRequest->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase font-bold mb-1">{{ __('Departemen') }}</p>
                            <p class="font-bold text-[#4a554a]">{{ $assetRequest->department->nama_departemen }}</p>
                        </div>
                        <div class="col-span-2 mt-2">
                            <p class="text-gray-400 text-xs uppercase font-bold mb-1">{{ __('Alasan Peminjaman') }}</p>
                            <p class="bg-[#faf9f6] p-3 rounded-lg italic text-gray-600 border border-[#f0eee9]">"{{ $assetRequest->reason }}"</p>
                        </div>
                        @if($assetRequest->hrManager)
                        <div class="col-span-2 mt-2">
                            <p class="text-gray-400 text-xs uppercase font-bold mb-1">{{ __('Requested By (HR Sponsor)') }}</p>
                            <p class="font-bold text-[#4a554a]">{{ $assetRequest->hrManager->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Tabel Barang --}}
                <div class="bg-white rounded-xl border border-[#e5e0d8] overflow-hidden shadow-sm">
                    <div class="bg-[#f4f1ea] px-6 py-3 border-b border-[#e5e0d8]">
                        <h3 class="font-serif font-bold text-[#4a554a]">{{ __('Daftar Barang') }}</h3>
                    </div>
                    <form id="approval-form" action="{{ route('asset-requests.approve', $assetRequest) }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-[#faf9f6] text-[#4a554a] uppercase text-[10px] font-bold">
                                    <tr>
                                        <th class="p-4 border-b border-[#f0eee9]">{{ __('Nama Barang') }}</th>
                                        <th class="p-4 border-b border-[#f0eee9]">{{ __('Spek Tambahan') }}</th>
                                        <th class="p-4 border-b border-[#f0eee9] text-center">{{ __('Qty') }}</th>
                                        <th class="p-4 border-b border-[#f0eee9] text-center">HR</th>
                                        <th class="p-4 border-b border-[#f0eee9] text-center">{{ __('Dept') }}</th>
                                        <th class="p-4 border-b border-[#f0eee9] text-center">IT</th>
                                        <th class="p-4 border-b border-[#f0eee9] text-center">MD</th>
                                        @if($assetRequest->status != 'approved' && $assetRequest->status != 'rejected' && $assetRequest->status != 'pending_fulfillment')
                                            <th class="p-4 border-b border-[#f0eee9] text-center bg-[#fdf2e9]">{{ __('Approve?') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assetRequest->items as $item)
                                        <tr class="border-b border-[#f0eee9]">
                                            <td class="p-4 font-bold">{{ $item->classification->nama_klasifikasi }}</td>
                                            <td class="p-4 text-xs text-gray-500">{{ $item->specs ?? '-' }}</td>
                                            <td class="p-4 text-center">{{ $item->qty }}</td>
                                             {{-- HR: tampil setelah HR approve (hr_manager_id terisi) --}}
                                            <td class="p-4 text-center">
                                                @if(!$assetRequest->hr_manager_id)
                                                    <span class="text-gray-200">—</span>
                                                @else
                                                    <div class="flex flex-col items-center gap-1">
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ $item->hr_approval ? 'bg-green-600 text-white border-green-600' : 'bg-white' }}">
                                                                {!! $item->hr_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('YES') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ !$item->hr_approval ? 'bg-red-600 text-white border-red-600' : 'bg-white' }}">
                                                                {!! !$item->hr_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('NO') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- DEPT: tampil setelah status melewati pending_dept --}}
                                            <td class="p-4 text-center">
                                                @if(in_array($assetRequest->status, ['pending_hr', 'pending_dept']))
                                                    <span class="text-gray-200">—</span>
                                                @else
                                                    <div class="flex flex-col items-center gap-1">
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ $item->dept_approval ? 'bg-green-600 text-white border-green-600' : 'bg-white' }}">
                                                                {!! $item->dept_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('YES') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ !$item->dept_approval ? 'bg-red-600 text-white border-red-600' : 'bg-white' }}">
                                                                {!! !$item->dept_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('NO') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- IT: tampil setelah status melewati pending_it --}}
                                            <td class="p-4 text-center">
                                                @if(in_array($assetRequest->status, ['pending_hr', 'pending_dept', 'pending_it']))
                                                    <span class="text-gray-200">—</span>
                                                @else
                                                    <div class="flex flex-col items-center gap-1">
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ $item->it_approval ? 'bg-green-600 text-white border-green-600' : 'bg-white' }}">
                                                                {!! $item->it_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('YES') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ !$item->it_approval ? 'bg-red-600 text-white border-red-600' : 'bg-white' }}">
                                                                {!! !$item->it_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('NO') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- MD: tampil setelah status melewati pending_md --}}
                                            <td class="p-4 text-center">
                                                @if(in_array($assetRequest->status, ['pending_hr', 'pending_dept', 'pending_it', 'pending_md']))
                                                    <span class="text-gray-200">—</span>
                                                @else
                                                    <div class="flex flex-col items-center gap-1">
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ $item->md_approval ? 'bg-green-600 text-white border-green-600' : 'bg-white' }}">
                                                                {!! $item->md_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('YES') }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-3 h-3 border border-gray-400 flex items-center justify-center text-[8px] {{ !$item->md_approval ? 'bg-red-600 text-white border-red-600' : 'bg-white' }}">
                                                                {!! !$item->md_approval ? '✓' : '' !!}
                                                            </div>
                                                            <span class="text-[8px] font-bold">{{ __('NO') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            
                                            {{-- Kolom Input Yes/No (Muncul saat tahap persetujuan manager) --}}
                                            @if($assetRequest->status != 'approved' && $assetRequest->status != 'rejected' && $assetRequest->status != 'pending_fulfillment')
                                                @php
                                                    $canApproveThisStage = false;
                                                    $user = auth()->user();
                                                    
                                                    if ($assetRequest->status == 'pending_hr' && $user->hasPermission('requests.approve_stage_1')) $canApproveThisStage = true;

                                                    if ($assetRequest->status == 'pending_dept' && $user->hasPermission('requests.approve_stage_2')) {
                                                        $allowedDeptId = $user->role_relation?->dept_approval_for ?? $user->department_id;
                                                        if ($allowedDeptId == $assetRequest->department_id) $canApproveThisStage = true;
                                                    }
                                                    
                                                    if ($assetRequest->status == 'pending_it' && $user->hasPermission('requests.approve_stage_3')) $canApproveThisStage = true;
                                                    if ($assetRequest->status == 'pending_md' && $user->hasPermission('requests.approve_stage_4')) $canApproveThisStage = true;
                                                @endphp
                                                <td class="p-4 text-center bg-[#fffaf5]">
                                                    @if($canApproveThisStage)
                                                        <div class="flex flex-col items-center gap-1">
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="item_approvals[{{ $item->id }}]" value="yes" checked class="text-green-600 focus:ring-green-500 w-3 h-3">
                                                                <span class="ml-1 text-[10px] font-bold text-green-700">{{ __('YES') }}</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="item_approvals[{{ $item->id }}]" value="no" class="text-red-600 focus:ring-red-500 w-3 h-3">
                                                                <span class="ml-1 text-[10px] font-bold text-red-700">{{ __('NO') }}</span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

                {{-- KOLOM ADMIN FULFILLMENT --}}
                @if($assetRequest->status == 'pending_fulfillment' && auth()->user()->hasRoleLevel('superadmin'))
                    <div class="bg-white rounded-xl border-2 border-green-600 p-6 shadow-lg mt-6">
                        <h4 class="font-serif font-bold text-[#4a554a] mb-4 flex items-center">
                            <span class="mr-2">🔧</span> {{ __('Finalisasi IT Fulfillment') }}
                        </h4>
                        <p class="text-xs text-gray-500 mb-4">{{ __('Silakan masukkan detail perangkat yang akan diberikan (Model, S/N, atau catatan IT lainnya).') }}</p>
                        
                        <form action="{{ route('asset-requests.finalize', $assetRequest) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('Catatan IT / Jenis Laptop') }}</label>
                                <textarea name="admin_notes" rows="3" class="w-full text-sm rounded-lg border-[#e5e0d8] focus:border-green-500" placeholder="{{ __('Contoh: Diberikan Laptop Dell Latitude 5420 S/N: XXXX') }}"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition shadow-md">
                                {{ __('Selesaikan & Siap Cetak') }}
                            </button>
                        </form>
                    </div>
                @endif

                {{-- INFO FULFILLMENT (SETELAH SELESAI) --}}
                @if($assetRequest->admin_notes)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mt-6">
                        <h4 class="text-xs font-bold text-green-800 uppercase mb-2">{{ __('Catatan IT Fulfillment') }}</h4>
                        <p class="text-sm text-green-700">{{ $assetRequest->admin_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- KOLOM KANAN: RIWAYAT & AKSI --}}
            <div class="space-y-6">
                
                {{-- Form Aksi Persetujuan --}}
                @php
                    $user = auth()->user();
                    $showActionForm = false;
                    
                    if ($assetRequest->status == 'pending_hr' && $user->hasPermission('requests.approve_stage_1')) $showActionForm = true;
                    
                    if ($assetRequest->status == 'pending_dept' && $user->hasPermission('requests.approve_stage_2')) {
                        $allowedDeptId = $user->role_relation?->dept_approval_for ?? $user->department_id;
                        if ($allowedDeptId == $assetRequest->department_id) $showActionForm = true;
                    }
                    
                    if ($assetRequest->status == 'pending_it' && $user->hasPermission('requests.approve_stage_3')) $showActionForm = true;
                    if ($assetRequest->status == 'pending_md' && $user->hasPermission('requests.approve_stage_4')) $showActionForm = true;

                    // Daftar komentar yang terlihat berdasarkan tahap
                    $visibleComments = [];
                    $stagesOrder = ['pending_hr' => 1, 'pending_dept' => 2, 'pending_it' => 3, 'pending_md' => 4, 'pending_fulfillment' => 5, 'approved' => 6, 'rejected' => 6];
                    $currentStage = $stagesOrder[$assetRequest->status] ?? 0;

                    if ($currentStage >= 2) $visibleComments[] = ['role' => __('HR Manager'), 'comment' => $assetRequest->hr_comment];
                    if ($currentStage >= 3) $visibleComments[] = ['role' => __('Manager Dept'), 'comment' => $assetRequest->dept_comment];
                    if ($currentStage >= 4) $visibleComments[] = ['role' => __('Manager IT'), 'comment' => $assetRequest->it_comment];
                    if ($currentStage >= 5) $visibleComments[] = ['role' => __('Managing Director'), 'comment' => $assetRequest->md_comment];
                @endphp

                @if($showActionForm)
                    <div class="bg-white rounded-xl border-2 border-[#a47b53] p-6 shadow-lg">
                        <h4 class="font-serif font-bold text-[#4a554a] mb-4 flex items-center">
                            <span class="mr-2">✍️</span> {{ __('Tindakan Persetujuan') }}
                        </h4>

                        {{-- Tampilkan Komentar Sebelumnya --}}
                        @if(!empty($visibleComments))
                            <div class="mb-4 space-y-2 border border-[#f0eee9] rounded-lg p-3 bg-[#faf9f6]">
                                <p class="text-[9px] font-bold text-gray-400 uppercase mb-2">{{ __('Komentar Tahap Sebelumnya') }}</p>
                                @foreach($visibleComments as $vc)
                                    @if($vc['comment'])
                                        <div class="text-xs">
                                            <span class="font-bold text-[#4a554a]">{{ $vc['role'] }}:</span>
                                            <span class="text-gray-600 italic"> "{{ $vc['comment'] }}"</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <p class="text-xs text-gray-500 mb-4">{{ __('Silakan berikan tanggapan Anda untuk tahap ini.') }}</p>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ __('Komentar / Catatan') }}</label>
                            <textarea form="approval-form" name="comment" rows="3" class="w-full text-sm rounded-lg border-[#e5e0d8] focus:border-[#a47b53]" placeholder="{{ __('Tambahkan alasan atau catatan khusus...') }}"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-2">
                            <button type="submit" form="approval-form" class="w-full bg-[#4a554a] hover:bg-[#3a443a] text-white font-bold py-2 rounded-lg transition shadow-md">
                                {{ __('Approve Tahap Ini') }}
                            </button>
                            <button type="button" onclick="confirmReject()" class="w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 font-bold py-2 rounded-lg transition">
                                {{ __('Tolak Permintaan') }}
                            </button>
                        </div>
                        <form id="reject-form" action="{{ route('asset-requests.reject', $assetRequest) }}" method="POST" class="hidden">@csrf <input type="hidden" name="comment" id="reject-comment"></form>
                    </div>
                @endif

                {{-- Tombol Cetak EWTR --}}
                @if($assetRequest->status == 'approved')
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <h4 class="text-xs font-bold text-green-800 uppercase mb-3">{{ __('Dokumen Siap') }}</h4>
                        <a href="{{ route('asset-requests.print', $assetRequest) }}" target="_blank"
                           class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition shadow-md text-sm">
                            🖨️ {{ __('Cetak EWTR') }}
                        </a>
                    </div>
                @endif

                {{-- Timeline Persetujuan (Tanda Tangan) --}}
                <div class="bg-white rounded-xl border border-[#e5e0d8] p-6 shadow-sm">
                    <h4 class="font-serif font-bold text-[#4a554a] mb-6 flex justify-between items-center">
                        {{ __('Persetujuan Digital') }}
                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-sans">E-SIGN READY</span>
                    </h4>
                    
                    <div class="space-y-8">
                        @php
                            $execApproval = $assetRequest->approvals->where('level', 'executor')->first();
                            $stages = [
                                ['label' => __('Pemohon'), 'user' => $assetRequest->user, 'approved' => true, 'comment' => $assetRequest->reason, 'date' => $assetRequest->created_at],
                                ['label' => __('HR Manager'), 'user' => $assetRequest->hrManager, 'approved' => !!$assetRequest->hr_manager_id, 'comment' => $assetRequest->hr_comment, 'date' => $assetRequest->approvals->where('level', 'hr')->first()?->created_at],
                                ['label' => __('Manager Dept'), 'user' => $assetRequest->approvals->where('level', 'dept')->first()?->user, 'approved' => !in_array($assetRequest->status, ['pending_hr', 'pending_dept']), 'comment' => $assetRequest->dept_comment, 'date' => $assetRequest->approvals->where('level', 'dept')->first()?->created_at],
                                ['label' => __('Manager IT'), 'user' => $assetRequest->approvals->where('level', 'it')->first()?->user, 'approved' => in_array($assetRequest->status, ['pending_md', 'pending_fulfillment', 'approved']), 'comment' => $assetRequest->it_comment, 'date' => $assetRequest->approvals->where('level', 'it')->first()?->created_at],
                                ['label' => __('Managing Director'), 'user' => $assetRequest->approvals->where('level', 'md')->first()?->user, 'approved' => in_array($assetRequest->status, ['pending_fulfillment', 'approved']), 'comment' => $assetRequest->md_comment, 'date' => $assetRequest->approvals->where('level', 'md')->first()?->created_at],
                                ['label' => __('IT Fulfillment (Admin)'), 'user' => $execApproval?->user, 'approved' => $assetRequest->status == 'approved', 'comment' => $assetRequest->admin_notes, 'date' => $execApproval?->created_at],
                            ];
                        @endphp

                        @foreach($stages as $stage)
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-3">{{ $stage['label'] }}</p>
                                @if($stage['approved'] && $stage['user'])
                                    <div class="signature-box">
                                        <div class="signed-badge">
                                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            <span class="text-[9px] font-bold text-green-600 uppercase">{{ __('Signed') }}</span>
                                        </div>
                                        
                                        @if($stage['user']->signature_path)
                                            <img src="{{ asset('storage/' . $stage['user']->signature_path) }}" class="signature-img" alt="Signature">
                                        @else
                                            <span class="signature-font">{{ $stage['user']->name }}</span>
                                        @endif

                                        <div class="mt-2 text-center">
                                            <p class="text-[10px] font-bold text-[#4a554a]">{{ $stage['user']->name }}</p>
                                            <p class="text-[8px] text-gray-400">{{ $stage['date'] ? $stage['date']->format('d/m/Y H:i') : '' }}</p>
                                        </div>
                                    </div>
                                    @if($stage['comment'])
                                        <p class="mt-2 text-xs text-gray-500 italic px-2">"{{ $stage['comment'] }}"</p>
                                    @endif
                                @else
                                    <div class="border-2 border-dashed border-gray-100 rounded-lg p-6 flex flex-col items-center justify-center bg-gray-50/50">
                                        <span class="text-[10px] font-bold text-gray-300 uppercase">{{ __('Menunggu Persetujuan') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function confirmReject() {
            const comment = document.querySelector('textarea[name="comment"]').value;
            if (!comment) {
                alert("{{ __('Silakan isi alasan penolakan pada kolom komentar terlebih dahulu.') }}");
                return;
            }
            if (confirm("{{ __('Apakah Anda yakin ingin menolak permintaan ini?') }}")) {
                document.getElementById('reject-comment').value = comment;
                document.getElementById('reject-form').submit();
            }
        }
    </script>
</x-app-layout>
