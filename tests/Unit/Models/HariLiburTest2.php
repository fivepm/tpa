<?php

use App\Models\AuditLog;
use App\Models\HariLibur;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    expect((new HariLibur)->getFillable())->toContain('tanggal', 'keterangan');
});

it('menggunakan tabel hari_libur', function () {
    expect((new HariLibur)->getTable())->toBe('hari_libur');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat hari libur baru', function () {
    $hariLibur = HariLibur::factory()->create([
        'tanggal'    => '2026-01-01',
        'keterangan' => 'Tahun Baru',
    ]);

    expect(HariLibur::count())->toBe(1)
        ->and($hariLibur->keterangan)->toBe('Tahun Baru');
});

it('dapat memperbarui data hari libur', function () {
    $hariLibur = HariLibur::factory()->create();
    $hariLibur->update(['keterangan' => 'Libur Nasional Updated']);

    expect($hariLibur->fresh()->keterangan)->toBe('Libur Nasional Updated');
});

it('tanggal bersifat unik', function () {
    HariLibur::factory()->create(['tanggal' => '2026-06-01']);

    expect(fn () => HariLibur::factory()->create(['tanggal' => '2026-06-01']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $hariLibur = HariLibur::factory()->create();
    $id        = $hariLibur->id;

    $hariLibur->delete();

    expect(HariLibur::find($id))->toBeNull()
        ->and(HariLibur::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $hariLibur = HariLibur::factory()->create();
    $id        = $hariLibur->id;

    $hariLibur->delete();
    HariLibur::withTrashed()->find($id)->restore();

    expect(HariLibur::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat hari libur dibuat', function () {
    $hariLibur = HariLibur::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', HariLibur::class)
        ->where('auditable_id', $hariLibur->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat hari libur diperbarui', function () {
    $hariLibur = HariLibur::factory()->create(['keterangan' => 'Libur Lama']);
    $hariLibur->update(['keterangan' => 'Libur Baru']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', HariLibur::class)
        ->where('auditable_id', $hariLibur->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['keterangan'])->toBe('Libur Lama')
        ->and($log->new_values['keterangan'])->toBe('Libur Baru');
});

it('mencatat audit log saat hari libur dihapus', function () {
    $hariLibur = HariLibur::factory()->create();
    $id        = $hariLibur->id;
    $hariLibur->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', HariLibur::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
