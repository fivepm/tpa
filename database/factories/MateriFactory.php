<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Materi>
 */
class MateriFactory extends Factory
{
    public function definition(): array
    {
        $materi = [
            'Al-Quran', 'Hadits', 'Fiqih', 'Aqidah', 'Akhlak',
            'Tahfidz', 'Bahasa Arab', 'Tajwid', 'Sirah Nabawiyah',
        ];

        return [
            'nama_materi' => fake()->unique()->randomElement($materi) . ' ' . fake()->bothify('##'),
        ];
    }
}
