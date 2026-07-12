<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Presensi Harian
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <form method="GET" action="{{ route('guru.presensi.index') }}" class="flex items-center gap-4">
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
                        Tidak ada jadwal presensi untuk bulan dan tahun yang dipilih.
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($jadwalPerTanggal as $tanggalStr => $data)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-800">
                                    {{ $data['namaHari'] }}, {{ \Carbon\Carbon::parse($tanggalStr)->translatedFormat('d F Y') }}
                                </h3>
                                @if (\Carbon\Carbon::parse($tanggalStr)->isToday())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hari Ini</span>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                @if ($data['hariLibur'])
                                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                        <h3 class="text-md font-medium text-yellow-800">Hari Libur: {{ $data['hariLibur']->keterangan }}</h3>
                                    </div>
                                @elseif ($data['kelas']->isEmpty())
                                    <p class="text-center text-gray-500 text-sm">Tidak ada jadwal kelas.</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach ($data['kelas'] as $kelas)
                                            <div class="border rounded-lg p-4 flex justify-between items-center {{ $kelas->presensi_sudah_diambil ? 'bg-gray-50' : '' }}">
                                                <div>
                                                    <div class="font-bold text-lg text-green-700">{{ $kelas->nama_kelas }}</div>
                                                    <p class="text-xs text-gray-500">Ambil presensi untuk semua siswa di kelas ini.</p>
                                                    @if ($kelas->presensi_sudah_diambil)
                                                        <span class="inline-flex mt-1 text-xs font-semibold text-green-600">✓ Sudah diambil</span>
                                                    @else
                                                        <span class="inline-flex mt-1 text-xs font-semibold text-red-500">! Belum diambil</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if ($kelas->presensi_sudah_diambil)
                                                        <a href="{{ route('guru.presensi.show', ['kelas' => $kelas->id_kelas, 'tanggal' => $tanggalStr]) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600">
                                                            Ubah
                                                        </a>
                                                    @else
                                                        <a href="{{ route('guru.presensi.show', ['kelas' => $kelas->id_kelas, 'tanggal' => $tanggalStr]) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700">
                                                            Ambil
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

        </div>
    </div>
</x-app-layout>
