<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\WaliKelas;
use Illuminate\Support\Facades\DB;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat halaman kelola wali kelas', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    
    $response = $this->actingAs($pengurus)
                     ->get(route('pengurus.kelola-walikelas.index'));

    $response->assertStatus(200);
});

it('pengurus dapat menugaskan wali kelas baru ke suatu kelas', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas    = Kelas::factory()->create();
    $guru     = User::factory()->create(['role' => 'guru']);

    DB::table('kelas_user')->insert([
        'kelas_id' => $kelas->id_kelas,
        'user_id'  => $guru->id,
    ]);

    $payload = [
        'kelas_id' => $kelas->id_kelas,
        'user_id'  => $guru->id,
    ];

    $response = $this->actingAs($pengurus)
                     ->post(route('pengurus.kelola-walikelas.store'), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('wali_kelas', [
        'kelas_id' => $kelas->id_kelas,
        'user_id'  => $guru->id,
    ]);
});

it('pengurus dapat menghapus penugasan wali kelas', function () {
    $pengurus  = User::factory()->create(['role' => 'pengurus']);
    $waliKelas = WaliKelas::factory()->create();

    $response = $this->actingAs($pengurus)
                     ->delete(route('pengurus.kelola-walikelas.destroy', $waliKelas->id));

    $response->assertRedirect();
    $this->assertSoftDeleted('wali_kelas', [
        'id' => $waliKelas->id,
    ]);
});

it('pengurus dapat mengambil daftar guru berdasarkan kelas via API', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas    = Kelas::factory()->create();
    
    $response = $this->actingAs($pengurus)
                     ->get(route('pengurus.api.walikelas.guru', $kelas->id_kelas));

    $response->assertStatus(200);
});