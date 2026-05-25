<?php

use App\Models\AuditLog;
use App\Models\Materi;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    expect((new Materi)->getFillable())->toContain('nama_materi');
});

it('menggunakan tabel materi', function () {
    expect((new Materi)->getTable())->toBe('materi');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat materi baru', function () {
    $materi = Materi::factory()->create(['nama_materi' => 'Al-Quran Dasar']);

    expect(Materi::count())->toBe(1)
        ->and($materi->nama_materi)->toBe('Al-Quran Dasar');
});

it('dapat memperbarui nama materi', function () {
    $materi = Materi::factory()->create();
    $materi->update(['nama_materi' => 'Tajwid Lanjutan']);

    expect($materi->fresh()->nama_materi)->toBe('Tajwid Lanjutan');
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $materi = Materi::factory()->create();
    $id     = $materi->id;

    $materi->delete();

    expect(Materi::find($id))->toBeNull()
        ->and(Materi::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $materi = Materi::factory()->create();
    $id     = $materi->id;

    $materi->delete();
    Materi::withTrashed()->find($id)->restore();

    expect(Materi::find($id))->not->toBeNull()
        ->and(Materi::find($id)->deleted_at)->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat materi dibuat', function () {
    $materi = Materi::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', Materi::class)
        ->where('auditable_id', $materi->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat materi diperbarui', function () {
    $materi = Materi::factory()->create(['nama_materi' => 'Awal']);
    $materi->update(['nama_materi' => 'Akhir']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', Materi::class)
        ->where('auditable_id', $materi->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['nama_materi'])->toBe('Awal')
        ->and($log->new_values['nama_materi'])->toBe('Akhir');
});

it('mencatat audit log saat materi dihapus', function () {
    $materi = Materi::factory()->create();
    $id     = $materi->id;
    $materi->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', Materi::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});

it('tidak ada duplikasi audit log jika tidak ada field yang berubah', function () {
    $materi = Materi::factory()->create();
    // Update dengan value yang sama
    $materi->update(['nama_materi' => $materi->nama_materi]);

    $jumlahLog = AuditLog::where('event', 'updated')
        ->where('auditable_type', Materi::class)
        ->where('auditable_id', $materi->id)
        ->count();

    expect($jumlahLog)->toBe(0);
});
