<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Edit Role') }}: {{ $role->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Info Utama --}}
                <div class="bg-white p-6 rounded-xl border border-[#e5e0d8] shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Nama Role') }}</label>
                            <input type="text" name="name" value="{{ $role->name }}" required class="w-full rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Deskripsi (Opsional)') }}</label>
                            <input type="text" name="description" value="{{ $role->description }}" class="w-full rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('roles.index') }}" class="bg-white border border-[#e5e0d8] text-gray-600 px-6 py-2 rounded-lg font-bold hover:bg-gray-50 transition">{{ __('Batal') }}</a>
                    <button type="submit" class="bg-[#4a554a] text-white px-10 py-2 rounded-lg font-bold hover:bg-[#3a443a] transition shadow-md">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
