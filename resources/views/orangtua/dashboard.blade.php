<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ===== FILTER ===== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('orangtua.dashboard') }}" method="GET"
                          class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Anak --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Anak</label>
                            <select name="anak_id" onchange="this.form.submit()"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach($anakList as $anak)
                                    <option value="{{ $anak->id }}"
                                            {{ $selectedAnak->id == $anak->id ? 'selected' : '' }}>
                                        {{ $anak->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bulan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bulan</label>
                            <select name="bulan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tahun --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select name="tahun"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach(range(now()->year - 3, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== INFO ANAK ===== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">{{ $selectedAnak->nama }}</h3>
                        <p class="text-gray-500 text-sm">
                            NIS: {{ $selectedAnak->nis }} | Kelas: {{ $selectedAnak->kelas->nama_kelas }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ===== JURNAL HARIAN ===== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-6 border-b pb-2 text-blue-700 uppercase">
                        Jurnal Harian Kelas
                        <span class="text-sm font-normal text-gray-400 normal-case ml-1">
                            — {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                        </span>
                    </h3>

                    @if($jurnalHarian->isEmpty())
                        <div class="text-center py-10 text-gray-400">
                            <svg class="mx-auto w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm">Belum ada jurnal mengajar yang dicatat pada bulan ini.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($jurnalHarian as $tanggal => $jurnalPerTanggal)
                                <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                                    {{-- Header Tanggal --}}
                                    <div class="bg-blue-50 px-4 py-3 flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-bold text-blue-800 text-sm">
                                            {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>

                                    {{-- Daftar jurnal pada tanggal tersebut --}}
                                    <div class="divide-y divide-gray-50">
                                        @foreach($jurnalPerTanggal as $jurnal)
                                            <div class="p-4 bg-white hover:bg-gray-50 transition-colors">
                                                {{-- Baris atas: Materi + Jam + Guru --}}
                                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded uppercase">
                                                            {{ $jurnal->jadwal?->materi->nama_materi ?? '—' }}
                                                        </span>
                                                        @if($jurnal->jam)
                                                            <span class="text-xs text-gray-400">
                                                                ⏰ {{ $jurnal->jam }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-400 italic">
                                                        Guru: {{ $jurnal->guru->nama }}
                                                    </span>
                                                </div>

                                                {{-- Topik --}}
                                                <p class="text-sm font-semibold text-gray-800 mb-1">
                                                    📌 {{ $jurnal->topik }}
                                                </p>

                                                {{-- Metode --}}
                                                @if($jurnal->metode)
                                                    <p class="text-xs text-gray-500 mb-2">
                                                        <span class="font-medium text-gray-600">Metode:</span>
                                                        {{ $jurnal->metode }}
                                                    </p>
                                                @endif

                                                {{-- Ringkasan --}}
                                                <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-300 mt-2">
                                                    <p class="text-xs font-medium text-blue-700 mb-1 uppercase tracking-wide">Ringkasan Materi</p>
                                                    <p class="text-sm text-gray-700">{{ $jurnal->ringkasan }}</p>
                                                </div>

                                                {{-- Catatan (opsional) --}}
                                                @if($jurnal->catatan)
                                                    <div class="mt-2 bg-yellow-50 rounded-lg p-3 border-l-4 border-yellow-300">
                                                        <p class="text-xs font-medium text-yellow-700 mb-1 uppercase tracking-wide">Catatan Guru</p>
                                                        <p class="text-sm text-gray-700 italic">{{ $jurnal->catatan }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== RIWAYAT AKTIVITAS & PERKEMBANGAN ===== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-6 border-b pb-2 text-green-700 uppercase">
                        Riwayat Aktivitas &amp; Perkembangan
                        <span class="text-sm font-normal text-gray-400 normal-case ml-1">
                            — {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}
                        </span>
                    </h3>

                    <div class="relative border-l-2 border-gray-200 ml-3">
                        @php $lastDate = ''; @endphp
                        @forelse ($logBulanan as $log)
                            @php $currentDate = $log->tanggal->toDateString(); @endphp

                            <div class="mb-8 ml-6">
                                @if ($currentDate != $lastDate)
                                    <span class="absolute -left-[11px] w-5 h-5 bg-white border-4 border-green-500 rounded-full"></span>
                                    <h4 class="font-bold text-gray-800 text-lg">
                                        {{ $log->tanggal->translatedFormat('l, d F Y') }}
                                    </h4>
                                    @php $lastDate = $currentDate; @endphp
                                @endif

                                <div class="mt-3">
                                    @if ($log->tipe == 'presensi')
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex items-center">
                                            @php
                                                $statusClass = match($log->status) {
                                                    'hadir' => 'bg-green-100 text-green-700',
                                                    'sakit' => 'bg-yellow-100 text-yellow-700',
                                                    'izin'  => 'bg-blue-100 text-blue-700',
                                                    'alfa'  => 'bg-red-100 text-red-700',
                                                    default => 'bg-gray-100'
                                                };
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $statusClass }}">
                                                {{ $log->status }}
                                            </span>
                                            <span class="ml-4 text-sm text-gray-600 italic">{{ $log->detail }}</span>
                                        </div>

                                    @elseif ($log->tipe == 'perkembangan')
                                        <div class="mt-2 p-4 bg-white border-l-4 border-green-400 shadow-sm rounded-r-lg border-y border-r">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="text-xs font-bold text-green-700 uppercase">
                                                    Perkembangan Materi: {{ $log->materi }} ({{ $log->jam }})
                                                </div>
                                                @if($log->penilaian)
                                                    <span class="bg-yellow-100 text-yellow-800 text-[10px] px-2 py-0.5 rounded font-bold uppercase border border-yellow-200">
                                                        {{ $log->penilaian }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-gray-700 text-sm italic font-serif">"{{ $log->catatan }}"</p>
                                            <div class="mt-2 text-[10px] text-gray-400 text-right">— Dicatat oleh: {{ $log->guru }}</div>
                                        </div>

                                    @elseif ($log->tipe == 'jurnal')
                                        <div class="mt-2 p-4 bg-white border-l-4 border-blue-400 shadow-sm rounded-r-lg border-y border-r">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="text-xs font-bold text-blue-700 uppercase">
                                                    Jurnal Materi: {{ $log->materi }} ({{ $log->jam }})
                                                </div>
                                            </div>
                                            <p class="text-gray-800 text-sm font-semibold">{{ $log->topik }}</p>
                                            @if($log->ringkasan)
                                                <p class="text-gray-600 text-sm mt-1">{{ $log->ringkasan }}</p>
                                            @endif
                                            <div class="mt-2 text-[10px] text-gray-400 text-right">— Diajarkan oleh: {{ $log->guru }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-500">
                                <p>Belum ada aktivitas yang dicatat pada bulan ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>