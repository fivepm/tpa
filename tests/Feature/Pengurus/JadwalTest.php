<?php

use App\Models\User;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Materi;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat daftar jadwal', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $response = $this->actingAs($pengurus)->get(route('pengurus.kelola-jadwal.index'));
    
    $response->assertStatus(200);
});

it('pengurus dapat membuat jadwal pelajaran', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas = Kelas::factory()->create();
    $guru = User::factory()->create(['role' => 'guru']);
    $materi = Materi::factory()->create();

    $response = $this->actingAs($pengurus)->post(route('pengurus.kelola-jadwal.store'), [
        'kelas_id' => $kelas->id_kelas,
        'guru_id' => $guru->id,
        'materi_id' => $materi->id,
        'hari' => 'Senin',
        'jam_mulai' => '08:00',
        'jam_selesai' => '09:30',
    ]);

    $response->assertRedirect(route('pengurus.kelola-jadwal.index'));
    $this->assertDatabaseHas('jadwal', ['hari' => 'Senin']);
});

it('pengurus dapat memperbarui jadwal pelajaran', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $jadwal = Jadwal::factory()->create(['hari' => 'Selasa']);

    $response = $this->actingAs($pengurus)->put(route('pengurus.kelola-jadwal.update', $jadwal->id), [
        'kelas_id' => $jadwal->kelas_id,
        'guru_id' => $jadwal->guru_id,
        'materi_id' => $jadwal->materi_id,
        'hari' => 'Rabu',
        'jam_mulai' => '10:00',
        'jam_selesai' => '11:30',
    ]);

    $response->assertRedirect(route('pengurus.kelola-jadwal.index'));
    $this->assertDatabaseHas('jadwal', ['hari' => 'Rabu']);
});

it('pengurus dapat menghapus jadwal pelajaran', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $jadwal = Jadwal::factory()->create();

    $response = $this->actingAs($pengurus)->delete(route('pengurus.kelola-jadwal.destroy', $jadwal->id));

    $response->assertRedirect(route('pengurus.kelola-jadwal.index'));
    $this->assertSoftDeleted('jadwal', ['id' => $jadwal->id]);
});