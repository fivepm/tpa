<?php

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat daftar siswa', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->get(route('pengurus.kelola-siswa.index'));
    
    $response->assertStatus(200);
});

it('pengurus dapat menambahkan data siswa', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas = Kelas::factory()->create();
    $orangtua = User::factory()->create(['role' => 'orangtua']);

    $response = $this->actingAs($pengurus)->post(route('pengurus.kelola-siswa.store'), [
        'nis' => '123456',
        'nama' => 'Ahmad',
        'kelas_id' => $kelas->id_kelas,
        'orangtua_id' => $orangtua->id,
    ]);

    $response->assertRedirect(route('pengurus.kelola-siswa.index'));
    $this->assertDatabaseHas('siswa', ['nis' => '123456', 'nama' => 'Ahmad']);
});

it('pengurus dapat memperbarui data siswa', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $siswa = Siswa::factory()->create(['nama' => 'Siswa Lama']);

    $response = $this->actingAs($pengurus)->put(route('pengurus.kelola-siswa.update', $siswa->id), [
        'nis' => $siswa->nis,
        'nama' => 'Siswa Baru',
        'kelas_id' => $siswa->kelas_id,
        'orangtua_id' => $siswa->orangtua_id,
    ]);

    $response->assertRedirect(route('pengurus.kelola-siswa.index'));
    $this->assertDatabaseHas('siswa', ['nama' => 'Siswa Baru']);
});

it('pengurus dapat menghapus data siswa', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $siswa = Siswa::factory()->create();

    $response = $this->actingAs($pengurus)->delete(route('pengurus.kelola-siswa.destroy', $siswa->id));

    $response->assertRedirect(route('pengurus.kelola-siswa.index'));
    $this->assertSoftDeleted('siswa', ['id' => $siswa->id]);
});