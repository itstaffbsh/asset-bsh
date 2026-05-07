<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Data Employee') }}</h2>
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('users.export'))
                    <a href="{{ route('employees.export') }}" class="bg-[#a47b53] text-white px-4 py-2 rounded-md shadow hover:bg-[#8b6540] transition font-bold text-sm">{{ __('EXPORT EXCEL') }}</a>
                @endif
                @if(auth()->user()->hasPermission('users.import'))
                    <button onclick="document.getElementById('import-modal').classList.remove('hidden')" class="bg-[#d1cdba] text-[#4a554a] px-4 py-2 rounded-md shadow hover:bg-[#c4c0a8] transition font-bold text-sm">{{ __('IMPORT EXCEL') }}</button>
                @endif
                @if(auth()->user()->hasPermission('users.create_employee'))
                    <a href="{{ route('employees.create') }}" class="bg-[#5c6b5b] text-[#f4f1ea] px-4 py-2 rounded-md shadow hover:bg-[#4a554a] transition font-bold text-sm">+ {{ __('TAMBAH KARYAWAN') }}</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Import Modal --}}
        <div id="import-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-xl shadow-xl w-96">
                <h3 class="text-lg font-bold mb-4">{{ __('Import Data Karyawan') }}</h3>
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="mb-4 w-full" required>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 text-gray-500">{{ __('Batal') }}</button>
                        <button type="submit" class="bg-[#5c6b5b] text-white px-4 py-2 rounded-md">{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[#e5e0d8] overflow-hidden">
            <div class="p-8">
                @if(session('success'))
                    <div class="bg-[#d4ebd0] text-[#3a5a3a] p-4 rounded-md mb-6 border border-[#b8deb2] font-bold">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6 border border-red-200">{!! session('error') !!}</div>
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
                    <form action="{{ route('employees.data') }}" method="GET">
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
                                    <a href="{{ route('employees.data') }}" class="bg-red-50 text-red-500 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100 transition">✕ {{ __('Reset') }}</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f9f8f6] border-b border-[#e5e0d8]">
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Employee ID') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Karyawan') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Jabatan / Level') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider">{{ __('Kontak') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider text-center">{{ __('Status') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-[10px] uppercase tracking-wider text-center">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-b border-[#f4f1ea] hover:bg-[#faf9f7] transition">
                                <td class="py-4 px-4">
                                    <div class="text-xs font-mono text-gray-400">#{{ $user->employee_id }}</div>
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
                                    @if($user->status === 'resigned')
                                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200 font-bold text-[8px] uppercase">{{ __('Resigned') }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 border border-green-200 font-bold text-[8px] uppercase">{{ __('Aktif') }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if(auth()->user()->hasPermission('users.edit_employee'))
                                            <a href="{{ route('employees.edit', $user->id) }}" class="p-1.5 bg-gray-50 rounded-lg hover:bg-gray-200 transition" title="{{ __('Edit Karyawan') }}">✏️</a>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.delete'))
                                            <button type="button" onclick="openDeleteEmployeeModal('{{ route('employees.destroy', $user->id) }}', '{{ $user->name }}')" class="p-1.5 bg-red-50 rounded-lg hover:bg-red-100 transition" title="{{ __('Hapus Karyawan') }}">🗑️</button>
                                        @endif
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

    {{-- Delete Employee Modal --}}
    <div id="deleteEmployeeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeDeleteEmployeeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form id="deleteEmployeeForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div>
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">{{ __('Hapus Karyawan') }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">{{ __('Apakah Anda yakin ingin menghapus data karyawan') }} <span id="deleteEmployeeName" class="font-bold"></span>? {{ __('Tindakan ini tidak dapat dibatalkan.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">{{ __('Hapus Data') }}</button>
                        <button type="button" onclick="closeDeleteEmployeeModal()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5c6b5b] sm:mt-0 sm:col-start-1 sm:text-sm">{{ __('Batal') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteEmployeeModal(actionUrl, name) {
            document.getElementById('deleteEmployeeForm').action = actionUrl;
            document.getElementById('deleteEmployeeName').textContent = name;
            document.getElementById('deleteEmployeeModal').classList.remove('hidden');
        }

        function closeDeleteEmployeeModal() {
            document.getElementById('deleteEmployeeModal').classList.add('hidden');
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#5c6b5b'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: "{!! session('error') !!}",
                confirmButtonColor: '#5c6b5b'
            });
        @endif
    </script>
</x-app-layout>
