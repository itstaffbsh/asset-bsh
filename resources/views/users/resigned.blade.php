<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Arsip Karyawan Resign') }}</h2>
            <a href="{{ route('accounts.index') }}" class="text-[#a47b53] hover:underline font-bold text-sm">← {{ __('Kembali ke Manajemen Akun') }}</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-[#e5e0d8] overflow-hidden">
            <div class="p-8">
                @if(session('success'))
                    <div class="bg-[#d4ebd0] text-[#3a5a3a] p-4 rounded-md mb-6 border border-[#b8deb2] font-bold">{{ session('success') }}</div>
                @endif

                {{-- Search Bar --}}
                <div class="mb-6">
                    <form action="{{ route('accounts.resigned') }}" method="GET">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <div class="relative">
                                    <input type="text" name="q" value="{{ $searchTerm ?? '' }}"
                                           class="w-full pl-8 pr-4 py-2 border-[#d1cdba] rounded-lg shadow-sm focus:border-[#a47b53] focus:ring focus:ring-[#a47b53] focus:ring-opacity-20 text-sm"
                                           placeholder="{{ __('Cari mantan karyawan...') }}">
                                    <span class="absolute left-2.5 top-2.5 text-gray-400 text-xs">🔍</span>
                                </div>
                            </div>
                            <button type="submit" class="bg-[#5c6b5b] text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-[#4a554a] transition">{{ __('Cari') }}</button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#f9f8f6] border-b border-[#e5e0d8]">
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-xs uppercase tracking-wider">{{ __('ID / Karyawan') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-xs uppercase tracking-wider">{{ __('Terakhir Menjabat') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-xs uppercase tracking-wider text-center">{{ __('Tanggal Resign') }}</th>
                                <th class="py-3 px-4 font-semibold text-[#5c6b5b] text-xs uppercase tracking-wider text-center">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="border-b border-[#f4f1ea] hover:bg-[#faf9f7] transition">
                                <td class="py-4 px-4">
                                    <div class="text-[10px] font-mono text-gray-400">#{{ $user->employee_id }}</div>
                                    <div class="font-bold text-[#4a554a]">{{ $user->name }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="py-4 px-4 text-sm">
                                    <div class="font-bold text-gray-700">{{ $user->job_position ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->department?->nama_departemen ?? '-' }}</div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="inline-block px-3 py-1 bg-red-50 text-red-700 rounded-full font-bold text-xs border border-red-100">
                                        🗓️ {{ $user->resigned_at ? $user->resigned_at->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('employees.details', $user->id) }}" class="p-1.5 bg-gray-50 rounded-lg hover:bg-gray-200 transition inline-block" title="{{ __('Lihat Riwayat') }}">👁️ {{ __('Detail History') }}</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic">{{ __('Belum ada arsip karyawan resign.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
