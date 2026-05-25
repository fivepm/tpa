<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nis'         => fake()->unique()->numerify('##########'),
            'nama'        => fake('id_ID')->name(),
            'kelas_id'    => Kelas::factory(),
            'orangtua_id' => User::factory()->orangtua(),
        ];
    }
}
