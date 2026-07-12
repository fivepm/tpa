<?php

use App\Models\AuditLog;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\Siswa;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    $fillable = (new Presensi)->getFillable();
    expect($fillable)->toContain('siswa_id', 'jadwal_id', 'tanggal', 'status');
});

it('menggunakan tabel presensi', function () {
    expect((new Presensi)->getTable())->toBe('presensi');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat presensi baru', function () {
    $presensi = Presensi::factory()->create(['status' => 'Hadir']);

    expect(Presensi::count())->toBe(1)
        ->and($presensi->status)->toBe('Hadir');
});

it('status hanya boleh Hadir, Sakit, Izin, atau Alpha', function () {
    $validStatus = ['Hadir', 'Sakit', 'Izin', 'Alpha'];

    foreach ($validStatus as $status) {
        $presensi = Presensi::factory()->create(['status' => $status]);
        expect($presensi->status)->toBe($status);
    }
});

it('dapat memperbarui status presensi', function () {
    $presensi = Presensi::factory()->create(['status' => 'Hadir']);
    $presensi->update(['status' => 'Sakit']);

    expect($presensi->fresh()->status)->toBe('Sakit');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsTo ke Siswa', function () {
    $presensi = Presensi::factory()->create();

    expect($presensi->siswa)->toBeInstanceOf(Siswa::class);
});

it('memiliki relasi belongsTo ke Jadwal', function () {
    $presensi = Presensi::factory()->create();

    expect($presensi->jadwal)->toBeInstanceOf(Jadwal::class);
});

// ─────────────────────────────────────────────
// updateOrCreate
// ─────────────────────────────────────────────

it('updateOrCreate membuat record baru jika belum ada', function () {
    $siswa  = Siswa::factory()->create();
    $jadwal = Jadwal::factory()->create();

    Presensi::updateOrCreate(
        ['siswa_id' => $siswa->id, 'tanggal' => '2026-05-25'],
        ['status' => 'Hadir', 'jadwal_id' => $jadwal->id]
    );

    expect(Presensi::count())->toBe(1);
});

it('updateOrCreate memperbarui record yang sudah ada tanpa duplikasi', function () {
    $siswa  = Siswa::factory()->create();
    $jadwal = Jadwal::factory()->create();

    Presensi::updateOrCreate(
        ['siswa_id' => $siswa->id, 'tanggal' => '2026-05-25'],
        ['status' => 'Hadir', 'jadwal_id' => $jadwal->id]
    );

    Presensi::updateOrCreate(
        ['siswa_id' => $siswa->id, 'tanggal' => '2026-05-25'],
        ['status' => 'Izin', 'jadwal_id' => $jadwal->id]
    );

    expect(Presensi::count())->toBe(1)
        ->and(Presensi::first()->status)->toBe('Izin');
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat presensi dibuat', function () {
    $presensi = Presensi::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', Presensi::class)
        ->where('auditable_id', $presensi->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat status presensi diperbarui', function () {
    $presensi = Presensi::factory()->create(['status' => 'Hadir']);
    $presensi->update(['status' => 'Alpha']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', Presensi::class)
        ->where('auditable_id', $presensi->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['status'])->toBe('Hadir')
        ->and($log->new_values['status'])->toBe('Alpha');
});

it('audit log mencatat created event dari updateOrCreate saat data baru', function () {
    $siswa  = Siswa::factory()->create();
    $jadwal = Jadwal::factory()->create();

    $presensi = Presensi::updateOrCreate(
        ['siswa_id' => $siswa->id, 'tanggal' => '2026-05-25'],
        ['status' => 'Hadir', 'jadwal_id' => $jadwal->id]
    );

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', Presensi::class)
        ->where('auditable_id', $presensi->id)
        ->exists()
    )->toBeTrue();
});
