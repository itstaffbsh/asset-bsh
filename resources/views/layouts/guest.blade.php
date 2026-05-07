<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>BSH ASSET - {{ __('Login') }}</title>

        <!-- Fonts: Lora for Titles, Inter for Body -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body { font-family: 'Inter', sans-serif; background-color: #f4f1ea; }
            .font-serif { font-family: 'Lora', serif; }
        </style>
    </head>
    <body class="text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="text-center mb-8">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('images/Primier-Logo.webp') }}" alt="BSH Logo" class="h-16 w-auto object-contain">
                    <p class="text-xs text-[#a47b53] uppercase tracking-[0.3em] font-bold">{{ __('Sistem Manajemen Aset') }}</p>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-[2rem] border border-[#e5e0d8]">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
