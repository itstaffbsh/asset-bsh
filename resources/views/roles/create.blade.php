<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Buat Role Baru') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Info Utama --}}
                <div class="bg-white p-6 rounded-xl border border-[#e5e0d8] shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Nama Role') }}</label>
                            <input type="text" name="name" required class="w-full rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]" placeholder="{{ __('Contoh: Manager Accounting') }}">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Deskripsi (Opsional)') }}</label>
                            <input type="text" name="description" class="w-full rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]" placeholder="{{ __('Jelaskan kegunaan role ini...') }}">
                        </div>
                    </div>
                </div>

                {{-- Matriks Hak Akses --}}
                <div class="bg-white rounded-xl border border-[#e5e0d8] shadow-sm overflow-hidden">
                    <div class="bg-[#f4f1ea] px-6 py-4 border-b border-[#e5e0d8]">
                        <h3 class="font-serif font-bold text-[#4a554a]">{{ __('Matriks Hak Akses Fitur') }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Centang fitur dan tindakan yang diizinkan untuk role ini.') }}</p>
                    </div>
                    
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#faf9f6] text-[#4a554a] text-[10px] uppercase font-bold tracking-wider">
                                    <th class="px-6 py-3 border-b border-[#f0eee9] w-1/4">{{ __('Fitur / Modul') }}</th>
                                    <th class="px-6 py-3 border-b border-[#f0eee9]">{{ __('Hak Akses (Permissions)') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissionsByFeature as $feature => $permissions)
                                    <tr class="hover:bg-[#faf9f6] transition group">
                                        <td class="px-6 py-4 border-b border-[#f0eee9] bg-[#fdf2e9]/30 font-bold text-[#a47b53]">
                                            {{ $feature }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-[#f0eee9]">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @foreach($permissions as $permission)
                                                    <label class="flex items-center gap-2 cursor-pointer group-hover:scale-105 transition-transform origin-left">
                                                        <div class="relative flex items-center">
                                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                                data-slug="{{ $permission->slug }}"
                                                                onchange="checkDeptApproval(this)"
                                                                class="peer h-4 w-4 rounded border-gray-300 text-[#a47b53] focus:ring-[#a47b53]">
                                                        </div>
                                                        <span class="text-xs font-medium text-gray-600 peer-checked:text-[#a47b53] peer-checked:font-bold italic">
                                                            {{ $permission->name }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pilihan Departemen untuk Approval Tahap 2 --}}
                <div id="dept-approval-container" class="hidden bg-white p-6 rounded-xl border border-[#e5e0d8] shadow-sm">
                    <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Pilih Departemen yang Di-manage (Untuk Approval Tahap 2)') }}</label>
                    <p class="text-xs text-gray-500 mb-3">{{ __('Karena role ini memiliki hak akses Approval Tahap 2, pilih departemen mana yang akan mereka setujui permintaannya.') }}</p>
                    <select name="dept_approval_for" class="w-full md:w-1/2 rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]">
                        <option value="">{{ __('-- Pilih Departemen --') }}</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('roles.index') }}" class="bg-white border border-[#e5e0d8] text-gray-600 px-6 py-2 rounded-lg font-bold hover:bg-gray-50 transition">{{ __('Batal') }}</a>
                    <button type="submit" class="bg-[#4a554a] text-white px-10 py-2 rounded-lg font-bold hover:bg-[#3a443a] transition shadow-md">
                        {{ __('Simpan Role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function checkDeptApproval(checkbox) {
            if (checkbox.getAttribute('data-slug') === 'requests.approve_stage_2') {
                const container = document.getElementById('dept-approval-container');
                if (checkbox.checked) {
                    container.classList.remove('hidden');
                } else {
                    container.classList.add('hidden');
                }
            }
        }
    </script>
</x-app-layout>
