<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight italic">
                Ubah Perkembangan: {{ $jadwal->materi->nama_materi }}
            </h2>
            <a href="{{ route('guru.perkembangan.index', ['bulan' => \Carbon\Carbon::parse($tanggal)->format('m'), 'tahun' => \Carbon\Carbon::parse($tanggal)->format('Y')]) }}" class="text-sm text-gray-500 hover:text-gray-800 font-bold uppercase">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="mb-4 border-b pb-4">
                        <p class="text-gray-500 text-sm">Tanggal: <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span></p>
                    </div>
                    <div class="mb-8 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg">
                        <p class="text-sm text-yellow-700 font-bold italic">Mode Edit: Catatan yang sudah ada ditampilkan di bawah. Jika catatan dihapus (kosong), maka data untuk siswa tersebut akan terhapus dari laporan tanggal ini.</p>
                    </div>

                    <form action="{{ route('guru.perkembangan.update', $jadwal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <div class="space-y-8">
                            @foreach ($jadwal->kelas->siswa as $siswa)
                                @php $data = $catatanTersimpan[$siswa->id] ?? null; @endphp
                                <div class="bg-white border rounded-2xl p-6 shadow-sm border-yellow-100">
                                    <div class="flex items-center mb-4 border-b pb-2">
                                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold mr-3">{{ substr($siswa->nama, 0, 1) }}</div>
                                        <h4 class="text-lg font-bold text-gray-800">{{ $siswa->nama }}</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                        <div class="md:col-span-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Penilaian</label>
                                            <select name="penilaian[{{ $siswa->id }}]" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($penilaianOptions as $option)
                                                    <option value="{{ $option }}" {{ ($data && $data->penilaian == $option) ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Catatan Perkembangan</label>
                                            <textarea name="catatan[{{ $siswa->id }}]" rows="2" class="w-full rounded-xl border-gray-200 focus:border-yellow-500 focus:ring-yellow-500">{{ $data->catatan ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end mt-10">
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold uppercase text-sm hover:bg-blue-700 transition shadow-lg">
                                Perbarui Semua Catatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>