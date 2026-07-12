<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📔 Catat Jurnal Mengajar
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Info Jadwal --}}
            <div class="bg-indigo-600 text-white rounded-xl p-5 mb-6 shadow-lg shadow-indigo-200 flex items-center gap-4">
                <div class="bg-white/20 rounded-xl p-3 text-center min-w-[70px]">
                    <span class="block text-xs font-bold uppercase opacity-75">Jam</span>
                    <span class="text-base font-bold">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase opacity-75 mb-0.5">Jadwal Mengajar</p>
                    <h3 class="text-lg font-bold">{{ $jadwal->materi->nama_materi }}</h3>
                    <p class="text-sm opacity-90">Kelas: <span class="font-semibold">{{ $jadwal->kelas->nama_kelas }}</span>
                        &bull; {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>

            {{-- Form --}}
            <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700">Form Jurnal Mengajar</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Isi ringkasan materi yang Anda ajarkan pada tanggal tersebut.</p>
                </div>

                <form action="{{ route('guru.jurnal.store', $jadwal) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                    {{-- Topik --}}
                    <div>
                        <label for="topik" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Topik / Sub-Materi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="topik" id="topik"
                               value="{{ old('topik') }}"
                               placeholder="Contoh: Surat Al-Fatihah, Huruf Hijaiyah Alif-Ba, dsb."
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition
                                      @error('topik') border-red-400 @enderror">
                        @error('topik')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Metode --}}
                    <div>
                        <label for="metode" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Metode Mengajar
                            <span class="font-normal text-gray-400 text-xs ml-1">(opsional)</span>
                        </label>
                        <select name="metode" id="metode"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition
                                       @error('metode') border-red-400 @enderror">
                            <option value="">-- Pilih Metode --</option>
                            @foreach ($metodeOptions as $metode)
                                <option value="{{ $metode }}" {{ old('metode') == $metode ? 'selected' : '' }}>
                                    {{ $metode }}
                                </option>
                            @endforeach
                        </select>
                        @error('metode')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ringkasan --}}
                    <div>
                        <label for="ringkasan" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Ringkasan Materi yang Diajarkan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="ringkasan" id="ringkasan" rows="5"
                                  placeholder="Tuliskan apa yang Anda ajarkan pada tanggal tersebut secara singkat..."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition
                                         @error('ringkasan') border-red-400 @enderror">{{ old('ringkasan') }}</textarea>
                        @error('ringkasan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Catatan Tambahan / Kendala
                            <span class="font-normal text-gray-400 text-xs ml-1">(opsional)</span>
                        </label>
                        <textarea name="catatan" id="catatan" rows="3"
                                  placeholder="Misalnya: ada beberapa siswa yang belum hafal, dsb."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition
                                         @error('catatan') border-red-400 @enderror">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <a href="{{ route('guru.jurnal.index', ['bulan' => \Carbon\Carbon::parse($tanggal)->format('m'), 'tahun' => \Carbon\Carbon::parse($tanggal)->format('Y')]) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Kembali
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-md shadow-indigo-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Jurnal
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
