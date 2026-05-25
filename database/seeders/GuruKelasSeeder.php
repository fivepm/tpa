<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Menghubungkan Guru ke Kelas...');

        $guru1 = User::where('username', 'ibu.guru')->first();
        $guru2 = User::where('username', 'budi.guru')->first();

        $kelasPaud = Kelas::where('nama_kelas', 'PAUD')->first();
        $kelasCaberawit = Kelas::where('nama_kelas', 'Caberawit A')->first();

        if ($guru1 && $kelasPaud) {
            DB::table('kelas_user')->insert(['user_id' => $guru1->id, 'kelas_id' => $kelasPaud->id_kelas]);
        }

        if ($guru2 && $kelasPaud) {
            DB::table('kelas_user')->insert(['user_id' => $guru2->id, 'kelas_id' => $kelasPaud->id_kelas]);
        }

        if ($guru2 && $kelasCaberawit) {
            DB::table('kelas_user')->insert(['user_id' => $guru2->id, 'kelas_id' => $kelasCaberawit->id_kelas]);
        }
    }
}
