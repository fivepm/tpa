<?php

use App\Models\AuditLog;
use App\Models\Jadwal;
use App\Models\Perkembangan;
use App\Models\Siswa;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    $fillable = (new Perkembangan)->getFillable();
    expect($fillable)->toContain('siswa_id', 'guru_id', 'tanggal', 'penilaian', 'catatan', 'jadwal_id');
});

it('menggunakan tabel perkembangan', function () {
    expect((new Perkembangan)->getTable())->toBe('perkembangan');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat catatan perkembangan baru', function () {
    $perkembangan = Perkembangan::factory()->create([
        'penilaian' => 'Sangat Baik',
        'catatan'   => 'Anak sangat aktif',
    ]);

    expect(Perkembangan::count())->toBe(1)
        ->and($perkembangan->penilaian)->toBe('Sangat Baik');
});

it('dapat memperbarui catatan perkembangan', function () {
    $perkembangan = Perkembangan::factory()->create();
    $perkembangan->update(['catatan' => 'Catatan diperbarui', 'penilaian' => 'Baik']);

    expect($perkembangan->fresh()->catatan)->toBe('Catatan diperbarui')
        ->and($perkembangan->fresh()->penilaian)->toBe('Baik');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsTo ke Siswa', function () {
    $perkembangan = Perkembangan::factory()->create();

    expect($perkembangan->siswa)->toBeInstanceOf(Siswa::class);
});

it('memiliki relasi belongsTo ke User sebagai guru', function () {
    $perkembangan = Perkembangan::factory()->create();

    expect($perkembangan->guru)->toBeInstanceOf(User::class)
        ->and($perkembangan->guru->role)->toBe('guru');
});

it('memiliki relasi belongsTo ke Jadwal', function () {
    $perkembangan = Perkembangan::factory()->create();

    expect($perkembangan->jadwal)->toBeInstanceOf(Jadwal::class);
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $perkembangan = Perkembangan::factory()->create();
    $id           = $perkembangan->id;

    $perkembangan->delete();

    expect(Perkembangan::find($id))->toBeNull()
        ->and(Perkembangan::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $perkembangan = Perkembangan::factory()->create();
    $id           = $perkembangan->id;

    $perkembangan->delete();
    Perkembangan::withTrashed()->find($id)->restore();

    expect(Perkembangan::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat perkembangan dibuat', function () {
    $perkembangan = Perkembangan::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', Perkembangan::class)
        ->where('auditable_id', $perkembangan->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat perkembangan diperbarui', function () {
    $perkembangan = Perkembangan::factory()->create(['penilaian' => 'Baik']);
    $perkembangan->update(['penilaian' => 'Sangat Baik']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', Perkembangan::class)
        ->where('auditable_id', $perkembangan->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['penilaian'])->toBe('Baik')
        ->and($log->new_values['penilaian'])->toBe('Sangat Baik');
});

it('mencatat audit log saat perkembangan dihapus', function () {
    $perkembangan = Perkembangan::factory()->create();
    $id           = $perkembangan->id;
    $perkembangan->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', Perkembangan::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
