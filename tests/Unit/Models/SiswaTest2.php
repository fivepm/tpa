<?php

use App\Models\AuditLog;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    $siswa = new Siswa;
    expect($siswa->getFillable())->toContain('nama', 'nis', 'kelas_id', 'orangtua_id');
});

it('menggunakan tabel siswa', function () {
    expect((new Siswa)->getTable())->toBe('siswa');
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat siswa baru', function () {
    $siswa = Siswa::factory()->create();

    expect(Siswa::count())->toBe(1)
        ->and($siswa->nama)->toBeString()
        ->and($siswa->nis)->toBeString();
});

it('dapat memperbarui data siswa', function () {
    $siswa = Siswa::factory()->create();
    $siswa->update(['nama' => 'Nama Baru Test']);

    expect($siswa->fresh()->nama)->toBe('Nama Baru Test');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsTo ke Kelas', function () {
    $siswa = Siswa::factory()->create();

    expect($siswa->kelas)->toBeInstanceOf(Kelas::class)
        ->and($siswa->kelas->id_kelas)->toBe($siswa->kelas_id);
});

it('memiliki relasi belongsTo ke User sebagai orangtua', function () {
    $siswa = Siswa::factory()->create();

    expect($siswa->orangtua)->toBeInstanceOf(User::class)
        ->and($siswa->orangtua->role)->toBe('orangtua');
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $siswa = Siswa::factory()->create();
    $id    = $siswa->id;

    $siswa->delete();

    expect(Siswa::find($id))->toBeNull()                    // query normal: tidak kelihatan
        ->and(Siswa::withTrashed()->find($id))->not->toBeNull() // withTrashed: masih ada
        ->and(Siswa::withTrashed()->find($id)->deleted_at)->not->toBeNull();
});

it('soft delete dapat dipulihkan dengan restore', function () {
    $siswa = Siswa::factory()->create();
    $id    = $siswa->id;

    $siswa->delete();
    Siswa::withTrashed()->find($id)->restore();

    expect(Siswa::find($id))->not->toBeNull()
        ->and(Siswa::find($id)->deleted_at)->toBeNull();
});

it('withTrashed mengembalikan semua record termasuk yang dihapus', function () {
    Siswa::factory()->count(2)->create();
    $deleted = Siswa::factory()->create();
    $deleted->delete();

    expect(Siswa::count())->toBe(2)
        ->and(Siswa::withTrashed()->count())->toBe(3)
        ->and(Siswa::onlyTrashed()->count())->toBe(1);
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat siswa dibuat', function () {
    $siswa = Siswa::factory()->create();

    $log = AuditLog::where('event', 'created')
        ->where('auditable_type', Siswa::class)
        ->where('auditable_id', $siswa->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->new_values)->toHaveKey('nama')
        ->and($log->new_values['nis'])->toBe($siswa->nis)
        ->and($log->old_values)->toBeNull();
});

it('mencatat audit log saat siswa diperbarui dengan old dan new values', function () {
    $siswa    = Siswa::factory()->create(['nama' => 'Nama Lama']);
    $namaLama = $siswa->nama;

    $siswa->update(['nama' => 'Nama Baru']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', Siswa::class)
        ->where('auditable_id', $siswa->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['nama'])->toBe('Nama Lama')
        ->and($log->new_values['nama'])->toBe('Nama Baru');
});

it('mencatat audit log saat siswa dihapus', function () {
    $siswa = Siswa::factory()->create();
    $id    = $siswa->id;
    $siswa->delete();

    $log = AuditLog::where('event', 'deleted')
        ->where('auditable_type', Siswa::class)
        ->where('auditable_id', $id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values)->toHaveKey('nama')
        ->and($log->new_values)->toBeNull();
});

it('mencatat user_id dan user_name saat ada user yang login', function () {
    $pengurus = User::factory()->pengurus()->create();
    Auth::login($pengurus);

    $siswa = Siswa::factory()->create();

    $log = AuditLog::where('event', 'created')
        ->where('auditable_type', Siswa::class)
        ->where('auditable_id', $siswa->id)
        ->first();

    expect($log->user_id)->toBe($pengurus->id)
        ->and($log->user_name)->toBe($pengurus->nama);
});

it('audit log tidak menyimpan field updated_at', function () {
    $siswa = Siswa::factory()->create();

    $log = AuditLog::where('event', 'created')
        ->where('auditable_type', Siswa::class)
        ->where('auditable_id', $siswa->id)
        ->first();

    expect($log->new_values)->not->toHaveKey('updated_at');
});
