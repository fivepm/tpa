<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-10 flex flex-col items-center text-center">

                    {{-- Ikon ilustrasi --}}
                    <div class="p-5 bg-yellow-100 rounded-full text-yellow-500 mb-6">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        </svg>
                    </div>

                    {{-- Judul --}}
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">
                        Akun Belum Dikaitkan dengan Data Siswa
                    </h3>

                    {{-- Deskripsi --}}
                    <p class="text-gray-500 text-base max-w-md mb-2">
                        Akun Anda saat ini belum terhubung dengan data siswa manapun.
                        Untuk dapat melihat informasi perkembangan dan aktivitas anak, silahkan hubungi
                        <span class="font-semibold text-green-700">Pengurus TPA</span>
                        agar akun Anda dapat dikaitkan dengan data siswa yang bersangkutan.
                    </p>

                    {{-- Divider --}}
                    <div class="w-16 h-1 bg-green-400 rounded my-5"></div>

                    {{-- Info kontak / langkah --}}
                    <div class="bg-green-50 border border-green-200 rounded-lg px-6 py-4 max-w-sm w-full text-left">
                        <p class="text-sm font-semibold text-green-800 mb-2 uppercase tracking-wide">
                            Langkah Selanjutnya
                        </p>
                        <ul class="text-sm text-green-700 space-y-1 list-disc list-inside">
                            <li>Datangi atau hubungi pengurus TPA.</li>
                            <li>Sampaikan nama lengkap dan email akun Anda.</li>
                            <li>Pengurus akan mengaitkan akun dengan data siswa.</li>
                            <li>Login kembali setelah proses selesai.</li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
