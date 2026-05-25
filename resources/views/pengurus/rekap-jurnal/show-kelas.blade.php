<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Jurnal Perkembangan: Kelas {{ $kelas->nama_kelas }}
                </h2>
                <p class="text-sm text-gray-500">
                    Bulan {{ \Carbon\Carbon::create(null, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
                </p>
            </div>
            <a href="{{ route('pengurus.rekap-jurnal.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Rekap
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @forelse ($rekapSiswa as $rekap)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        {{-- Header Siswa --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $rekap['siswa']->nama }}</h3>
                                <p class="text-sm text-gray-500">Total Jurnal: {{ $rekap['total'] }} entri</p>
                            </div>
                            {{-- Badge distribusi penilaian --}}
                            <div class="flex flex-wrap gap-2 mt-2 sm:mt-0">
                                @php
                                    $badgeConfig = [
                                        'Sangat Baik'     => ['cls' => 'bg-green-100 text-green-700',  'icon' => '⭐'],
                                        'Baik'            => ['cls' => 'bg-teal-100 text-teal-700',    'icon' => '✅'],
                                        'Cukup'           => ['cls' => 'bg-yellow-100 text-yellow-700','icon' => '⚠️'],
                                        'Perlu Bimbingan' => ['cls' => 'bg-red-100 text-red-700',      'icon' => '🔴'],
                                    ];
                                @endphp
                                @foreach ($badgeConfig as $label => $cfg)
                                    @if ($rekap['distribusi'][$label] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $cfg['cls'] }}">
                                            {{ $cfg['icon'] }} {{ $label }}: {{ $rekap['distribusi'][$label] }}
                                        </span>
                                    @endif
                                @endforeach
                                @if ($rekap['total'] == 0)
                                    <span class="text-xs text-gray-400 italic">Belum ada jurnal bulan ini</span>
                                @endif
                            </div>
                        </div>

                        {{-- Tabel Jurnal --}}
                        @if ($rekap['total'] > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase text-xs">Tanggal</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase text-xs">Materi</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase text-xs">Guru</th>
                                            <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase text-xs">Penilaian</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase text-xs">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($rekap['jurnal'] as $jurnal)
                                            @php
                                                $penilaianBadge = match($jurnal->penilaian) {
                                                    'Sangat Baik'     => 'bg-green-100 text-green-700',
                                                    'Baik'            => 'bg-teal-100 text-teal-700',
                                                    'Cukup'           => 'bg-yellow-100 text-yellow-700',
                                                    'Perlu Bimbingan' => 'bg-red-100 text-red-700',
                                                    default           => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    @if ($jurnal->jadwal && $jurnal->jadwal->materi)
                                                        {{ $jurnal->jadwal->materi->nama_materi }}
                                                        <span class="text-xs text-gray-400">
                                                            ({{ \Carbon\Carbon::parse($jurnal->jadwal->jam_mulai)->format('H:i') }}
                                                            – {{ \Carbon\Carbon::parse($jurnal->jadwal->jam_selesai)->format('H:i') }})
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 italic text-xs">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                                    {{ $jurnal->guru->nama ?? '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($jurnal->penilaian)
                                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $penilaianBadge }}">
                                                            {{ $jurnal->penilaian }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400 text-xs italic">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ $jurnal->catatan }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-gray-400 italic py-4">Belum ada catatan jurnal untuk siswa ini pada bulan yang dipilih.</p>
                        @endif

                    </div>
                </div>
            @empty
                <div class="bg-white p-8 shadow-sm sm:rounded-lg text-center text-gray-500">
                    Tidak ada siswa di kelas ini.
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
