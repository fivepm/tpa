<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rekapitulasi Jurnal Perkembangan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Bulan & Tahun --}}
            <div class="bg-white p-4 shadow sm:rounded-lg">
                <form action="{{ route('pengurus.rekap-jurnal.index') }}" method="GET"
                    class="flex flex-col sm:flex-row sm:items-end sm:space-x-4">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="bulan" id="bulan"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                        <select name="tahun" id="tahun"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                            @foreach (range(now()->year, now()->year - 5) as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="mt-4 sm:mt-0 w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Tampilkan
                    </button>
                </form>
            </div>

            {{-- Header Info --}}
            <div class="bg-indigo-600 text-white p-6 shadow-lg sm:rounded-lg text-center">
                <h3 class="text-lg font-medium">Rekapitulasi Jurnal Perkembangan Siswa</h3>
                <p class="text-sm opacity-80 mt-1">
                    Bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                </p>
            </div>

            {{-- Daftar Kelas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rekap per Kelas</h3>
                    <div class="space-y-4">
                        @forelse ($rekapKelas as $rekap)
                            <div class="border rounded-lg p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">

                                    {{-- Nama & Jumlah Siswa --}}
                                    <div class="sm:col-span-3">
                                        <h4 class="font-bold text-lg text-gray-800">{{ $rekap['kelas']->nama_kelas }}</h4>
                                        <p class="text-sm text-gray-500">{{ $rekap['total_siswa'] }} Siswa</p>
                                    </div>

                                    {{-- Statistik Jurnal --}}
                                    <div class="sm:col-span-5">
                                        <div class="flex flex-wrap gap-2 text-xs">
                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 font-medium">
                                                📝 {{ $rekap['total_jurnal'] }} Entri Jurnal
                                            </span>
                                            <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">
                                                👤 {{ $rekap['siswa_ada_jurnal'] }} Siswa Tercatat
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @php
                                                $penilaianConfig = [
                                                    'Sangat Baik'     => 'bg-green-100 text-green-700',
                                                    'Baik'            => 'bg-teal-100 text-teal-700',
                                                    'Cukup'           => 'bg-yellow-100 text-yellow-700',
                                                    'Perlu Bimbingan' => 'bg-red-100 text-red-700',
                                                ];
                                            @endphp
                                            @foreach ($penilaianConfig as $label => $cls)
                                                @if (($rekap['distribusi_penilaian'][$label] ?? 0) > 0)
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cls }}">
                                                        {{ $label }}: {{ $rekap['distribusi_penilaian'][$label] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                            @if ($rekap['total_jurnal'] == 0)
                                                <span class="text-xs text-gray-400 italic">Belum ada jurnal bulan ini</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tombol Detail --}}
                                    <div class="sm:col-span-4 sm:text-right">
                                        <a href="{{ route('pengurus.rekap-jurnal.show-kelas', ['kelas' => $rekap['kelas']->id_kelas, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition duration-150 ease-in-out w-full sm:w-auto">
                                            Lihat Detail
                                            <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                            </svg>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Tidak ada data kelas untuk ditampilkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
