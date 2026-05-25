<?php

namespace Database\Factories;

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JurnalMengajar>
 */
class JurnalMengajarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jadwal_id' => Jadwal::factory(),
            'guru_id'   => User::factory()->guru(),
            'tanggal'   => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'topik'     => fake()->sentence(4),
            'metode'    => fake()->randomElement([
                'Ceramah', 'Tanya Jawab', 'Hafalan', 'Praktek Langsung',
                'Diskusi Kelompok', 'Penugasan', 'Demonstrasi',
            ]),
            'ringkasan' => fake()->paragraph(),
            'catatan'   => fake()->optional()->sentence(),
        ];
    }
}
