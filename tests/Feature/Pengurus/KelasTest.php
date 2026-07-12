<?php

use App\Models\User;
use App\Models\Kelas;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat daftar kelas', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->get(route('pengurus.kelola-kelas.index'));
    
    $response->assertStatus(200);
});

it('pengurus dapat membuat kelas baru', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->post(route('pengurus.kelola-kelas.store'), [
        'nama_kelas' => 'Kelas 10A',
    ]);

    $response->assertRedirect(route('pengurus.kelola-kelas.index'));
    $this->assertDatabaseHas('kelas', ['nama_kelas' => 'Kelas 10A']);
});

it('pengurus dapat memperbarui data kelas', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas = Kelas::factory()->create(['nama_kelas' => 'Kelas Lama']);

    $response = $this->actingAs($pengurus)->put(route('pengurus.kelola-kelas.update', $kelas->id_kelas), [
        'nama_kelas' => 'Kelas Baru',
    ]);

    $response->assertRedirect(route('pengurus.kelola-kelas.index'));
    $this->assertDatabaseHas('kelas', ['nama_kelas' => 'Kelas Baru']);
});

it('pengurus dapat menghapus kelas', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas = Kelas::factory()->create();

    $response = $this->actingAs($pengurus)->delete(route('pengurus.kelola-kelas.destroy', $kelas->id_kelas));

    $response->assertRedirect(route('pengurus.kelola-kelas.index'));
    $this->assertSoftDeleted('kelas', ['id_kelas' => $kelas->id_kelas]); 
});