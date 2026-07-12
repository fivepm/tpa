<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Laporan Lengkap: {{ $siswa->nama }}
            </h2>
            <p class="text-sm text-gray-500">
                Kelas {{ $siswa->kelas->nama_kelas }} | Bulan {{ \Carbon\Carbon::create(null, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
            </p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('pengurus.laporan.showKelas', ['kelas' => $siswa->kelas_id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Laporan Kelas
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Log Kehadiran & Perkembangan</h3>
                    <div class="border-l-2 border-gray-200 ml-2">
                        @php $lastDate = ''; @endphp
                        @forelse ($logBulanan as $log)
                            @php $currentDate = $log->tanggal->toDateString(); @endphp
                            
                            <div class="relative mb-6">
                                @if ($currentDate != $lastDate)
                                    <div class="absolute -left-[11px] top-1 w-5 h-5 rounded-full {{ $log->tipe == 'presensi' ? 'bg-blue-500' : 'bg-green-500' }}"></div>
                                    <div class="ml-8 mb-4">
                                        <p class="font-bold text-lg text-gray-800">{{ $log->tanggal->translatedFormat('l, d F Y') }}</p>
                                    </div>
                                    @php $lastDate = $currentDate; @endphp
                                @endif

                                <div class="ml-8">
                                    @if ($log->tipe == 'presensi')
                                        <div class="flex items-center">
                                            @php
                                                $color = 'bg-gray-200 text-gray-800';
                                                if ($log->status == 'hadir') $color = 'bg-green-100 text-green-800';
                                                if ($log->status == 'sakit') $color = 'bg-yellow-100 text-yellow-800';
                                                if ($log->status == 'izin') $color = 'bg-blue-100 text-blue-800';
                                                if ($log->status == 'alfa') $color = 'bg-red-100 text-red-800';
                                            @endphp
                                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $color }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                            <span class="ml-3 text-sm text-gray-600">{{ $log->detail }}</span>
                                        </div>
                                    @endif

                                    @if ($log->tipe == 'perkembangan')
                                        <div class="mt-2 p-4 bg-gray-50 rounded-lg border ml-6 border-l-4 border-green-400">
                                            @if($log->penilaian)
                                                <span class="text-xs font-medium px-2.5 py-0.5 rounded bg-yellow-100 text-yellow-800 mb-2 inline-block">{{ $log->penilaian }}</span>
                                            @endif
                                            <p class="text-gray-700 italic">"{{ $log->detail }}"</p>
                                            <p class="text-right text-xs text-gray-500 mt-2">- {{ $log->guru }} ({{ $log->materi ?? 'Umum' }} - {{ $log->jam ?? '' }})</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="ml-8 text-center text-gray-500 py-4">
                                Tidak ada data kehadiran atau perkembangan di bulan ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

