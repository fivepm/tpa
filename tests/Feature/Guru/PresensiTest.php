<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('guru dapat melihat halaman daftar presensi', function () {
    $guru = User::factory()->create(['role' => 'guru']);
    
    $response = $this->actingAs($guru)
                     ->get(route('guru.presensi.index'));

    $response->assertStatus(200);
});

it('guru dapat menyimpan data presensi kelas', function () {
    $guru  = User::factory()->create(['role' => 'guru']);
    $kelas = Kelas::factory()->create();
    $siswa = Siswa::factory()->create();
    
    $payload = [
        'tanggal'  => '2023-10-10',
        'presensi' => [
            $siswa->id => 'hadir',
        ],
    ];

    $response = $this->actingAs($guru)
                     ->post(route('guru.presensi.store', $kelas->id_kelas), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('presensi', [
        'siswa_id' => $siswa->id,
        'status'   => 'hadir',
    ]);
});

it('guru dapat melihat halaman form/detail presensi kelas (show)', function () {
    $guru  = User::factory()->create(['role' => 'guru']);
    $kelas = Kelas::factory()->create();
    
    $response = $this->actingAs($guru)
                     ->get(route('guru.presensi.show', $kelas->id_kelas));

    $response->assertStatus(200);
});