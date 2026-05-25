<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat data Siswa...');
        $faker = Faker::create('id_ID');

        $kelasPaud = Kelas::where('nama_kelas', 'PAUD')->first();
        $kelasCaberawit = Kelas::where('nama_kelas', 'Caberawit A')->first();

        $ortu1 = User::where('username', 'ayah.budi')->first();
        $ortu2 = User::where('username', 'ibu.ani')->first();

        if ($kelasPaud && $ortu1) {
            Siswa::create(['nama' => $faker->name, 'nis' => '1111', 'kelas_id' => $kelasPaud->id_kelas, 'orangtua_id' => $ortu1->id]);
        }

        if ($kelasPaud && $ortu2) {
            Siswa::create(['nama' => $faker->name, 'nis' => '2222', 'kelas_id' => $kelasPaud->id_kelas, 'orangtua_id' => $ortu2->id]);
        }

        if ($kelasCaberawit && $ortu1) {
            Siswa::create(['nama' => $faker->name, 'nis' => '3333', 'kelas_id' => $kelasCaberawit->id_kelas, 'orangtua_id' => $ortu1->id]);
        }
    }
}
