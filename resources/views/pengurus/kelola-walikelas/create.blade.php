<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Wali Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($kelasTersedia->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-lg font-medium">Semua kelas sudah memiliki wali kelas. 🎉</p>
                            <a href="{{ route('pengurus.kelola-walikelas.index') }}"
                                class="mt-4 inline-block text-sm text-blue-600 hover:underline">
                                &larr; Kembali ke Daftar Wali Kelas
                            </a>
                        </div>
                    @else
                        <form action="{{ route('pengurus.kelola-walikelas.store') }}" method="POST" class="space-y-6">
                            @csrf

                            {{-- Pilih Kelas --}}
                            <div>
                                <label for="kelas_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kelas <span class="text-red-500">*</span>
                                </label>
                                <select name="kelas_id" id="kelas_id"
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md @error('kelas_id') border-red-500 @enderror"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelasTersedia as $kelas)
                                        <option value="{{ $kelas->id_kelas }}" {{ old('kelas_id') == $kelas->id_kelas ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Hanya kelas yang belum memiliki wali kelas yang ditampilkan.</p>
                            </div>

                            {{-- Pilih Guru (dinamis berdasarkan kelas) --}}
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Wali Kelas (Guru) <span class="text-red-500">*</span>
                                </label>
                                <select name="user_id" id="user_id"
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md @error('user_id') border-red-500 @enderror"
                                    required disabled>
                                    <option value="">-- Pilih kelas terlebih dahulu --</option>
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Hanya guru yang <strong>mengajar di kelas ini</strong> yang dapat ditugaskan sebagai wali kelas.
                                </p>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="flex items-center justify-between pt-2">
                                <a href="{{ route('pengurus.kelola-walikelas.index') }}"
                                    class="text-sm text-gray-600 hover:text-gray-900">
                                    &larr; Kembali
                                </a>
                                <button type="submit" id="btn-simpan" disabled
                                    class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        const kelasSelect = document.getElementById('kelas_id');
        const guruSelect  = document.getElementById('user_id');
        const btnSimpan   = document.getElementById('btn-simpan');

        // Jika ada old('kelas_id') saat validation error, muat ulang guru
        const oldKelas = "{{ old('kelas_id') }}";
        const oldGuru  = "{{ old('user_id') }}";

        async function loadGuru(kelasId) {
            guruSelect.disabled = true;
            guruSelect.innerHTML = '<option value="">Memuat data guru...</option>';
            btnSimpan.disabled = true;

            if (!kelasId) {
                guruSelect.innerHTML = '<option value="">-- Pilih kelas terlebih dahulu --</option>';
                return;
            }

            try {
                const url = `{{ url('pengurus/api/walikelas/kelas') }}/${kelasId}/guru`;
                const res  = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();

                if (data.length === 0) {
                    guruSelect.innerHTML = '<option value="">Tidak ada guru yang mengajar di kelas ini</option>';
                } else {
                    guruSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
                    data.forEach(guru => {
                        const opt = document.createElement('option');
                        opt.value = guru.id;
                        opt.textContent = guru.nama;
                        if (String(guru.id) === String(oldGuru)) opt.selected = true;
                        guruSelect.appendChild(opt);
                    });
                    guruSelect.disabled = false;
                    btnSimpan.disabled  = false;
                }
            } catch (e) {
                guruSelect.innerHTML = '<option value="">Gagal memuat data guru</option>';
            }
        }

        kelasSelect.addEventListener('change', () => loadGuru(kelasSelect.value));

        // Auto-load saat ada old value (setelah validation error)
        if (oldKelas) loadGuru(oldKelas);
    </script>
</x-app-layout>
