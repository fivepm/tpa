<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100 flex">
            @if (Auth::user()->role !== 'orangtua')
            <aside 
                class="w-64 bg-green-800 text-white flex-shrink-0 fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-200 ease-in-out z-20 flex flex-col shadow-lg"
                :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
                >
                <div class="p-4 text-2xl font-bold border-b border-green-700 flex justify-center items-center gap-3">
                    <img src="https://placehold.co/100x100/ffffff/16a34a?text=TPA" alt="Logo" class="h-8 w-8 rounded-full object-cover">
                    <a href="#">TPA Sunten Permai</a>
                </div>
                <nav class="mt-4">
                    {{-- Pengurus --}}
                    @if (Auth::user()->role == 'pengurus')
                        <a href="{{ route('pengurus.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-green-700 {{ request()->routeIs('pengurus.dashboard') ? 'bg-green-700' : '' }}">
                            <span class="mr-2">🏠</span> Dashboard
                        </a>
                        {{-- Group: Users --}}
                        <div x-data="{ open: {{ request()->routeIs('pengurus.kelola-pengurus.*', 'pengurus.kelola-guru.*', 'pengurus.kelola-orangtua.*') ? 'true' : 'false' }} }" class="mb-1">
                            <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none">
                                <div>
                                    <span class="mr-2">👥</span>
                                    <span class="font-semibold">Users</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="pl-4 mt-1 space-y-1">
                                <a href="{{ route('pengurus.kelola-pengurus.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-pengurus.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Pengurus
                                </a>
                                <a href="{{ route('pengurus.kelola-guru.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-guru.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Guru
                                </a>
                                <a href="{{ route('pengurus.kelola-orangtua.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-orangtua.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Orang Tua
                                </a>
                            </div>
                        </div>

                        {{-- Group: TPA --}}
                        <div x-data="{ open: {{ request()->routeIs('pengurus.kelola-kelas.*', 'pengurus.kelola-materi.*', 'pengurus.kelola-siswa.*', 'pengurus.kelola-walikelas.*') ? 'true' : 'false' }} }" class="mb-1">
                            <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none">
                                <div>
                                    <span class="mr-2">🕌</span>
                                    <span class="font-semibold">TPA</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="pl-4 mt-1 space-y-1">
                                <a href="{{ route('pengurus.kelola-kelas.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-kelas.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Kelas
                                </a>
                                <a href="{{ route('pengurus.kelola-materi.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-materi.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Materi
                                </a>
                                <a href="{{ route('pengurus.kelola-siswa.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-siswa.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Siswa
                                </a>
                                <a href="{{ route('pengurus.kelola-walikelas.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-walikelas.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Wali Kelas
                                </a>
                            </div>
                        </div>

                        {{-- Group: Jadwal --}}
                        <div x-data="{ open: {{ request()->routeIs('pengurus.kelola-jadwal.*', 'pengurus.kelola-harilibur.*') ? 'true' : 'false' }} }" class="mb-1">
                            <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none">
                                <div>
                                    <span class="mr-2">📅</span>
                                    <span class="font-semibold">Jadwal</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="pl-4 mt-1 space-y-1">
                                <a href="{{ route('pengurus.kelola-jadwal.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-jadwal.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Jadwal
                                </a>
                                <a href="{{ route('pengurus.kelola-harilibur.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.kelola-harilibur.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kelola Hari Libur
                                </a>
                            </div>
                        </div>

                        {{-- Group: Rekapitulasi --}}
                        <div x-data="{ open: {{ request()->routeIs('pengurus.laporan.*', 'pengurus.rekap-jurnal.*') ? 'true' : 'false' }} }" class="mb-1">
                            <button @click="open = !open" class="w-full flex justify-between items-center px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none">
                                <div>
                                    <span class="mr-2">📊</span>
                                    <span class="font-semibold">Rekapitulasi</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="pl-4 mt-1 space-y-1">
                                <a href="{{ route('pengurus.laporan.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.laporan.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Kehadiran
                                </a>
                                <a href="{{ route('pengurus.rekap-jurnal.index') }}" class="ml-3 block px-4 py-2 text-sm rounded-lg hover:bg-green-700 {{ request()->routeIs('pengurus.rekap-jurnal.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                                    Jurnal
                                </a>
                            </div>
                        </div>
                    {{-- Guru --}}
                    @elseif (Auth::user()->role == 'guru')
                        <a href="{{ route('guru.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-green-700 {{ request()->routeIs('guru.dashboard') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                            <span class="mr-2">🏠</span> Dashboard
                        </a>
                        <a href="{{ route('guru.presensi.index') }}" class="flex items-center px-4 py-2 hover:bg-green-700 {{ request()->routeIs('guru.presensi.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                            <span class="mr-2">🎓</span> Presensi
                        </a>
                        <a href="{{ route('guru.perkembangan.index') }}" class="flex items-center px-4 py-2 hover:bg-green-700 {{ request()->routeIs('guru.perkembangan.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                            <span class="mr-2">🗓️</span> Perkembangan Siswa
                        </a>
                        <a href="{{ route('guru.jurnal.index') }}" class="flex items-center px-4 py-2 hover:bg-green-700 {{ request()->routeIs('guru.jurnal.*') ? 'bg-green-600 font-semibold shadow-inner' : '' }}">
                            <span class="mr-2">📔</span> Jurnal Mengajar
                        </a>
                    @endif
                </nav>
                
            </aside>
            <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden" x-cloak></div>
            @endif

            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex">
                            
                            @if (Auth::user()->role !== 'orangtua')
                            <button @click="sidebarOpen = true" class="md:hidden text-gray-500 focus:outline-none mr-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            @endif

                            @if (isset($header))
                                <div>
                                    {{ $header }}
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center ms-6">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <!-- Desktop: Nama & Icon Dropdown -->
                                        <div class="hidden sm:flex items-center">
                                            <div>{{ Auth::user()->nama }}</div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <!-- Mobile: Icon User Circle -->
                                        <div class="sm:hidden flex items-center justify-center p-1">
                                            <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.index')">
                                        👤 Profil Saya
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-6">
                    {{-- Banner Fast Login (jika belum punya credential) --}}
                    @if(Auth::user()->webAuthnCredentials()->count() === 0 && session('show_webauthn_prompt', true))
                        <div id="webauthn-banner"
                             x-data="{ show: false }"
                             x-init="async () => { try { show = !!(window.PublicKeyCredential && await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable()); } catch{} }"
                             x-show="show"
                             class="mb-4 flex items-center justify-between bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🔏</span>
                                <div>
                                    <p class="text-sm font-semibold text-indigo-800">Aktifkan Fast Login!</p>
                                    <p class="text-xs text-indigo-600">Login lebih cepat menggunakan fingerprint, Face ID, atau PIN perangkat ini.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('profile.index') }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 transition">
                                    Daftar Sekarang
                                </a>
                                <button @click="show = false" class="text-indigo-400 hover:text-indigo-600 text-lg leading-none">&times;</button>
                            </div>
                        </div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
