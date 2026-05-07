<x-app-layout>
    <x-slot name="header"><h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Edit Akun Karyawan') }}</h2></x-slot>
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
            <form action="{{ route('accounts.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if(auth()->user()->hasPermission('users.edit_job'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Employee ID') }}</label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" placeholder="Contoh: 00001">
                    </div>
                @endif
                    @if(auth()->user()->hasPermission('users.edit_role'))
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Peran (Role)') }}</label>
                        <select name="role_id" id="role-select" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" required onchange="togglePassword()">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                @if(auth()->user()->hasPermission('users.edit_profile'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Nama Lengkap') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" required>
                    </div>
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" required>
                    </div>
                </div>
                @endif

                @if(auth()->user()->hasPermission('users.edit_job') || auth()->user()->hasPermission('users.edit_profile'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    @if(auth()->user()->hasPermission('users.edit_job'))
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Jabatan (Job Position)') }}</label>
                        <input type="text" name="job_position" value="{{ old('job_position', $user->job_position) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" placeholder="Contoh: Marketing Staff">
                    </div>
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Level Pekerjaan') }}</label>
                        <input type="text" name="job_level" value="{{ old('job_level', $user->job_level) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" placeholder="Contoh: Junior / Senior">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Tanggal Bergabung') }}</label>
                        <input type="date" name="join_date" value="{{ old('join_date', $user->join_date) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20">
                    </div>
                    @endif
                    @if(auth()->user()->hasPermission('users.edit_profile'))
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Nomor Telepon') }}</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" placeholder="081234567890">
                    </div>
                    @endif
                </div>
                @endif

                @if(auth()->user()->hasPermission('users.edit_profile'))
                <div class="mb-8">
                    <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Foto Tanda Tangan') }}</label>
                    
                    @if($user->signature_path)
                    <div class="mb-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">{{ __('Tanda Tangan Saat Ini') }}</p>
                        <div class="p-4 border rounded-lg bg-gray-50 w-48">
                            <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Signature" class="max-h-24 mx-auto">
                        </div>
                    </div>
                    @endif

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[#d1cdba] border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="signature" class="relative cursor-pointer bg-white rounded-md font-medium text-[#a47b53] hover:text-[#8b6540] focus-within:outline-none">
                                    <span>{{ __('Ganti file') }}</span>
                                    <input id="signature" name="signature" type="file" class="sr-only" accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF {{ __('maksimal 2MB') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(auth()->user()->hasPermission('users.edit_role'))
                <div id="password-container" class="mb-5">
                    <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Ganti Password') }}</label>
                    <input type="password" name="password" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" placeholder="{{ __('Kosongkan jika tidak ingin ganti. Syarat: Min 8 Karakter, 1 Kapital, 1 Angka, 1 Simbol') }}">
                </div>
                @endif

                @if(auth()->user()->hasPermission('users.edit_placement'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Kantor') }}</label>
                        <select name="office_id" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20" required>
                            <option value="">-- {{ __('Pilih Kantor') }} --</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('office_id', $user->office_id) == $office->id ? 'selected' : '' }}>{{ $office->nama_kantor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[#5c6b5b] text-xs font-bold uppercase tracking-widest mb-2">{{ __('Departemen') }}</label>
                        <select name="department_id" class="w-full border-[#d1cdba] rounded-md shadow-sm focus:border-[#a47b53] focus:ring-[#a47b53] focus:ring-opacity-20">
                            <option value="">-- {{ __('Pilih Departemen') }} --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-[#5c6b5b] text-white px-8 py-3 rounded-lg shadow-lg hover:bg-[#4a554a] transition font-bold uppercase tracking-wider text-sm">{{ __('Perbarui Akun') }}</button>
                    <a href="{{ route('accounts.index') }}" class="text-[#8b6540] hover:underline font-semibold text-sm">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const select = document.getElementById('role-select');
            const selectedOption = select.options[select.selectedIndex];
            const slug = selectedOption.getAttribute('data-slug');
            const container = document.getElementById('password-container');
            
            if (slug === 'user') {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
