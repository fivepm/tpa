<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WaliKelas>
 */
class WaliKelasFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'user_id'  => User::factory()->guru(),
        ];
    }
}
