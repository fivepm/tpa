<?php

namespace Database\Seeders;

use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        Materi::create(['nama_materi' => 'Al-Quran']);
        Materi::create(['nama_materi' => 'Hadits']);
        Materi::create(['nama_materi' => 'Doa Harian']);
        Materi::create(['nama_materi' => 'Praktik Ibadah']);
        Materi::create(['nama_materi' => 'Bahasa Arab']);
    }
}
