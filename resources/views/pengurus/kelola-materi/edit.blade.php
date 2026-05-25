<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Materi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <form method="POST" action="{{ route('pengurus.kelola-materi.update', $materi) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="nama_materi" class="block font-medium text-sm text-gray-700">Nama Materi</label>
                            <input id="nama_materi" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="nama_materi" value="{{ old('nama_materi', $materi->nama_materi) }}" required autofocus />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('pengurus.kelola-materi.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
