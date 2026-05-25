<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membuat data Users...');

        User::create([
            'nama' => 'Admin Pengurus',
            'username' => 'pengurus',
            'password' => Hash::make('password'),
            'role' => 'pengurus'
        ]);

        User::create([
            'nama' => 'Ibu Guru Hebat',
            'username' => 'ibu.guru',
            'password' => Hash::make('password'),
            'role' => 'guru'
        ]);

        User::create([
            'nama' => 'Budi Setiawan',
            'username' => 'budi.guru',
            'password' => Hash::make('password'),
            'role' => 'guru'
        ]);

        User::create([
            'nama' => 'Ayah Budi',
            'username' => 'ayah.budi',
            'password' => Hash::make('password'),
            'role' => 'orangtua'
        ]);

        User::create([
            'nama' => 'Ibu Ani',
            'username' => 'ibu.ani',
            'password' => Hash::make('password'),
            'role' => 'orangtua'
        ]);
    }
}
