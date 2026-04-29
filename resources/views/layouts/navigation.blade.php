<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                        OSCE Farmasi
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-nav-link>

                        {{-- Data Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none h-16
                                {{ request()->routeIs('admin.stasi.*') || request()->routeIs('admin.kelas.*') || request()->routeIs('admin.mahasiswa.*') || request()->routeIs('admin.penguji.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Data
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="top: 100%;">
                                <div class="py-1">
                                    <a href="{{ route('admin.stasi.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.stasi.*') ? 'bg-gray-100' : '' }}">
                                        Stasi
                                    </a>
                                    <a href="{{ route('admin.kelas.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.kelas.*') ? 'bg-gray-100' : '' }}">
                                        Kelas
                                    </a>
                                    <a href="{{ route('admin.mahasiswa.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-gray-100' : '' }}">
                                        Mahasiswa
                                    </a>
                                    <a href="{{ route('admin.penguji.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.penguji.*') ? 'bg-gray-100' : '' }}">
                                        Penguji
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Jadwal Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none h-16
                                {{ request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.gelombang.*') || request()->routeIs('admin.jadwal-penguji.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Jadwal
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="top: 100%;">
                                <div class="py-1">
                                    <a href="{{ route('admin.jadwal.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.jadwal.*') ? 'bg-gray-100' : '' }}">
                                        Jadwal Ujian
                                    </a>
                                    <a href="{{ route('admin.gelombang.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.gelombang.*') ? 'bg-gray-100' : '' }}">
                                        Kelola Gelombang
                                    </a>
                                    <a href="{{ route('admin.jadwal-penguji.index') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.jadwal-penguji.*') ? 'bg-gray-100' : '' }}">
                                        Jadwal Penguji
                                    </a>
                                </div>
                            </div>
                        </div>
                        <x-nav-link :href="route('admin.rekap.index')" :active="request()->routeIs('admin.rekap.*')">
                            Rekap
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('penguji.dashboard')" :active="request()->routeIs('penguji.dashboard')">
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('penguji.penilaian.index')" :active="request()->routeIs('penguji.penilaian.*')">
                            Penilaian
                        </x-nav-link>
                        <x-nav-link :href="route('penguji.stasi.index')" :active="request()->routeIs('penguji.stasi.*')">
                            Daftar Stasi
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Server Clock --}}
                <div x-data="{ time: '{{ now()->format('H:i:s') }}' }" 
                     x-init="setInterval(() => { 
                         let d = new Date(); 
                         time = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); 
                     }, 1000)"
                     class="flex items-center mr-4 text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="time" class="font-mono"></span>
                </div>

                <span class="text-xs text-gray-500 mr-3 px-2 py-1 bg-gray-100 rounded">
                    {{ Auth::user()->role === 'admin' ? 'Admin' : 'Penguji' }}
                </span>
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

                        @if(Auth::user()->isAdmin())
                            <x-dropdown-link :href="route('admin.settings.index')">
                                Pengaturan
                            </x-dropdown-link>
                        @endif

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

            <!-- Mobile Clock + Hamburger -->
            <div class="flex items-center sm:hidden">
                {{-- Mobile Server Clock --}}
                <div x-data="{ time: '{{ now()->format('H:i:s') }}' }" 
                     x-init="setInterval(() => { 
                         let d = new Date(); 
                         time = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); 
                     }, 1000)"
                     class="flex items-center mr-2 text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="time" class="font-mono"></span>
                </div>
                {{-- Hamburger Button --}}
                <button @click="open = ! open" class="-me-2 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-responsive-nav-link>

                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Data</div>
                <x-responsive-nav-link :href="route('admin.stasi.index')" :active="request()->routeIs('admin.stasi.*')">
                    Stasi
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.kelas.index')" :active="request()->routeIs('admin.kelas.*')">
                    Kelas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.mahasiswa.index')" :active="request()->routeIs('admin.mahasiswa.*')">
                    Mahasiswa
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.penguji.index')" :active="request()->routeIs('admin.penguji.*')">
                    Penguji
                </x-responsive-nav-link>

                <div class="px-4 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jadwal</div>
                <x-responsive-nav-link :href="route('admin.jadwal.index')" :active="request()->routeIs('admin.jadwal.*')">
                    Jadwal Ujian
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.gelombang.index')" :active="request()->routeIs('admin.gelombang.*')">
                    Kelola Gelombang
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.jadwal-penguji.index')" :active="request()->routeIs('admin.jadwal-penguji.*')">
                    Jadwal Penguji
                </x-responsive-nav-link>

                <div class="border-t border-gray-200 my-1"></div>
                <x-responsive-nav-link :href="route('admin.rekap.index')" :active="request()->routeIs('admin.rekap.*')">
                    Rekap
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('penguji.dashboard')" :active="request()->routeIs('penguji.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penguji.penilaian.index')" :active="request()->routeIs('penguji.penilaian.*')">
                    Penilaian
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penguji.stasi.index')" :active="request()->routeIs('penguji.stasi.*')">
                    Daftar Stasi
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
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
