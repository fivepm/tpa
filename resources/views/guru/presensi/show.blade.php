<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Formulir Presensi Harian: Kelas {{ $kelas->nama_kelas }}
            </h2>
            <a href="{{ route('guru.presensi.index', ['bulan' => \Carbon\Carbon::parse($tanggal)->format('m'), 'tahun' => \Carbon\Carbon::parse($tanggal)->format('Y')]) }}" class="text-sm text-gray-600">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b">
                    <p class="text-gray-500 text-sm">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('guru.presensi.store', $kelas) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <div class="space-y-4">
                            @forelse ($siswaDiKelas as $siswa)
                                <div class="border rounded-lg p-4 flex flex-col sm:flex-row justify-between sm:items-center">
                                    <div class="font-medium mb-2 sm:mb-0">{{ $siswa->nama }}</div>
                                    <div class="flex items-center space-x-4">
                                        @php $statusSiswa = $presensiHariIni[$siswa->id] ?? 'hadir'; @endphp
                                        <label class="inline-flex items-center"><input type="radio" name="presensi[{{ $siswa->id }}]" value="hadir" class="form-radio text-green-600" {{ $statusSiswa == 'hadir' ? 'checked' : '' }}><span class="ml-2">Hadir</span></label>
                                        <label class="inline-flex items-center"><input type="radio" name="presensi[{{ $siswa->id }}]" value="sakit" class="form-radio text-yellow-600" {{ $statusSiswa == 'sakit' ? 'checked' : '' }}><span class="ml-2">Sakit</span></label>
                                        <label class="inline-flex items-center"><input type="radio" name="presensi[{{ $siswa->id }}]" value="izin" class="form-radio text-blue-600" {{ $statusSiswa == 'izin' ? 'checked' : '' }}><span class="ml-2">Izin</span></label>
                                        <label class="inline-flex items-center"><input type="radio" name="presensi[{{ $siswa->id }}]" value="alfa" class="form-radio text-red-600" {{ $statusSiswa == 'alfa' ? 'checked' : '' }}><span class="ml-2">Alfa</span></label>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Tidak ada siswa di kelas ini.</p>
                            @endforelse
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Simpan Presensi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>