<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jadwal>
 */
class JadwalFactory extends Factory
{
    public function definition(): array
    {
        $hariList   = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jamMulai   = fake()->time('H:i', '12:00:00');
        $jamSelesai = fake()->time('H:i', '17:00:00');

        // Pastikan jam_selesai setelah jam_mulai
        if ($jamSelesai <= $jamMulai) {
            $jamSelesai = date('H:i', strtotime($jamMulai) + 3600);
        }

        return [
            'kelas_id'    => Kelas::factory(),
            'guru_id'     => User::factory()->guru(),
            'materi_id'   => Materi::factory(),
            'hari'        => fake()->randomElement($hariList),
            'jam_mulai'   => $jamMulai,
            'jam_selesai' => $jamSelesai,
        ];
    }
}
