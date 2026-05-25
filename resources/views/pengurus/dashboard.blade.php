<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 {{ __('Dashboard Pengurus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl overflow-hidden relative">
                <div class="absolute inset-0 bg-white/10 pattern-dots opacity-20"></div>
                <div class="p-8 sm:p-10 relative z-10 flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-2xl sm:text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->nama }}! 👋</h3>
                        <p class="text-indigo-100 text-sm sm:text-base max-w-xl">
                            Ini adalah pusat kendali sistem Anda. Pantau aktivitas belajar mengajar, kelola data pengguna, dan pastikan semuanya berjalan lancar hari ini.
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 p-4 rounded-full backdrop-blur-sm">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card: Guru -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-xl group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Guru</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $totalGuru }}</h4>
                    </div>
                </div>

                <!-- Card: Siswa -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="bg-green-50 text-green-600 p-4 rounded-xl group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Siswa</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $totalSiswa }}</h4>
                    </div>
                </div>

                <!-- Card: Kelas -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="bg-purple-50 text-purple-600 p-4 rounded-xl group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Kelas</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $totalKelas }}</h4>
                    </div>
                </div>

                <!-- Card: Jadwal Hari Ini -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="bg-amber-50 text-amber-600 p-4 rounded-xl group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jadwal Hari Ini</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $totalJadwalHariIni }}</h4>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Jadwal Hari Ini -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Jadwal Mengajar ({{ $todayStr }})</h3>
                                <p class="text-sm text-gray-500">Daftar kelas yang aktif hari ini</p>
                            </div>
                            <span class="bg-indigo-100 text-indigo-700 py-1 px-3 rounded-full text-xs font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="p-0">
                            @if($jadwalHariIni->count() > 0)
                                <ul class="divide-y divide-gray-100">
                                    @foreach($jadwalHariIni as $jadwal)
                                        <li class="p-6 hover:bg-gray-50 transition-colors">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div class="flex items-start gap-4">
                                                    <div class="flex flex-col items-center justify-center bg-indigo-50 text-indigo-700 w-16 h-16 rounded-xl shrink-0 border border-indigo-100">
                                                        <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                                                        <span class="text-xs font-medium opacity-75">s/d</span>
                                                        <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-base font-bold text-gray-900">{{ $jadwal->materi->nama_materi ?? 'Materi Tidak Diketahui' }}</h4>
                                                        <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                            <span>{{ $jadwal->guru->nama ?? 'Guru Tidak Diketahui' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:text-right">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        Kelas {{ $jadwal->kelas->nama_kelas ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-12 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">Tidak ada jadwal</h3>
                                    <p class="mt-1 text-sm text-gray-500">Belum ada jadwal mengajar yang terdaftar untuk hari ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Recent Activity & Jurnal -->
                <div class="space-y-6">
                    <!-- Info Card -->
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl shadow-sm p-6 text-white relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 opacity-20">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="relative z-10">
                            <p class="text-indigo-100 text-sm font-medium uppercase tracking-wider mb-1">Jurnal Tercatat</p>
                            <h3 class="text-3xl font-bold mb-1">{{ $jurnalBulanIni }} <span class="text-sm font-normal opacity-80">bulan ini</span></h3>
                            <a href="{{ route('pengurus.rekap-jurnal.index') }}" class="inline-flex items-center text-sm font-medium text-white hover:text-indigo-200 transition-colors mt-2 group">
                                Lihat Rekapitulasi 
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Jurnals -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Jurnal Terbaru</h3>
                        </div>
                        <div class="p-0">
                            @if($jurnalTerbaru->count() > 0)
                                <ul class="divide-y divide-gray-100">
                                    @foreach($jurnalTerbaru as $jurnal)
                                        <li class="p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex gap-4">
                                                <div class="mt-1">
                                                    <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_0_4px_rgba(34,197,94,0.15)]"></div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 truncate">
                                                        {{ $jurnal->jadwal->materi->nama_materi ?? 'Materi Terhapus' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                                        Oleh: <span class="font-medium text-gray-700">{{ $jurnal->jadwal->guru->nama ?? 'Guru Terhapus' }}</span>
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        {{ $jurnal->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-6 text-center">
                                    <p class="text-sm text-gray-500">Belum ada jurnal yang dicatat hari ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
