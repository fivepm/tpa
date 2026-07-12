<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-4 shadow sm:rounded-lg">
                <form action="{{ route('pengurus.laporan.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:space-x-4">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="bulan" id="bulan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                        <select name="tahun" id="tahun" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                            @foreach (range(now()->year, now()->year - 5) as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="mt-4 sm:mt-0 w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Tampilkan
                    </button>
                </form>
            </div>

            <div class="bg-green-600 text-white p-6 shadow-lg sm:rounded-lg text-center">
                <h3 class="text-lg font-medium">Total Persentase Kehadiran Seluruh Kelas</h3>
                <p class="text-5xl font-bold mt-2">{{ $persentaseSekolah }}%</p>
                <p class="text-sm opacity-80 mt-1">untuk bulan {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Rekapitulasi per Kelas</h3>
                    <div class="space-y-4">
                        @forelse ($rekapKelas as $rekap)
                            <div class="border rounded-lg p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center p-4 bg-white border-b border-gray-100 last:border-0">
                                    <div class="sm:col-span-4">
                                        <h4 class="font-bold text-xl text-gray-800">{{ $rekap['kelas']->nama_kelas }}</h4>
                                        <p class="text-sm text-gray-500">{{ $rekap['total_siswa'] }} Siswa</p>
                                    </div>
                                    <div class="sm:col-span-5">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-medium text-green-700">Kehadiran</span>
                                            <span class="text-sm font-medium text-green-700">{{ $rekap['persentase_kehadiran'] }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-green-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $rekap['persentase_kehadiran'] }}%"></div>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-3 sm:text-right">
                                        <a href="{{ route('pengurus.laporan.showKelas', ['kelas' => $rekap['kelas']->id_kelas, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 transition duration-150 ease-in-out w-full sm:w-auto">
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
