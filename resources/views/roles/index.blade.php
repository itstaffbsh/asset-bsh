<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Manajemen Role & Hak Akses') }}</h2>
            <a href="{{ route('roles.create') }}" class="bg-[#4a554a] hover:bg-[#3a443a] text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                + {{ __('Tambah Role Baru') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-[#e5e0d8]">
                <div class="p-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f4f1ea] text-[#4a554a] uppercase text-xs font-bold">
                                <th class="px-6 py-4 border-b border-[#e5e0d8]">{{ __('Nama Role') }}</th>
                                <th class="px-6 py-4 border-b border-[#e5e0d8]">{{ __('Slug') }}</th>
                                <th class="px-6 py-4 border-b border-[#e5e0d8]">{{ __('Jumlah Permission') }}</th>
                                <th class="px-6 py-4 border-b border-[#e5e0d8]">{{ __('Deskripsi') }}</th>
                                <th class="px-6 py-4 border-b border-[#e5e0d8] text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($roles as $role)
                                <tr class="hover:bg-[#faf9f6] transition">
                                    <td class="px-6 py-4 border-b border-[#f0eee9] font-bold text-[#4a554a]">
                                        {{ $role->name }}
                                        @if($role->slug === 'superadmin')
                                            <span class="ml-2 px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] rounded-full uppercase">{{ __('System') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 border-b border-[#f0eee9] text-gray-500">{{ $role->slug }}</td>
                                    <td class="px-6 py-4 border-b border-[#f0eee9]">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md font-bold">
                                            {{ $role->slug === 'superadmin' ? __('FULL ACCESS') : $role->permissions_count . ' ' . __('Fitur') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-[#f0eee9] text-gray-500 italic">
                                        {{ $role->description ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-[#f0eee9] text-right">
                                        <div class="flex justify-end gap-2 items-center">
                                            <a href="{{ route('roles.detail', $role) }}" class="text-green-600 hover:text-green-800 font-bold">{{ __('Detail') }}</a>
                                            <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:text-blue-800 font-bold">{{ __('Edit') }}</a>
                                            
                                            @if(!in_array($role->slug, ['superadmin', 'managing_director']))
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('{{ __('Hapus role ini? User yang menggunakan role ini akan kehilangan akses.') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold">{{ __('Hapus') }}</button>
                                                </form>
                                            @else
                                                <span class="text-gray-300 italic text-xs font-bold ml-2">{{ __('System') }}</span>
                                            @endif
                                        </div>
                                    </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
