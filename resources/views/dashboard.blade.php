<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Welcome banner --}}
        <div class="bg-gradient-to-r from-[#5c6b5b] to-[#4a554a] text-white rounded-xl p-8 mb-6 shadow-md">
            <h3 class="text-2xl font-serif font-bold mb-1">{{ __('Selamat Datang') }}, {{ Auth::user()->name }}!</h3>
            <p class="text-[#c8dfc6] text-sm">
                {{ __(ucwords(str_replace('_', ' ', auth()->user()->role))) }} 
                — {{ auth()->user()->office?->nama_kantor ?? '-' }}
            </p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @if(auth()->user()->hasPermission('products.view'))
            <div class="bg-white rounded-xl border border-[#e5e0d8] p-6 shadow-sm">
                <p class="text-sm text-gray-400 mb-1">{{ __('Total Aset') }}</p>
                <p class="text-3xl font-bold text-[#4a554a]">{{ \App\Models\Aset::count() }}</p>
            </div>
            @endif
            @if(auth()->user()->hasPermission('master.departments'))
            <div class="bg-white rounded-xl border border-[#e5e0d8] p-6 shadow-sm">
                <p class="text-sm text-gray-400 mb-1">{{ __('Total Departemen') }}</p>
                <p class="text-3xl font-bold text-[#4a554a]">{{ \App\Models\Department::count() }}</p>
            </div>
            @endif
            @if(auth()->user()->hasPermission('transactions.view_history'))
            <div class="bg-white rounded-xl border border-[#e5e0d8] p-6 shadow-sm">
                <p class="text-sm text-gray-400 mb-1">{{ __('Total Transaksi') }}</p>
                <p class="text-3xl font-bold text-[#4a554a]">{{ \App\Models\ProductHistory::count() }}</p>
            </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-[#e5e0d8] p-8 shadow-sm">
            <h4 class="font-serif text-lg font-bold text-[#4a554a] mb-4">{{ __('Menu Cepat') }}</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {{-- Daftar Aset --}}
                @if(auth()->user()->hasPermission('products.view'))
                <a href="{{ route('products.index') }}" class="flex flex-col items-center p-4 bg-[#f4f1ea] rounded-lg hover:bg-[#e8e4da] transition text-center border border-[#e5e0d8]">
                    <span class="text-3xl mb-2">📦</span>
                    <span class="text-sm font-semibold text-[#4a554a]">{{ __('Daftar Aset') }}</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('requests.view'))
                <a href="{{ route('asset-requests.index') }}" class="flex flex-col items-center p-4 bg-[#fdf2f2] rounded-lg hover:bg-[#fee2e2] transition text-center border border-[#fecaca]">
                    <span class="text-3xl mb-2">📝</span>
                    <span class="text-sm font-semibold text-red-800">{{ __('Request Aset') }}</span>
                </a>
                @endif

                {{-- Transaksi --}}
                @if(auth()->user()->hasPermission('transactions.transfer'))
                <a href="{{ route('products.meminjam') }}" class="flex flex-col items-center p-4 bg-[#e8f5e4] rounded-lg hover:bg-[#d4ebd0] transition text-center border border-[#c8dfc6]">
                    <span class="text-3xl mb-2">🤝</span>
                    <span class="text-sm font-semibold text-[#3a5a3a]">{{ __('Pinjam Aset') }}</span>
                </a>
                <a href="{{ route('products.kembali') }}" class="flex flex-col items-center p-4 bg-[#f0f9ff] rounded-lg hover:bg-[#e0f2fe] transition text-center border border-[#bae6fd]">
                    <span class="text-3xl mb-2">🔄</span>
                    <span class="text-sm font-semibold text-[#0369a1]">{{ __('Kembali Aset') }}</span>
                </a>
                @endif

                {{-- Master Data & Users --}}
                @if(auth()->user()->hasPermission('users.view'))
                <a href="{{ route('accounts.index') }}" class="flex flex-col items-center p-4 bg-[#f4f1ea] rounded-lg hover:bg-[#e8e4da] transition text-center border border-[#e5e0d8]">
                    <span class="text-3xl mb-2">👥</span>
                    <span class="text-sm font-semibold text-[#4a554a]">{{ __('Management Account') }}</span>
                </a>
                @endif
                @if(auth()->user()->hasPermission('master.offices'))
                <a href="{{ route('offices.index') }}" class="flex flex-col items-center p-4 bg-[#f4f1ea] rounded-lg hover:bg-[#e8e4da] transition text-center border border-[#e5e0d8]">
                    <span class="text-3xl mb-2">🏢</span>
                    <span class="text-sm font-semibold text-[#4a554a]">{{ __('Data Kantor') }}</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
