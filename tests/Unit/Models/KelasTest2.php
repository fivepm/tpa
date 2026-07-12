<?php

use App\Models\AuditLog;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\WaliKelas;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    expect((new Kelas)->getFillable())->toContain('nama_kelas');
});

it('menggunakan tabel kelas dengan primary key id_kelas', function () {
    expect((new Kelas)->getTable())->toBe('kelas')
        ->and((new Kelas)->getKeyName())->toBe('id_kelas');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat kelas baru', function () {
    $kelas = Kelas::factory()->create(['nama_kelas' => 'Kelas A']);

    expect(Kelas::count())->toBe(1)
        ->and($kelas->nama_kelas)->toBe('Kelas A');
});

it('dapat memperbarui nama kelas', function () {
    $kelas = Kelas::factory()->create();
    $kelas->update(['nama_kelas' => 'Kelas Updated']);

    expect($kelas->fresh()->nama_kelas)->toBe('Kelas Updated');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi hasMany ke Siswa', function () {
    $kelas = Kelas::factory()->create();
    Siswa::factory()->count(3)->create(['kelas_id' => $kelas->id_kelas]);

    expect($kelas->siswa)->toHaveCount(3)
        ->and($kelas->siswa->first())->toBeInstanceOf(Siswa::class);
});

it('memiliki relasi hasOne ke WaliKelas', function () {
    $kelas    = Kelas::factory()->create();
    $guru     = User::factory()->guru()->create();
    WaliKelas::create(['kelas_id' => $kelas->id_kelas, 'user_id' => $guru->id]);

    expect($kelas->waliKelas)->toBeInstanceOf(WaliKelas::class);
});

it('memiliki relasi belongsToMany ke User (guru)', function () {
    $kelas = Kelas::factory()->create();
    $guru  = User::factory()->guru()->create();
    $kelas->guru()->attach($guru->id);

    expect($kelas->guru)->toHaveCount(1)
        ->and($kelas->guru->first()->id)->toBe($guru->id);
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $kelas = Kelas::factory()->create();
    $id    = $kelas->id_kelas;

    $kelas->delete();

    expect(Kelas::find($id))->toBeNull()
        ->and(Kelas::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $kelas = Kelas::factory()->create();
    $id    = $kelas->id_kelas;

    $kelas->delete();
    Kelas::withTrashed()->find($id)->restore();

    expect(Kelas::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat kelas dibuat', function () {
    $kelas = Kelas::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', Kelas::class)
        ->where('auditable_id', $kelas->id_kelas)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat kelas diperbarui', function () {
    $kelas = Kelas::factory()->create(['nama_kelas' => 'Lama']);
    $kelas->update(['nama_kelas' => 'Baru']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', Kelas::class)
        ->where('auditable_id', $kelas->id_kelas)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['nama_kelas'])->toBe('Lama')
        ->and($log->new_values['nama_kelas'])->toBe('Baru');
});

it('mencatat audit log saat kelas dihapus', function () {
    $kelas = Kelas::factory()->create();
    $id    = $kelas->id_kelas;
    $kelas->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', Kelas::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
