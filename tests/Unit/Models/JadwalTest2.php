<?php

use App\Models\AuditLog;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    $fillable = (new Jadwal)->getFillable();
    expect($fillable)->toContain('kelas_id', 'guru_id', 'materi_id', 'hari', 'jam_mulai', 'jam_selesai');
});

it('menggunakan tabel jadwal', function () {
    expect((new Jadwal)->getTable())->toBe('jadwal');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat jadwal baru', function () {
    $jadwal = Jadwal::factory()->create();

    expect(Jadwal::count())->toBe(1)
        ->and($jadwal->hari)->toBeIn(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
});

it('dapat memperbarui data jadwal', function () {
    $jadwal = Jadwal::factory()->create();
    $jadwal->update(['hari' => 'Jumat']);

    expect($jadwal->fresh()->hari)->toBe('Jumat');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsTo ke Kelas', function () {
    $jadwal = Jadwal::factory()->create();

    expect($jadwal->kelas)->toBeInstanceOf(Kelas::class);
});

it('memiliki relasi belongsTo ke User sebagai guru', function () {
    $jadwal = Jadwal::factory()->create();

    expect($jadwal->guru)->toBeInstanceOf(User::class)
        ->and($jadwal->guru->role)->toBe('guru');
});

it('memiliki relasi belongsTo ke Materi', function () {
    $jadwal = Jadwal::factory()->create();

    expect($jadwal->materi)->toBeInstanceOf(Materi::class);
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $jadwal = Jadwal::factory()->create();
    $id     = $jadwal->id;

    $jadwal->delete();

    expect(Jadwal::find($id))->toBeNull()
        ->and(Jadwal::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $jadwal = Jadwal::factory()->create();
    $id     = $jadwal->id;

    $jadwal->delete();
    Jadwal::withTrashed()->find($id)->restore();

    expect(Jadwal::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat jadwal dibuat', function () {
    $jadwal = Jadwal::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', Jadwal::class)
        ->where('auditable_id', $jadwal->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat jadwal diperbarui', function () {
    $jadwal = Jadwal::factory()->create(['hari' => 'Senin']);
    $jadwal->update(['hari' => 'Rabu']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', Jadwal::class)
        ->where('auditable_id', $jadwal->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['hari'])->toBe('Senin')
        ->and($log->new_values['hari'])->toBe('Rabu');
});

it('mencatat audit log saat jadwal dihapus', function () {
    $jadwal = Jadwal::factory()->create();
    $id     = $jadwal->id;
    $jadwal->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', Jadwal::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
