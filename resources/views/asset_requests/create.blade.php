<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl font-bold text-[#4a554a] leading-tight">{{ __('Buat Permintaan Aset Baru') }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm border border-[#e5e0d8] rounded-xl overflow-hidden">
            <form action="{{ route('asset-requests.store') }}" method="POST" class="p-8">
                @csrf
                
                {{-- Info Dasar --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Tingkat Criticality') }} <span class="text-red-500">*</span></label>
                        <select name="criticality_level" class="w-full rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]" required>
                            <option value="normal">{{ __('Normal') }}</option>
                            <option value="urgent">{{ __('Urgent') }}</option>
                            <option value="top priority">{{ __('Top Priority') }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-[#4a554a] mb-2">{{ __('Alasan Permintaan') }} <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="w-full rounded-lg border-[#e5e0d8] focus:border-[#a47b53] focus:ring-[#a47b53]" placeholder="{{ __('Jelaskan mengapa Anda membutuhkan barang ini...') }}" required></textarea>
                </div>

                {{-- Daftar Barang --}}
                <div class="mb-8">
                    <h3 class="font-serif text-lg font-bold text-[#4a554a] mb-4 border-b border-[#e5e0d8] pb-2">{{ __('Daftar Barang yang Diminta') }}</h3>
                    <div id="items-container">
                        <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 mb-4 items-start p-4 bg-[#faf9f6] rounded-lg border border-[#f0eee9]">
                            <div class="md:col-span-4">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">{{ __('Klasifikasi Barang') }}</label>
                                <select name="items[0][classification_id]" class="w-full text-sm rounded-lg border-[#e5e0d8]" required>
                                    <option value="">{{ __('Pilih Klasifikasi...') }}</option>
                                    @foreach($classifications as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->nama_klasifikasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">{{ __('Qty') }}</label>
                                <input type="number" name="items[0][qty]" min="1" value="1" class="w-full text-sm rounded-lg border-[#e5e0d8]" required>
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">{{ __('Spek Tambahan (Opsional)') }}</label>
                                <input type="text" name="items[0][specs]" class="w-full text-sm rounded-lg border-[#e5e0d8]" placeholder="{{ __('Contoh: RAM 16GB, Warna Putih, dll') }}">
                            </div>
                            <div class="md:col-span-1 pt-5">
                                <button type="button" class="text-red-400 hover:text-red-600 remove-item hidden">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" id="add-item" class="mt-2 flex items-center text-sm font-bold text-[#a47b53] hover:text-[#8b6540] transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="12 4v16m8-8H4"></path></svg>
                        {{ __('Tambah Barang Lain') }}
                    </button>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-[#e5e0d8]">
                    <a href="{{ route('asset-requests.index') }}" class="px-6 py-2 rounded-lg border border-gray-300 text-gray-600 text-sm font-bold hover:bg-gray-50 transition">{{ __('Batal') }}</a>
                    <button type="submit" class="px-8 py-2 rounded-lg bg-[#4a554a] text-white text-sm font-bold hover:bg-[#3a443a] transition shadow-md">{{ __('Kirim Permintaan') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('items-container');
            const addButton = document.getElementById('add-item');
            let rowIndex = 1;

            addButton.addEventListener('click', function() {
                const firstRow = container.querySelector('.item-row');
                const newRow = firstRow.cloneNode(true);
                
                // Update names
                newRow.querySelectorAll('select, input').forEach(input => {
                    const name = input.getAttribute('name');
                    input.setAttribute('name', name.replace('[0]', '[' + rowIndex + ']'));
                    input.value = input.tagName === 'SELECT' ? '' : (input.type === 'number' ? 1 : '');
                });

                // Show remove button
                const removeBtn = newRow.querySelector('.remove-item');
                removeBtn.classList.remove('hidden');
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });

                container.appendChild(newRow);
                rowIndex++;
            });
        });
    </script>
</x-app-layout>
