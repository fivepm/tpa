<?php

use App\Models\AuditLog;
use App\Models\Kelas;
use App\Models\User;
use App\Models\WaliKelas;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    expect((new WaliKelas)->getFillable())->toContain('kelas_id', 'user_id');
});

it('menggunakan tabel wali_kelas', function () {
    expect((new WaliKelas)->getTable())->toBe('wali_kelas');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat wali kelas baru', function () {
    $waliKelas = WaliKelas::factory()->create();

    expect(WaliKelas::count())->toBe(1);
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsTo ke Kelas', function () {
    $waliKelas = WaliKelas::factory()->create();

    expect($waliKelas->kelas)->toBeInstanceOf(Kelas::class);
});

it('memiliki relasi belongsTo ke User (guru)', function () {
    $waliKelas = WaliKelas::factory()->create();

    expect($waliKelas->guru)->toBeInstanceOf(User::class)
        ->and($waliKelas->guru->role)->toBe('guru');
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $waliKelas = WaliKelas::factory()->create();
    $id        = $waliKelas->id;

    $waliKelas->delete();

    expect(WaliKelas::find($id))->toBeNull()
        ->and(WaliKelas::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $waliKelas = WaliKelas::factory()->create();
    $id        = $waliKelas->id;

    $waliKelas->delete();
    WaliKelas::withTrashed()->find($id)->restore();

    expect(WaliKelas::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat wali kelas dibuat', function () {
    $waliKelas = WaliKelas::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', WaliKelas::class)
        ->where('auditable_id', $waliKelas->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat wali kelas dihapus', function () {
    $waliKelas = WaliKelas::factory()->create();
    $id        = $waliKelas->id;
    $waliKelas->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', WaliKelas::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
