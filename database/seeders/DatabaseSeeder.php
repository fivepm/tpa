<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Siswa;
use App\Models\Jadwal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Kelas::truncate();
        Materi::truncate();
        Siswa::truncate();
        Jadwal::truncate();
        DB::table('kelas_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UserSeeder::class,
            KelasSeeder::class,
            MateriSeeder::class,
            GuruKelasSeeder::class,
            SiswaSeeder::class,
            JadwalSeeder::class,
        ]);

        $this->command->info('Semua Seeding selesai dengan sukses!');
    }
}
