<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">
                {{ __('Pembuatan Akun Karyawan') }}
            </h2>
            <a href="{{ route('accounts.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-md font-bold text-sm hover:bg-gray-200 transition">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-xl shadow-sm border border-[#e5e0d8] p-8">
            @if ($errors->any())
                <div class="bg-red-50 text-red-800 p-4 rounded-lg mb-6 border border-red-200">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6 border border-red-200">{{ session('error') }}</div>
            @endif

            <form action="{{ route('accounts.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-[#5c6b5b] text-sm font-bold uppercase tracking-widest mb-2">{{ __('Pilih Data Karyawan') }} <span class="text-red-500">*</span></label>
                    <select name="user_id" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20 py-3" required>
                        <option value="">-- {{ __('Pilih Karyawan yang Belum Memiliki Akun') }} --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('user_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} (ID: {{ $emp->employee_id ?? '-' }}) - {{ $emp->department?->nama_departemen ?? 'Tanpa Dept' }}
                            </option>
                        @endforeach
                    </select>
                    @if($employees->isEmpty())
                        <p class="text-sm text-red-500 mt-2 italic">{{ __('Semua data karyawan aktif sudah memiliki akun. Silakan tambahkan Data Karyawan baru terlebih dahulu.') }}</p>
                    @endif
                </div>

                <div class="mb-6">
                    <label class="block text-[#5c6b5b] text-sm font-bold uppercase tracking-widest mb-2">{{ __('Peran (Role Hak Akses)') }} <span class="text-red-500">*</span></label>
                    <select name="role_id" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20 py-3" required>
                        <option value="">-- {{ __('Pilih Role') }} --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-8">
                    <label class="block text-[#5c6b5b] text-sm font-bold uppercase tracking-widest mb-2">{{ __('Password Login') }} <span class="text-red-500">*</span></label>
                    <input type="password" name="password" value="Balibsh@1234" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20 py-3" placeholder="{{ __('Min 8 Karakter, 1 Kapital, 1 Angka, 1 Simbol') }}" required>
                    <p class="text-[10px] text-gray-400 mt-1 uppercase italic">{{ __('Contoh: Balibsh@1234') }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-[#5c6b5b] text-white px-8 py-3 rounded-lg shadow-lg hover:bg-[#4a554a] transition font-bold uppercase tracking-wider text-sm" {{ $employees->isEmpty() ? 'disabled' : '' }}>
                        {{ __('Buat Akun') }}
                    </button>
                    <a href="{{ route('accounts.index') }}" class="text-[#8b6540] hover:underline font-semibold text-sm">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
