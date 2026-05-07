<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">
                {{ __('Edit Data Karyawan') }} - {{ $user->name }}
            </h2>
            <a href="{{ route('employees.data') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-md font-bold text-sm hover:bg-gray-200 transition">
                {{ __('Kembali') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-[#e5e0d8] overflow-hidden">
            <form action="{{ route('employees.update', $user->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6 border border-red-200">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-md mb-6 border border-red-100 text-sm">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('ID Karyawan') }}</label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Nama Lengkap') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Nomor HP') }}</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Departemen') }}</label>
                        <select name="department_id" class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                            <option value="">{{ __('-- Pilih Departemen --') }}</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id', $user->department_id) == $d->id ? 'selected' : '' }}>{{ $d->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Kantor') }} <span class="text-red-500">*</span></label>
                        <select name="office_id" required class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                            <option value="">{{ __('-- Pilih Kantor --') }}</option>
                            @foreach($offices as $o)
                                <option value="{{ $o->id }}" {{ old('office_id', $user->office_id) == $o->id ? 'selected' : '' }}>{{ $o->nama_kantor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Jabatan') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="job_position" value="{{ old('job_position', $user->job_position) }}" required class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]" placeholder="{{ __('Masukkan jabatan...') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Job Level') }} <span class="text-red-500">*</span></label>
                        <select name="job_level" required class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                            <option value="">{{ __('-- Pilih Job Level --') }}</option>
                            @foreach(['Director', 'Manager', 'Assistant Manager', 'Supervisor', 'Staff', 'Assistant Director'] as $level)
                                <option value="{{ $level }}" {{ old('job_level', $user->job_level) == $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-[#5c6b5b] mb-1">{{ __('Tanggal Bergabung') }}</label>
                        <input type="date" name="join_date" value="{{ old('join_date', $user->join_date) }}" class="w-full border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53]">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-[#5c6b5b] text-white px-6 py-2 rounded-lg font-bold hover:bg-[#4a554a] transition shadow-md">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
