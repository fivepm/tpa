<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Guru') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

                    <form method="POST" action="{{ route('pengurus.kelola-guru.update', $guru) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="nama" class="block font-medium text-sm text-gray-700">Nama</label>
                            <input id="nama" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="nama" value="{{ old('nama', $guru->nama) }}" required autofocus />
                        </div>
                        <div class="mt-4">
                            <label for="username" class="block font-medium text-sm text-gray-700">Username</label>
                            <input id="username" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="username" value="{{ old('username', $guru->username) }}" required />
                        </div>
                        <div class="mt-4">
                            <label for="no_hp" class="block font-medium text-sm text-gray-700">No. HP / WhatsApp <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input id="no_hp" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}" placeholder="Contoh: 08123456789" />
                            <small class="text-gray-400">Digunakan untuk menerima notifikasi WhatsApp.</small>
                        </div>
                        <div class="mt-4">
                            <label for="password" class="block font-medium text-sm text-gray-700">Password Baru (Opsional)</label>
                            <input id="password" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="password" name="password" autocomplete="new-password" />
                            <small class="text-gray-500">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                        <div class="mt-4">
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Konfirmasi Password Baru</label>
                            <input id="password_confirmation" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="password" name="password_confirmation" />
                        </div>
                        <div class="mt-4">
                            <label class="block font-medium text-sm text-gray-700">Kelas yang Diajar</label>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach ($daftarKelas as $kelas)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="kelas[]" value="{{ $kelas->id_kelas }}" 
                                            @if(in_array($kelas->id_kelas, old('kelas', $guru->kelas->pluck('id_kelas')->toArray()))) checked @endif
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2 text-sm text-gray-600">{{ $kelas->nama_kelas }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengurus.kelola-guru.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
