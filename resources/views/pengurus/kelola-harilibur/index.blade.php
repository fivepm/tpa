<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Hari Libur') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('pengurus.kelola-harilibur.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            + Tambah Hari Libur
                        </a>
                    </div>

                    {{-- MOBILE: Card List --}}
                    <div class="block md:hidden space-y-3">
                        @forelse ($dataHariLibur as $item)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $item->keterangan }}</p>
                                        <p class="text-sm text-gray-500 mt-0.5">📅 {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 ml-3">
                                        <a href="{{ route('pengurus.kelola-harilibur.edit', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">Edit</a>
                                        <form action="{{ route('pengurus.kelola-harilibur.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-4">Data hari libur tidak ditemukan.</p>
                        @endforelse
                    </div>

                    {{-- DESKTOP: Tabel --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Tanggal</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Keterangan</th>
                                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($dataHariLibur as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                                        <td class="py-3 px-4">{{ $item->keterangan }}</td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('pengurus.kelola-harilibur.edit', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">Edit</a>
                                                <form action="{{ route('pengurus.kelola-harilibur.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4 text-gray-500">Data hari libur tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $dataHariLibur->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
