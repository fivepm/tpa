<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎓 {{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl shadow-xl overflow-hidden relative">
                <div class="absolute inset-0 bg-white/10 pattern-dots opacity-20"></div>
                <div class="p-8 sm:p-10 relative z-10 flex items-center justify-between">
                    <div class="text-white">
                        <h3 class="text-2xl sm:text-3xl font-bold mb-2">Selamat Datang, Ustadz/Ustadzah {{ Auth::user()->nama }}! 👋</h3>
                        <p class="text-emerald-100 text-sm sm:text-base max-w-xl">
                            Semoga hari ini penuh berkah. Pantau jadwal mengajar Anda, catat perkembangan santri, dan jangan lupa mengisi jurnal mengajar setelah kelas selesai.
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 p-4 rounded-full backdrop-blur-sm">
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card: Kelas -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="bg-teal-50 text-teal-600 p-4 rounded-xl group-hover:scale-110 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Kelas Diajar</p>
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

                <!-- Card: Jurnal Bulan Ini -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jurnal Bulan Ini</p>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $jurnalBulanIni }}</h4>
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
                                <p class="text-sm text-gray-500">Daftar kelas Anda hari ini</p>
                            </div>
                            <span class="bg-emerald-100 text-emerald-800 py-1 px-3 rounded-full text-xs font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="p-0">
                            @if($jadwalHariIni->count() > 0)
                                <ul class="divide-y divide-gray-100">
                                    @foreach($jadwalHariIni as $jadwal)
                                        <li class="p-6 hover:bg-gray-50 transition-colors">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div class="flex items-start gap-4">
                                                    <div class="flex flex-col items-center justify-center bg-emerald-50 text-emerald-700 w-16 h-16 rounded-xl shrink-0 border border-emerald-100">
                                                        <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                                                        <span class="text-xs font-medium opacity-75">s/d</span>
                                                        <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-base font-bold text-gray-900">{{ $jadwal->materi->nama_materi ?? 'Materi Tidak Diketahui' }}</h4>
                                                        <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                            <span>Kelas {{ $jadwal->kelas->nama_kelas ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="sm:text-right flex flex-col items-end gap-2">
                                                    <a href="{{ route('guru.jurnal.create', $jadwal->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                                        Isi Jurnal
                                                    </a>
                                                    <a href="{{ route('guru.presensi.show', ['kelas' => $jadwal->kelas->id_kelas]) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-emerald-600 text-emerald-700 hover:bg-emerald-50 transition">
                                                        Ambil Presensi
                                                    </a>
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
                                    <h3 class="text-lg font-medium text-gray-900">Alhamdulillah</h3>
                                    <p class="mt-1 text-sm text-gray-500">Anda tidak memiliki jadwal mengajar hari ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Recent Activity & Jurnal -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Akses Cepat</h3>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-4">
                            <a href="{{ route('guru.presensi.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition group border border-emerald-100">
                                <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">🎓</span>
                                <span class="text-xs font-semibold text-center">Presensi<br>Harian</span>
                            </a>
                            <a href="{{ route('guru.perkembangan.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl bg-teal-50 text-teal-700 hover:bg-teal-100 transition group border border-teal-100">
                                <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">🗓️</span>
                                <span class="text-xs font-semibold text-center">Evaluasi<br>Santri</span>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Jurnals -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Riwayat Jurnal</h3>
                            <a href="{{ route('guru.jurnal.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">Lihat Semua</a>
                        </div>
                        <div class="p-0">
                            @if($jurnalTerbaru->count() > 0)
                                <ul class="divide-y divide-gray-100">
                                    @foreach($jurnalTerbaru as $jurnal)
                                        <li class="p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex gap-4">
                                                <div class="mt-1">
                                                    <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.15)]"></div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 truncate">
                                                        {{ $jurnal->jadwal->materi->nama_materi ?? 'Materi Terhapus' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                                        Kelas {{ $jurnal->jadwal->kelas->nama_kelas ?? 'Terhapus' }}
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
                                    <p class="text-sm text-gray-500">Anda belum mencatat jurnal apapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
