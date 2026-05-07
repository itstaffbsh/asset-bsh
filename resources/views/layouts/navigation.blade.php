<nav x-data="{ open: false }" class="bg-white border-b border-[#e5e0d8]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- [BAGIAN: LOGO] | Identitas Sistem -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 bg-[#5c6b5b] rounded-lg flex items-center justify-center text-white text-lg shadow-sm group-hover:bg-[#4a554a] transition">
                            🏰
                        </div>
                        <span class="font-serif font-bold text-xl text-[#4a554a] tracking-tight">BSH ASSET</span>
                    </a>
                </div>

                <!-- [BAGIAN: MENU] | Tautan Navigasi Utama -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->hasPermission('requests.view'))
                    <x-nav-link :href="route('asset-requests.index')" :active="request()->routeIs('asset-requests.*')">
                        {{ __('Permintaan Aset') }}
                    </x-nav-link>
                    @endif

                    {{-- [MENU KHUSUS] | Master Data (Muncul jika punya salah satu akses) --}}
                    @if(auth()->user()->hasPermission('users.view') || auth()->user()->hasPermission('roles.manage') || auth()->user()->hasPermission('master.offices') || auth()->user()->hasPermission('master.classifications') || auth()->user()->hasPermission('master.departments'))
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                         <div>{{ __('Master Data') }}</div>
                                         <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                     @if(auth()->user()->hasPermission('users.view'))
                                         <x-dropdown-link :href="route('employees.data')">{{ __('Data Employee') }}</x-dropdown-link>
                                         <x-dropdown-link :href="route('accounts.index')">{{ __('Management Account') }}</x-dropdown-link>
                                     @endif
                                     @if(auth()->user()->hasPermission('roles.manage'))
                                         <x-dropdown-link :href="route('roles.index')">{{ __('Role & Hak Akses') }}</x-dropdown-link>
                                     @endif
                                     @if(auth()->user()->hasPermission('master.offices'))
                                         <x-dropdown-link :href="route('offices.index')">{{ __('Kantor') }}</x-dropdown-link>
                                     @endif
                                     @if(auth()->user()->hasPermission('master.classifications'))
                                         <x-dropdown-link :href="route('classifications.index')">{{ __('Klasifikasi') }}</x-dropdown-link>
                                     @endif
                                     @if(auth()->user()->hasPermission('master.departments'))
                                         <x-dropdown-link :href="route('departments.index')">{{ __('Departemen') }}</x-dropdown-link>
                                     @endif
                                     <div class="border-t border-gray-100"></div>
                                     @if(auth()->user()->hasPermission('assets.delete'))
                                         <x-dropdown-link :href="route('products.trash')" class="text-red-500">{{ __('Tempat Sampah') }}</x-dropdown-link>
                                     @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- [MENU OPERASIONAL] | Aset dan Transaksi --}}
                    @if(auth()->user()->hasPermission('assets.view'))
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">
                            {{ __('Daftar Aset') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasPermission('transactions.loan') || auth()->user()->hasPermission('transactions.return'))
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                         <div>{{ __('Transaksi') }}</div>
                                         <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                     @if(auth()->user()->hasPermission('transactions.loan'))
                                         <x-dropdown-link :href="route('products.meminjam')">{{ __('Pinjam Aset') }}</x-dropdown-link>
                                     @endif
                                     @if(auth()->user()->hasPermission('transactions.return'))
                                         <x-dropdown-link :href="route('products.kembali')">{{ __('Kembali Aset') }}</x-dropdown-link>
                                     @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if(auth()->user()->hasPermission('transactions.history'))
                        <x-nav-link :href="route('history.index')" :active="request()->routeIs('history.*')">
                            {{ __('Riwayat Mutasi') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Language & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Language Switcher -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-1">
                                <span>🌐</span>
                                <span>{{ strtoupper(App::getLocale()) }}</span>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('lang.switch', 'id')" class="flex justify-between items-center">
                            <span>Bahasa Indonesia</span>
                            @if(App::getLocale() == 'id') <span class="text-xs text-green-600">✓</span> @endif
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('lang.switch', 'en')" class="flex justify-between items-center">
                            <span>English</span>
                            @if(App::getLocale() == 'en') <span class="text-xs text-green-600">✓</span> @endif
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(auth()->user()->hasPermission('requests.view'))
            <x-responsive-nav-link :href="route('asset-requests.index')" :active="request()->routeIs('asset-requests.*')">
                {{ __('Permintaan Aset') }}
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 flex justify-between items-center">
                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('lang.switch', 'id') }}" class="text-xs {{ App::getLocale() == 'id' ? 'font-bold text-green-600' : 'text-gray-500' }}">ID</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang.switch', 'en') }}" class="text-xs {{ App::getLocale() == 'en' ? 'font-bold text-green-600' : 'text-gray-500' }}">EN</a>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
