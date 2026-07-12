<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight italic">
            Input Perkembangan: {{ $jadwal->materi->nama_materi }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('guru.perkembangan.index', ['bulan' => \Carbon\Carbon::parse($tanggal)->format('m'), 'tahun' => \Carbon\Carbon::parse($tanggal)->format('Y')]) }}"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-gray-900 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Kembali ke Daftar Perkembangan
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="mb-4 border-b pb-4">
                        <p class="text-gray-500 text-sm">Tanggal: <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span></p>
                    </div>
                    <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                        <p class="text-sm text-blue-700">Silakan isi catatan perkembangan untuk setiap siswa yang mengikuti materi ini pada tanggal tersebut. Anda bisa mengosongkan jika siswa tidak masuk atau tidak ada perkembangan khusus.</p>
                    </div>

                    <form action="{{ route('guru.perkembangan.store', $jadwal) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <div class="space-y-8">
                            @foreach ($jadwal->kelas->siswa as $siswa)
                                <div class="bg-white border rounded-2xl p-6 shadow-sm">
                                    <div class="flex items-center mb-4 border-b pb-2">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold mr-3">{{ substr($siswa->nama, 0, 1) }}</div>
                                        <h4 class="text-lg font-bold text-gray-800">{{ $siswa->nama }}</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                        <div class="md:col-span-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Penilaian</label>
                                            <select name="penilaian[{{ $siswa->id }}]" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($penilaianOptions as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Catatan Perkembangan</label>
                                            <textarea name="catatan[{{ $siswa->id }}]" rows="2" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Sangat aktif bertanya, Hafalan surat An-Naba lancar..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end mt-10">
                            <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-xl font-bold uppercase text-sm hover:bg-green-700 transition shadow-lg shadow-green-100">
                                Simpan Semua Catatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>