<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Laporan Detail: Kelas {{ $kelas->nama_kelas }}
            </h2>
            <p class="text-sm text-gray-500">
                Bulan {{ \Carbon\Carbon::create(null, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
            </p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('pengurus.laporan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Rekap Laporan
                </a>
                <a href="{{ route('pengurus.laporan.exportKelasPdf', ['kelas' => $kelas->id_kelas, 'bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-red-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export PDF
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Ringkasan Kehadiran Siswa</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Siswa</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Hadir</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Sakit/Izin/Alfa</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Persentase</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($rekapSiswa as $rekap)
                                    <tr class="border-b">
                                        <td class="py-3 px-4 font-medium">
                                            <a href="{{ route('pengurus.laporan.showSiswa', ['siswa' => $rekap['siswa']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="text-blue-600 hover:underline">
                                                {{ $rekap['siswa']->nama }}
                                            </a>
                                        </td>
                                        <td class="text-center py-3 px-4">{{ $rekap['hadir'] }}</td>
                                        <td class="text-center py-3 px-4">{{ $rekap['sakit'] }}/{{ $rekap['izin'] }}/{{ $rekap['alfa'] }}</td>
                                        <td class="text-center py-3 px-4 font-bold">{{ $rekap['persentase'] }}%</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('pengurus.laporan.showSiswa', ['siswa' => $rekap['siswa']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4">Tidak ada data siswa di kelas ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rekapitulasi Kehadiran Harian</h3>
                    <div class="overflow-x-auto border">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white sticky top-0 z-10">
                                <tr>
                                    <th class="py-2 px-3 uppercase font-semibold text-xs text-left border-r">Nama Siswa</th>
                                    @foreach ($daftarHari as $hari)
                                        <th class="py-2 px-2 uppercase font-semibold text-xs text-center min-w-[60px] border-r">
                                            <div>{{ $hari->translatedFormat('d') }}</div>
                                            <div class="font-normal">{{ $hari->translatedFormat('D') }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($rekapSiswa as $rekap)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="py-2 px-3 font-medium border-r">{{ $rekap['siswa']->nama }}</td>
                                        @foreach ($daftarHari as $hari)
                                            <td class="text-center py-2 px-2 border-r">
                                                @php
                                                    $status = $rekap['riwayat_harian'][$hari->toDateString()] ?? 'kosong';
                                                    $char = 'N/A';
                                                    $color = 'bg-gray-200 text-gray-700';
                                                    if ($status == 'hadir') { $color = 'bg-green-500 text-white'; $char = 'H'; }
                                                    if ($status == 'sakit') { $color = 'bg-yellow-500 text-white'; $char = 'S'; }
                                                    if ($status == 'izin') { $color = 'bg-blue-500 text-white'; $char = 'I'; }
                                                    if ($status == 'alfa') { $color = 'bg-red-500 text-white'; $char = 'A'; }
                                                @endphp
                                                <span class="text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full mx-auto {{ $color }}">
                                                    {{ $char }}
                                                </span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ 1 + count($daftarHari) }}" class="text-center py-4">Tidak ada sesi pelajaran di bulan ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

