<?php

use App\Models\User;
use App\Models\Jadwal;
use App\Models\Siswa;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('guru dapat melihat halaman catatan perkembangan', function () {
    $guru = User::factory()->create(['role' => 'guru']);
    
    $response = $this->actingAs($guru)
                     ->get(route('guru.perkembangan.index'));

    $response->assertStatus(200);
});

it('guru dapat menyimpan catatan perkembangan siswa', function () {
    $guru = User::factory()->create(['role' => 'guru']);
    $jadwal = Jadwal::factory()->create(['guru_id' => $guru->id]);
    $siswa = Siswa::factory()->create();

    $payload = [
        'tanggal'   => '2023-10-10',
        'penilaian' => [
            $siswa->id => 'Sangat Baik'
        ],
        'catatan'   => [
            $siswa->id => 'Hafalan lancar dan fasih'
        ]
    ];

    $response = $this->actingAs($guru)
                     ->post(route('guru.perkembangan.store', $jadwal->id), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('perkembangan', [
        'siswa_id' => $siswa->id,
        'catatan'  => 'Hafalan lancar dan fasih',
    ]);
});

it('guru dapat memperbarui catatan perkembangan siswa (update)', function () {
    $guru = User::factory()->create(['role' => 'guru']);
    $jadwal = Jadwal::factory()->create(['guru_id' => $guru->id]);
    $siswa = Siswa::factory()->create();

    \App\Models\Perkembangan::factory()->create([
        'siswa_id'  => $siswa->id,
        'jadwal_id' => $jadwal->id,
        'tanggal'   => '2023-10-10',
        'catatan'   => 'Catatan lama'
    ]);

    $payload = [
        'tanggal'   => '2023-10-10',
        'penilaian' => [
            $siswa->id => 'Baik'
        ],
        'catatan'   => [
            $siswa->id => 'Catatan baru diupdate'
        ]
    ];

    $response = $this->actingAs($guru)
                     ->put(route('guru.perkembangan.update', $jadwal->id), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('perkembangan', [
        'siswa_id' => $siswa->id,
        'catatan'  => 'Catatan baru diupdate',
    ]);
});