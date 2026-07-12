<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hari Libur') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('pengurus.kelola-harilibur.update', $hariLibur) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tanggal" class="block font-medium text-sm text-gray-700">Tanggal</label>
                                <input id="tanggal" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="date" name="tanggal" value="{{ old('tanggal', $hariLibur->tanggal) }}" required />
                            </div>
                            <div>
                                <label for="keterangan" class="block font-medium text-sm text-gray-700">Keterangan</label>
                                <input id="keterangan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="keterangan" value="{{ old('keterangan', $hariLibur->keterangan) }}" required autofocus />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengurus.kelola-harilibur.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>