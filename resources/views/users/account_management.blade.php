<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Management Account') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('accounts.create') }}" class="bg-[#5c6b5b] text-[#f4f1ea] px-4 py-2 rounded-md shadow hover:bg-[#4a554a] transition font-bold text-sm">+ {{ __('TAMBAH AKUN') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white rounded-xl shadow-sm border border-[#e5e0d8] overflow-hidden">
            <div class="p-8">
                @if(session('success'))
                    <div class="bg-[#d4ebd0] text-[#3a5a3a] p-4 rounded-md mb-6 border border-[#b8deb2] font-bold">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6 border border-red-200">{{ session('error') }}</div>
                @endif

                @if(!\Illuminate\Support\Facades\Schema::hasColumn('users', 'status'))
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0 text-yellow-400">⚠️</div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    {{ __('Fitur Resign belum aktif sepenuhnya. Silakan jalankan update database dengan klik tombol ini:') }}
                                    <a href="{{ url('/tambah-status-user') }}" class="ml-2 bg-yellow-600 text-white px-3 py-1 rounded-md text-xs font-bold hover:bg-yellow-700">{{ __('AKTIFKAN FITUR RESIGN') }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-6">
                    <form action="{{ route('accounts.index') }}" method="GET">
                        <div class="flex flex-wrap gap-3 items-end">
                            {{-- Search --}}
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-bold text-[#5c6b5b] uppercase tracking-widest mb-1">{{ __('Cari Karyawan') }}</label>
                                <div class="relative">
                                    <input type="text" name="q" value="{{ $searchTerm ?? '' }}"
                                           class="w-full pl-8 pr-4 py-2 border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53] focus:ring focus:ring-[#a47b53] focus:ring-opacity-20 text-sm"
                                           placeholder="{{ __('Nama, Email, ID, Jabatan...') }}">
                                    <span class="absolute left-2.5 top-2.5 text-gray-400 text-xs">🔍</span>
                                </div>
                            </div>
                            {{-- Filter Departemen --}}
                            <div class="min-w-[160px]">
                                <label class="block text-xs font-bold text-[#5c6b5b] uppercase tracking-widest mb-1">{{ __('Departemen') }}</label>
                                <select name="department_id" onchange="this.form.submit()" class="w-full border-[#d1cdba] rounded-lg py-2 text-sm shadow-sm focus:border-[#a47b53]">
                                    <option value="">{{ __('Semua Dept.') }}</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" {{ ($filterDept ?? '') == $d->id ? 'selected' : '' }}>{{ $d->nama_departemen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Filter Kantor --}}
                            <div class="min-w-[160px]">
                                <label class="block text-xs font-bold text-[#5c6b5b] uppercase tracking-widest mb-1">{{ __('Kantor') }}</label>
                                <select name="office_id" onchange="this.form.submit()" class="w-full border-[#d1cdba] rounded-lg py-2 text-sm shadow-sm focus:border-[#a47b53]">
                                    <option value="">{{ __('Semua Kantor') }}</option>
                                    @foreach($offices as $o)
                                        <option value="{{ $o->id }}" {{ ($filterKantor ?? '') == $o->id ? 'selected' : '' }}>{{ $o->nama_kantor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Filter Status --}}
                            <div class="min-w-[160px]">
                                <label class="block text-xs font-bold text-[#5c6b5b] uppercase tracking-widest mb-1">{{ __('Status') }}</label>
                                <select name="status" onchange="this.form.submit()" class="w-full border-[#d1cdba] rounded-lg py-2 text-sm shadow-sm focus:border-[#a47b53]">
                                    <option value="active" {{ ($filterStatus ?? '') == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                    <option value="resigned" {{ ($filterStatus ?? '') == 'resigned' ? 'selected' : '' }}>{{ __('Resign') }}</option>
                                    <option value="" {{ ($filterStatus ?? '') == '' ? 'selected' : '' }}>{{ __('Semua') }}</option>
                                </select>
                            </div>
                            {{-- Tombol --}}
                            <div class="flex gap-2">
                                <button type="submit" class="bg-[#5c6b5b] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#4a554a] transition">{{ __('Cari') }}</button>
                                @if(($searchTerm ?? '') || ($filterDept ?? '') || ($filterKantor ?? ''))
                                    <a href="{{ route('accounts.index') }}" class="bg-red-50 text-red-500 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition">✕ {{ __('Reset') }}</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f9f8f6] border-b border-[#e5e0d8]">
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('ID / Role') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Karyawan') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Jabatan / Level') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Kontak') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider text-center">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-b border-[#f4f1ea] hover:bg-[#faf9f7] transition">
                                <td class="py-4 px-4">
                                    <div class="text-xs font-mono text-gray-400 mb-1">#{{ $user->employee_id }}</div>
                                    @php
                                        $badgeClass = match($user->role) {
                                            'managing_director' => 'bg-red-100 text-red-700 border-red-200',
                                            'director'          => 'bg-orange-100 text-orange-700 border-orange-200',
                                            'manager'           => 'bg-green-100 text-green-700 border-green-200',
                                            'superadmin'        => 'bg-purple-100 text-purple-700 border-purple-200',
                                            'admin'             => 'bg-blue-100 text-blue-700 border-blue-200',
                                            default             => 'bg-gray-100 text-gray-600 border-gray-200',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full {{ $badgeClass }} border font-bold text-[8px] uppercase">
                                        {{ __($user->role) }}
                                    </span>

                                    @if($user->status === 'resigned')
                                        <div class="mt-1">
                                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200 font-bold text-[8px] uppercase tracking-tighter">{{ __('Resigned') }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-[#4a554a]">{{ $user->name }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-xs text-gray-700 font-bold">{{ $user->job_position ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ __('Level:') }} {{ $user->job_level ?? '-' }}</div>
                                    <div class="text-[9px] text-gray-400 mt-1">{{ __('Join:') }} {{ $user->join_date ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-[10px] text-gray-700 font-medium">{{ $user->office?->nama_kantor ?? '-' }}</div>
                                    <div class="text-[9px] text-gray-400 mb-1">{{ $user->department?->nama_departemen ?? '-' }}</div>
                                    <div class="text-[10px] font-mono text-blue-500">{{ $user->phone_number ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        {{-- Edit Button (Pencil) --}}
                                        @if(auth()->user()->hasPermission('users.edit_profile') || auth()->user()->hasPermission('users.edit_placement') || auth()->user()->hasPermission('users.edit_job') || auth()->user()->hasPermission('users.edit_role'))
                                            <a href="{{ route('accounts.edit', $user->id) }}" class="p-1.5 bg-gray-50 rounded-lg hover:bg-gray-200 transition" title="{{ __('Edit') }}">✏️</a>
                                        @endif

                                        {{-- Resign Button (Ganti dari Hapus) --}}
                                        @if(auth()->user()->hasPermission('users.resign') && $user->status === 'active')
                                            <button type="button" onclick="openResignModal({{ $user->id }}, '{{ $user->name }}')" class="p-1.5 bg-orange-50 rounded-lg hover:bg-orange-100 text-orange-600 transition" title="{{ __('Resign Karyawan') }}">🚪 {{ __('Resign') }}</button>
                                        @endif

                                        {{-- Tombol Hapus Permanen dihapus --}}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($users->isEmpty())
                            <tr><td colspan="5" class="py-12 text-center text-gray-400 italic">{{ __('Tidak ada data karyawan ditemukan.') }}</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Resign --}}
    <div id="resignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#f9f8f6]">
                <div>
                    <h3 class="text-xl font-bold text-[#4a554a]">{{ __('Proses Resign Karyawan') }}</h3>
                    <p class="text-sm text-gray-500 mt-1" id="resignUserName"></p>
                </div>
                <button onclick="closeResignModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            {{-- Content --}}
            <div class="p-6 overflow-y-auto space-y-6">
                {{-- Warning held items --}}
                <div id="heldItemsSection">
                    <div class="flex items-center gap-2 text-orange-600 font-bold mb-3">
                        <span>📦</span>
                        <h4>{{ __('Aset yang Masih Dipegang') }}</h4>
                    </div>
                    <div id="heldItemsList" class="space-y-2">
                        {{-- Data will be loaded via JS --}}
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- History items --}}
                <div>
                    <div class="flex items-center gap-2 text-gray-600 font-bold mb-3">
                        <span>📜</span>
                        <h4>{{ __('Riwayat Terakhir') }}</h4>
                    </div>
                    <div id="historyItemsList" class="text-xs space-y-1">
                        {{-- Data will be loaded via JS --}}
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3">
                <button onclick="closeResignModal()" class="px-6 py-2 text-gray-500 font-bold hover:text-gray-700 transition">{{ __('Batal') }}</button>
                <form id="resignConfirmForm" method="POST">
                    @csrf
                    <button type="submit" id="resignConfirmBtn" class="bg-orange-600 text-white px-6 py-2 rounded-xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition disabled:opacity-50 disabled:cursor-not-allowed">{{ __('Konfirmasi Resign') }}</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentUserId = null;

        async function openResignModal(userId, userName) {
            currentUserId = userId;
            document.getElementById('resignUserName').innerText = userName;
            document.getElementById('resignModal').classList.remove('hidden');
            document.getElementById('heldItemsList').innerHTML = '<p class="text-sm text-gray-400 italic">{{ __('Mengambil data...') }}</p>';
            document.getElementById('historyItemsList').innerHTML = '<p class="text-sm text-gray-400 italic">{{ __('Mengambil data...') }}</p>';
            document.getElementById('resignConfirmBtn').disabled = true;

            try {
                // Gunakan URL yang dinamis agar tidak error jika aplikasi di dalam subfolder
                const url = "{{ route('employees.details', ':id') }}".replace(':id', userId);
                const response = await fetch(url);
                const data = await response.json();

                // 1. Render Held Items
                const heldList = document.getElementById('heldItemsList');
                if (data.active_borrowings.length === 0) {
                    heldList.innerHTML = '<div class="p-4 bg-green-50 text-green-700 rounded-xl text-sm font-medium">✅ {{ __('Semua barang sudah dikembalikan.') }}</div>';
                    document.getElementById('resignConfirmBtn').disabled = false;
                } else {
                    heldList.innerHTML = data.active_borrowings.map(item => `
                        <div class="flex justify-between items-center p-3 bg-white border border-orange-100 rounded-xl shadow-sm">
                            <div>
                                <div class="font-bold text-sm text-gray-800">${item.product.description}</div>
                                <div class="text-[10px] font-mono text-orange-500">${item.product.full_nomor_unik}</div>
                            </div>
                            <button onclick="quickReturn(${item.id})" class="text-[10px] bg-orange-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-orange-700 transition">{{ __('Sudah Kembali') }}</button>
                        </div>
                    `).join('');
                    document.getElementById('heldItemsList').insertAdjacentHTML('afterbegin', '<div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm font-bold mb-3">⚠️ {{ __('Peringatan: Masih ada barang yang belum dikembalikan!') }}</div>');
                    document.getElementById('resignConfirmBtn').disabled = true;
                }

                // 2. Render History
                const histList = document.getElementById('historyItemsList');
                if (data.history.length === 0) {
                    histList.innerHTML = '<p class="text-gray-400 italic">{{ __('Tidak ada riwayat.') }}</p>';
                } else {
                    histList.innerHTML = data.history.map(item => {
                        const date = new Date(item.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                        const type = item.jenis_transaksi === 'meminjam' ? '<span class="text-orange-500 font-bold">PINJAM</span>' : '<span class="text-green-500 font-bold">KEMBALI</span>';
                        return `<div class="py-1 border-b border-gray-50 flex gap-2"><span class="text-gray-400 font-mono">${date}</span> | ${type} | <span>${item.product.description}</span></div>`;
                    }).join('');
                }

                // 3. Set Form Action
                let resignUrl = "{{ route('accounts.resign', ':id') }}".replace(':id', userId);
                document.getElementById('resignConfirmForm').action = resignUrl;

            } catch (error) {
                console.error(error);
                alert('{{ __('Gagal mengambil data karyawan.') }}');
            }
        }

        function closeResignModal() {
            document.getElementById('resignModal').classList.add('hidden');
        }

        async function quickReturn(historyId) {
            if (!confirm('{{ __('Proses pengembalian aset ini ke kantor?') }}')) return;

            try {
                let returnUrl = "{{ route('history.quickReturn', ':id') }}".replace(':id', historyId);
                const response = await fetch(returnUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    // Refresh modal data
                    openResignModal(currentUserId, document.getElementById('resignUserName').innerText);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error(error);
                alert('{{ __('Terjadi kesalahan saat mengembalikan aset.') }}');
            }
        }
    </script>
</x-app-layout>
