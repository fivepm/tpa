<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jadwal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900"
                    x-data="{ 
                        selectedKelas: '{{ old('kelas_id', $jadwal->kelas_id) }}', 
                        selectedGuru: '{{ old('guru_id', $jadwal->guru_id) }}',
                        guruList: [],
                        fetchGuru() {
                            if (!this.selectedKelas) {
                                this.guruList = [];
                                return;
                            }
                            fetch(`/pengurus/api/kelas/${this.selectedKelas}/guru`)
                                .then(response => response.json())
                                .then(data => {
                                    this.guruList = data;
                                });
                        }
                    }"
                    x-init="fetchGuru()">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('pengurus.kelola-jadwal.update', $jadwal) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="hari" class="block font-medium text-sm text-gray-700">Hari</label>
                                <select name="hari" id="hari" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    @foreach ($hari as $h)
                                        <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>{{ $h }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="kelas_id" class="block font-medium text-sm text-gray-700">Kelas</label>
                                <select name="kelas_id" id="kelas_id" x-model="selectedKelas" @change="fetchGuru()" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id_kelas }}" {{ $jadwal->kelas_id == $k->id_kelas ? 'selected' : ''}}>{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="materi_id" class="block font-medium text-sm text-gray-700">Materi</label>
                                <select name="materi_id" id="materi_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    @foreach ($materi as $m)
                                        <option value="{{ $m->id }}" {{ old('materi_id', $jadwal->materi_id) == $m->id ? 'selected' : '' }}>{{ $m->nama_materi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="guru_id" class="block font-medium text-sm text-gray-700">Guru</label>
                                <select name="guru_id" id="guru_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    <template x-for="guru in guruList" :key="guru.id">
                                        <option :value="guru.id" :selected="guru.id == selectedGuru" x-text="guru.nama"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label for="jam_mulai" class="block font-medium text-sm text-gray-700">Jam Mulai</label>
                                <input id="jam_mulai" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="time" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" required />
                            </div>
                            <div>
                                <label for="jam_selesai" class="block font-medium text-sm text-gray-700">Jam Selesai</label>
                                <input id="jam_selesai" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="time" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" required />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengurus.kelola-jadwal.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

