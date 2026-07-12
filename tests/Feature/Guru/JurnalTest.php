<?php

use App\Models\User;
use App\Models\Jadwal;
use App\Models\JurnalMengajar;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('guru dapat menyimpan jurnal mengajar baru', function () {
    $guru   = User::factory()->create(['role' => 'guru']);
    $jadwal = Jadwal::factory()->create(['guru_id' => $guru->id]);

    $payload = [
        'tanggal'   => '2023-10-10',
        'topik'     => 'Belajar Tajwid',
        'metode'    => 'Ceramah',
        'ringkasan' => 'Mempelajari hukum nun mati',
        'catatan'   => 'Santri sangat antusias',
    ];

    $response = $this->actingAs($guru)
                     ->post(route('guru.jurnal.store', $jadwal->id), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('jurnal_mengajar', [
        'jadwal_id' => $jadwal->id,
        'topik'     => 'Belajar Tajwid',
    ]);
});

it('guru dapat memperbarui jurnal mengajar (update)', function () {
    $tanggal = '2023-10-10';
    $guru    = User::factory()->create(['role' => 'guru']);
    $jadwal  = Jadwal::factory()->create(['guru_id' => $guru->id]);

    // Buat jurnal dengan tanggal SAMA dan guru_id SAMA agar query update bisa menemukan baris
    JurnalMengajar::create([
        'jadwal_id' => $jadwal->id,
        'guru_id'   => $guru->id,
        'tanggal'   => $tanggal,
        'topik'     => 'Topik Lama',
        'metode'    => 'Ceramah',
        'ringkasan' => 'Ringkasan lama',
    ]);

    $payload = [
        'tanggal'   => $tanggal,
        'topik'     => 'Topik Baru Diupdate',
        'metode'    => 'Ceramah',
        'ringkasan' => 'Mempelajari hukum nun mati',
    ];

    $response = $this->actingAs($guru)
                     ->put(route('guru.jurnal.update', $jadwal->id), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('jurnal_mengajar', [
        'jadwal_id' => $jadwal->id,
        'topik'     => 'Topik Baru Diupdate',
    ]);
});