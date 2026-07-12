<?php

use App\Models\User;
use App\Models\HariLibur;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat daftar hari libur', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->get(route('pengurus.kelola-harilibur.index'));
    
    $response->assertStatus(200);
});

it('pengurus dapat menambahkan hari libur', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->post(route('pengurus.kelola-harilibur.store'), [
        'tanggal' => '2026-08-17',
        'keterangan' => 'Hari Kemerdekaan RI',
    ]);

    $response->assertRedirect(route('pengurus.kelola-harilibur.index'));
    $this->assertDatabaseHas('hari_libur', ['tanggal' => '2026-08-17']);
});

it('pengurus dapat memperbarui data hari libur', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $hariLibur = HariLibur::factory()->create(['keterangan' => 'Libur Biasa']);

    $response = $this->actingAs($pengurus)->put(route('pengurus.kelola-harilibur.update', $hariLibur->id), [
        'tanggal' => $hariLibur->tanggal,
        'keterangan' => 'Libur Diubah',
    ]);

    $response->assertRedirect(route('pengurus.kelola-harilibur.index'));
    $this->assertDatabaseHas('hari_libur', ['keterangan' => 'Libur Diubah']);
});

it('pengurus dapat menghapus hari libur', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $hariLibur = HariLibur::factory()->create();

    $response = $this->actingAs($pengurus)->delete(route('pengurus.kelola-harilibur.destroy', $hariLibur->id));

    $response->assertRedirect(route('pengurus.kelola-harilibur.index'));
    $this->assertSoftDeleted('hari_libur', ['id' => $hariLibur->id]);
});