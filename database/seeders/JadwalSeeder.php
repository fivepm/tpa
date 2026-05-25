<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Materi;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat data Jadwal...');

        $kelasPaud = Kelas::where('nama_kelas', 'PAUD')->first();
        $kelasCaberawit = Kelas::where('nama_kelas', 'Caberawit A')->first();

        $guru1 = User::where('username', 'ibu.guru')->first();
        $guru2 = User::where('username', 'budi.guru')->first();

        $materiQuran = Materi::where('nama_materi', 'Al-Quran')->first();
        $materiDoa = Materi::where('nama_materi', 'Doa Harian')->first();

        if ($kelasPaud && $guru1 && $materiQuran) {
            Jadwal::create([
                'kelas_id' => $kelasPaud->id_kelas,
                'guru_id' => $guru1->id,
                'materi_id' => $materiQuran->id,
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00'
            ]);
        }

        if ($kelasCaberawit && $guru2 && $materiDoa) {
            Jadwal::create([
                'kelas_id' => $kelasCaberawit->id_kelas,
                'guru_id' => $guru2->id,
                'materi_id' => $materiDoa->id,
                'hari' => 'Rabu',
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:00'
            ]);
        }
    }
}
