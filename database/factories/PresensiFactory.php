<?php

namespace Database\Factories;

use App\Models\Jadwal;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Presensi>
 */
class PresensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'siswa_id'  => Siswa::factory(),
            'jadwal_id' => Jadwal::factory(),
            'tanggal'   => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'status'    => fake()->randomElement(['Hadir', 'Sakit', 'Izin', 'Alpha']),
        ];
    }
}
