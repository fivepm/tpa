<?php

use App\Models\User;
use App\Models\Materi;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat daftar materi', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->get(route('pengurus.kelola-materi.index'));
    
    $response->assertStatus(200);
});

it('pengurus dapat membuat materi baru', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->post(route('pengurus.kelola-materi.store'), [
        'nama_materi' => 'Fiqih Bab 1',
    ]);

    $response->assertRedirect(route('pengurus.kelola-materi.index'));
    $this->assertDatabaseHas('materi', ['nama_materi' => 'Fiqih Bab 1']);
});

it('pengurus dapat memperbarui data materi', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $materi = Materi::factory()->create(['nama_materi' => 'Materi Lama']);

    $response = $this->actingAs($pengurus)->put(route('pengurus.kelola-materi.update', $materi->id), [
        'nama_materi' => 'Materi Baru',
    ]);

    $response->assertRedirect(route('pengurus.kelola-materi.index'));
    $this->assertDatabaseHas('materi', ['nama_materi' => 'Materi Baru']);
});

it('pengurus dapat menghapus materi', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $materi = Materi::factory()->create();

    $response = $this->actingAs($pengurus)->delete(route('pengurus.kelola-materi.destroy', $materi->id));

    $response->assertRedirect(route('pengurus.kelola-materi.index'));
    $this->assertSoftDeleted('materi', ['id' => $materi->id]);
});