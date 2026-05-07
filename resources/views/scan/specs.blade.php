<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Spesifikasi Teknis') }} - {{ $product->description }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f1ea; }
        h1, h2, h3 { font-family: 'Lora', serif; }
    </style>
</head>
<body class="antialiased pb-16">

    <!-- [BAGIAN: HEADER] | Menampilkan nama barang dan kategorinya -->
    <div class="max-w-md mx-auto mt-8 px-4">
        <div class="bg-[#2d3748] text-white rounded-2xl px-6 py-5 mb-4 shadow-md">
            <p class="text-xs text-gray-400 mb-1 uppercase tracking-widest">{{ __('Spesifikasi Teknis') }}</p>
            <h1 class="text-2xl font-bold leading-tight">{{ $product->description }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $product->classification->nama_klasifikasi }}</p>
        </div>

        <!-- [BAGIAN: DETAIL] | Menampilkan rincian Processor, RAM, SSD, dll -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-[#e5e0d8]">
            <div class="space-y-6">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ __('Processor') }}</p>
                    <p class="text-lg font-semibold text-[#2d3748]">{{ $product->processor ?? '-' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ __('RAM') }}</p>
                        <p class="text-md font-semibold text-[#2d3748]">{{ $product->ram }}</p>
                        @if($product->new_ram) <p class="text-xs text-green-600 font-medium">{{ __('Upgrade') }}: {{ $product->new_ram }}</p> @endif
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ __('Storage') }}</p>
                        <p class="text-md font-semibold text-[#2d3748]">{{ $product->ssd }}</p>
                        @if($product->new_ssd) <p class="text-xs text-green-600 font-medium">{{ __('Upgrade') }}: {{ $product->new_ssd }}</p> @endif
                    </div>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ __('Operating System') }}</p>
                    <p class="text-md font-semibold text-[#2d3748]">{{ $product->os ?? '-' }}</p>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ __('Serial Number / Screen ID') }}</p>
                    <p class="text-sm font-mono text-gray-600">{{ $product->serial_number ?? '-' }} / {{ $product->screen_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ __('MAC / IMEI') }}</p>
                    <p class="text-xs font-mono text-gray-600">{{ $product->mac_address ?? '-' }} / {{ $product->imei ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
