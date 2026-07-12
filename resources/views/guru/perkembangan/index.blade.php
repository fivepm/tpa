<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Perkembangan Siswa') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <form method="GET" action="{{ route('guru.perkembangan.index') }}" class="flex items-center gap-4">
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
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-700">Jadwal Mengajar</h3>
                                    <p class="text-sm text-gray-500 italic">{{ $data['namaHari'] }}, {{ \Carbon\Carbon::parse($tanggalStr)->translatedFormat('d F Y') }}</p>
                                </div>
                                @if (\Carbon\Carbon::parse($tanggalStr)->isToday())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hari Ini</span>
                                @endif
                            </div>

                            <div class="p-6">
                                @if ($data['hariLibur'])
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-center">
                                        <p class="text-yellow-700 font-bold uppercase tracking-widest">Hari Libur: {{ $data['hariLibur']->keterangan }}</p>
                                    </div>
                                @elseif ($data['jadwal']->isEmpty())
                                    <div class="text-center py-10">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p class="mt-2 text-gray-500">Tidak ada jadwal mengajar pada hari ini.</p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 gap-4">
                                        @foreach ($data['jadwal'] as $jadwal)
                                            <div class="flex flex-col md:flex-row justify-between items-center p-5 border rounded-xl hover:shadow-md transition duration-200 {{ $jadwal->sudah_ada_catatan ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                                                    <div class="bg-white p-3 rounded-lg shadow-sm text-center min-w-[80px]">
                                                        <span class="block text-xs font-bold text-gray-400 uppercase">Jam</span>
                                                        <span class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-lg font-bold text-gray-800">{{ $jadwal->materi->nama_materi }}</h4>
                                                        <p class="text-sm text-gray-600">Kelas: <span class="font-semibold">{{ $jadwal->kelas->nama_kelas }}</span></p>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    @if ($jadwal->sudah_ada_catatan)
                                                        <a href="{{ route('guru.perkembangan.edit', ['jadwal' => $jadwal->id, 'tanggal' => $tanggalStr]) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                            Ubah Perkembangan
                                                        </a>
                                                    @else
                                                        <a href="{{ route('guru.perkembangan.create', ['jadwal' => $jadwal->id, 'tanggal' => $tanggalStr]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Input Perkembangan
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