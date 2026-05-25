<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat data Kelas...');

        Kelas::create(['nama_kelas' => 'PAUD']);
        Kelas::create(['nama_kelas' => 'Caberawit A']);
        Kelas::create(['nama_kelas' => 'Caberawit B']);
        Kelas::create(['nama_kelas' => 'Pra Remaja']);
        Kelas::create(['nama_kelas' => 'Remaja']);
    }
}
