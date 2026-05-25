<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Wali Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </button>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-800">Daftar Wali Kelas</h3>
                        <a href="{{ route('pengurus.kelola-walikelas.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                            + Tambah Wali Kelas
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">No</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nama Kelas</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Wali Kelas</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($dataKelas as $kelas)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-4 font-medium">{{ $kelas->nama_kelas }}</td>
                                        <td class="py-3 px-4">
                                            @if ($kelas->waliKelas)
                                                <div class="flex items-center gap-2">
                                                    <span class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold">
                                                        {{ strtoupper(substr($kelas->waliKelas->guru->nama, 0, 1)) }}
                                                    </span>
                                                    <span>{{ $kelas->waliKelas->guru->nama }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic text-sm">— Belum ada wali kelas —</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($kelas->waliKelas)
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Sudah Ditugaskan</span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">Belum Ditugaskan</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($kelas->waliKelas)
                                                <form action="{{ route('pengurus.kelola-walikelas.destroy', $kelas->waliKelas->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Hapus wali kelas {{ $kelas->nama_kelas }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('pengurus.kelola-walikelas.create') }}"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Tugaskan
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-400 italic">Tidak ada data kelas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
