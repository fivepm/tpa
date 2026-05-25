<?php

namespace Database\Factories;

use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perkembangan>
 */
class PerkembanganFactory extends Factory
{
    public function definition(): array
    {
        return [
            'siswa_id'  => Siswa::factory(),
            'guru_id'   => User::factory()->guru(),
            'jadwal_id' => Jadwal::factory(),
            'tanggal'   => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'penilaian' => fake()->randomElement(['Sangat Baik', 'Baik', 'Cukup', 'Perlu Bimbingan']),
            'catatan'   => fake()->paragraph(),
        ];
    }
}
