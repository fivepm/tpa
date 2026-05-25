<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kelas' => 'Kelas ' . fake()->unique()->bothify('? #'),
        ];
    }
}
