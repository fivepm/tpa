<?php

use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Materi;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\Perkembangan;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('pengurus dapat melihat halaman utama rekap laporan', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    
    // Buat data kelas dan siswa agar fungsi looping di controller tidak error
    $kelas = Kelas::factory()->create();
    Siswa::factory()->create(['kelas_id' => $kelas->id_kelas ?? $kelas->id]);

    $response = $this->actingAs($pengurus)->get(route('pengurus.laporan.index'));

    $response->assertStatus(200);
    $response->assertViewHasAll(['rekapKelas', 'bulan', 'tahun', 'persentaseSekolah']);
});

it('pengurus dapat melihat detail laporan per kelas', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    
    // Setup hierarki data agar controller bisa membaca presensi
    $kelas = Kelas::factory()->create();
    $siswa = Siswa::factory()->create(['kelas_id' => $kelas->id_kelas ?? $kelas->id]);
    
    Presensi::factory()->create([
        'siswa_id' => $siswa->id,
        'tanggal'  => now()->format('Y-m-d'),
        'status'   => 'hadir',
    ]);

    $response = $this->actingAs($pengurus)->get(route('pengurus.laporan.showKelas', $kelas->id_kelas ?? $kelas->id));

    $response->assertStatus(200);
    $response->assertViewHasAll(['kelas', 'rekapSiswa', 'daftarHari', 'bulan', 'tahun']);
});

it('pengurus dapat melihat detail laporan per siswa', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $guru = User::factory()->create(['role' => 'guru']);
    
    $kelas = Kelas::factory()->create();
    $materi = Materi::factory()->create();
    
    $siswa = Siswa::factory()->create(['kelas_id' => $kelas->id_kelas ?? $kelas->id]);
    
    $jadwal = Jadwal::factory()->create([
        'kelas_id'  => $kelas->id_kelas ?? $kelas->id,
        'guru_id'   => $guru->id,
        'materi_id' => $materi->id,
        'hari'      => now()->locale('id')->dayName,
        'jam_mulai' => '08:00',
        'jam_selesai'=> '09:30'
    ]);

    // Setup Presensi (Sesuai model yang kamu berikan)
    Presensi::factory()->create([
        'siswa_id'  => $siswa->id,
        'tanggal'   => now()->format('Y-m-d'),
        'status'    => 'hadir',
    ]);

    // Setup Perkembangan (Sesuai model yang kamu berikan)
    Perkembangan::factory()->create([
        'siswa_id'  => $siswa->id,
        'guru_id'   => $guru->id,
        'jadwal_id' => $jadwal->id,
        'tanggal'   => now()->format('Y-m-d'),
        'penilaian' => 'A',
        'catatan'   => 'Sangat baik'
    ]);

    $response = $this->actingAs($pengurus)->get(route('pengurus.laporan.showSiswa', $siswa->id));

    $response->assertStatus(200);
    $response->assertViewHasAll(['siswa', 'logBulanan', 'bulan', 'tahun']);
});

it('pengurus dapat mengekspor laporan kelas ke PDF', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $kelas = Kelas::factory()->create();

    $response = $this->actingAs($pengurus)->get(route('pengurus.laporan.exportKelasPdf', $kelas->id_kelas ?? $kelas->id));

    $response->assertStatus(200);
});