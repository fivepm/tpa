<?php

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('menampilkan halaman peringatan jika orang tua belum memiliki data anak', function () {
    $orangTua = User::factory()->create(['role' => 'orangtua']);
    
    $response = $this->actingAs($orangTua)->get(route('orangtua.dashboard'));
    
    $response->assertStatus(200);
    $response->assertViewIs('orangtua.dashboard_no_child');
});

it('menampilkan halaman dashboard lengkap jika orang tua memiliki anak', function () {
    $orangTua = User::factory()->create(['role' => 'orangtua']);
    $kelas = Kelas::factory()->create();
    $siswa = Siswa::factory()->create([
        'orangtua_id' => $orangTua->id,
        'kelas_id'    => $kelas->id_kelas ?? $kelas->id
    ]);

    $response = $this->actingAs($orangTua)->get(route('orangtua.dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('orangtua.dashboard');
    $response->assertViewHasAll([
        'selectedAnak', 
        'logBulanan', 
        'bulan', 
        'tahun', 
        'anakList', 
        'jurnalHarian'
    ]);
});