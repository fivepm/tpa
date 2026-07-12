<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📔 Jurnal Mengajar
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-300 text-green-700 px-5 py-3 rounded-xl shadow-sm">
                    <span class="text-2xl">✅</span>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('info'))
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-300 text-blue-700 px-5 py-3 rounded-xl shadow-sm">
                    <span class="text-2xl">ℹ️</span>
                    <span class="font-medium">{{ session('info') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <form method="GET" action="{{ route('guru.jurnal.index') }}" class="flex items-center gap-4">
                        <div>
                            <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                            <select name="bulan" id="bulan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->locale('id')->monthName }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                            <select name="tahun" id="tahun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if (empty($jadwalPerTanggal))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        Tidak ada jadwal untuk bulan dan tahun yang dipilih.
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($jadwalPerTanggal as $tanggalStr => $data)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-6 border-b pb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">Jadwal Mengajar</h3>
                                        <p class="text-sm text-gray-500 italic mt-0.5">
                                            {{ $data['namaHari'] }}, {{ \Carbon\Carbon::parse($tanggalStr)->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                    @if (\Carbon\Carbon::parse($tanggalStr)->isToday())
                                        <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 text-xs font-semibold rounded-full border border-green-200">
                                            📅 Hari Ini
                                        </span>
                                    @endif
                                </div>

                                @if ($data['hariLibur'])
                                    <div class="flex items-center gap-3 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                                        <span class="text-2xl">🏖️</span>
                                        <div>
                                            <p class="font-bold text-yellow-700 uppercase tracking-wide text-sm">Hari Libur</p>
                                            <p class="text-yellow-600 text-sm">{{ $data['hariLibur']->keterangan }}</p>
                                        </div>
                                    </div>
                                @elseif ($data['jadwal']->isEmpty())
                                    <div class="text-center py-6 sm:py-12">
                                        <div class="text-6xl mb-3">📭</div>
                                        <p class="text-gray-400 text-sm">Tidak ada jadwal mengajar pada tanggal ini.</p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach ($data['jadwal'] as $jadwal)
                                            <div class="flex flex-col md:flex-row justify-between items-center p-5 border rounded-xl transition duration-200
                                                {{ $jadwal->sudah_ada_jurnal ? 'bg-green-50 border-green-200 shadow-sm' : 'bg-white border-gray-200 hover:shadow-md hover:border-indigo-200' }}">

                                                <div class="flex items-center gap-4 mb-4 md:mb-0">
                                                    {{-- Waktu --}}
                                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center min-w-[70px]">
                                                        <span class="block text-xs font-bold text-gray-400 uppercase">Jam</span>
                                                        <span class="text-sm font-bold text-gray-700">
                                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                        </span>
                                                    </div>

                                                    <div>
                                                        <h4 class="text-base font-bold text-gray-800">{{ $jadwal->materi->nama_materi }}</h4>
                                                        <p class="text-sm text-gray-500">
                                                            Kelas: <span class="font-semibold text-gray-700">{{ $jadwal->kelas->nama_kelas }}</span>
                                                        </p>
                                                        @if ($jadwal->sudah_ada_jurnal)
                                                            <span class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">
                                                                ✓ Sudah dijurnal
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-orange-500 bg-orange-50 px-2 py-0.5 rounded-full">
                                                                ⚪ Belum dijurnal
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div>
                                                    @if ($jadwal->sudah_ada_jurnal)
                                                        <a href="{{ route('guru.jurnal.edit', ['jadwal' => $jadwal->id, 'tanggal' => $tanggalStr]) }}"
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg uppercase tracking-wide transition shadow-sm">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Ubah Jurnal
                                                        </a>
                                                    @else
                                                        <a href="{{ route('guru.jurnal.create', ['jadwal' => $jadwal->id, 'tanggal' => $tanggalStr]) }}"
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg uppercase tracking-wide transition shadow-md shadow-indigo-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                            </svg>
                                                            Catat Jurnal
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Riwayat Jurnal --}}
            @if ($riwayatJurnal->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b">🗂️ Riwayat Jurnal Terbaru</h3>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                        <th class="px-4 py-3 font-semibold rounded-l-lg">Tanggal</th>
                                        <th class="px-4 py-3 font-semibold">Kelas</th>
                                        <th class="px-4 py-3 font-semibold">Materi</th>
                                        <th class="px-4 py-3 font-semibold">Topik</th>
                                        <th class="px-4 py-3 font-semibold">Metode</th>
                                        <th class="px-4 py-3 font-semibold rounded-r-lg">Ringkasan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($riwayatJurnal as $jurnalItem)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-medium text-gray-700 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($jurnalItem->tanggal)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $jurnalItem->jadwal->kelas->nama_kelas ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $jurnalItem->jadwal->materi->nama_materi ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-indigo-700">{{ $jurnalItem->topik }}</td>
                                            <td class="px-4 py-3">
                                                @if ($jurnalItem->metode)
                                                    <span class="inline-flex px-2 py-0.5 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full border border-blue-200">
                                                        {{ $jurnalItem->metode }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-xs">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 max-w-xs">
                                                <p class="truncate" title="{{ $jurnalItem->ringkasan }}">{{ $jurnalItem->ringkasan }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
