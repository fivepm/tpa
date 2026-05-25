<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HariLibur>
 */
class HariLiburFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tanggal'    => fake()->unique()->dateTimeBetween('2025-01-01', '2026-12-31')->format('Y-m-d'),
            'keterangan' => fake()->randomElement([
                'Hari Raya Idul Fitri',
                'Hari Raya Idul Adha',
                'Maulid Nabi Muhammad SAW',
                'Isra Miraj',
                'Libur Nasional',
                'Libur Sekolah',
            ]),
        ];
    }
}
