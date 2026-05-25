<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Siswa Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pengurus.kelola-siswa.store') }}">
                        @csrf
                        <div>
                            <label for="nis" class="block font-medium text-sm text-gray-700">NIS (Nomor Induk Siswa)</label>
                            <input id="nis" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="nis" value="{{ old('nis') }}" required autofocus />
                        </div>
                        <div class="mt-4">
                            <label for="nama" class="block font-medium text-sm text-gray-700">Nama Lengkap Siswa</label>
                            <input id="nama" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="nama" value="{{ old('nama') }}" required />
                        </div>
                        <div class="mt-4">
                            <label for="kelas_id" class="block font-medium text-sm text-gray-700">Kelas</label>
                            <select name="kelas_id" id="kelas_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id_kelas }}" {{ old('kelas_id') == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <label for="orangtua_id" class="block font-medium text-sm text-gray-700">Orang Tua</label>
                            <select name="orangtua_id" id="orangtua_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                <option value="">Pilih Orang Tua</option>
                                @foreach ($orangtua as $ortu)
                                    <option value="{{ $ortu->id }}" {{ old('orangtua_id') == $ortu->id ? 'selected' : '' }}>{{ $ortu->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengurus.kelola-siswa.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
