<?php

use App\Models\AuditLog;
use App\Models\Kelas;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    $fillable = (new User)->getFillable();
    expect($fillable)->toContain('nama', 'username', 'email', 'password', 'role');
});

it('menyembunyikan field password dan remember_token', function () {
    $hidden = (new User)->getHidden();
    expect($hidden)->toContain('password', 'remember_token');
});

// ─────────────────────────────────────────────
// CRUD & Role
// ─────────────────────────────────────────────

it('dapat membuat user dengan role guru', function () {
    $guru = User::factory()->guru()->create();

    expect($guru->role)->toBe('guru')
        ->and(User::where('role', 'guru')->count())->toBe(1);
});

it('dapat membuat user dengan role orangtua', function () {
    $orangtua = User::factory()->orangtua()->create();
    expect($orangtua->role)->toBe('orangtua');
});

it('dapat membuat user dengan role pengurus', function () {
    $pengurus = User::factory()->pengurus()->create();
    expect($pengurus->role)->toBe('pengurus');
});

it('password tersimpan dalam bentuk hash', function () {
    $user = User::factory()->create();
    expect(Hash::check('password', $user->password))->toBeTrue();
});

it('dapat memperbarui data user', function () {
    $user = User::factory()->create();
    $user->update(['nama' => 'Nama Baru']);

    expect($user->fresh()->nama)->toBe('Nama Baru');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsToMany ke Kelas', function () {
    $guru  = User::factory()->guru()->create();
    $kelas = Kelas::factory()->create();
    $guru->kelas()->attach($kelas->id_kelas);

    expect($guru->kelas)->toHaveCount(1)
        ->and($guru->kelas->first())->toBeInstanceOf(Kelas::class);
});

it('memiliki relasi hasMany ke WaliKelas', function () {
    $guru  = User::factory()->guru()->create();
    $kelas = Kelas::factory()->create();
    WaliKelas::create(['user_id' => $guru->id, 'kelas_id' => $kelas->id_kelas]);

    expect($guru->waliKelas)->toHaveCount(1)
        ->and($guru->waliKelas->first())->toBeInstanceOf(WaliKelas::class);
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus user dari database', function () {
    $user = User::factory()->create();
    $id   = $user->id;

    $user->delete();

    expect(User::find($id))->toBeNull()
        ->and(User::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete user dapat dipulihkan', function () {
    $user = User::factory()->create();
    $id   = $user->id;

    $user->delete();
    User::withTrashed()->find($id)->restore();

    expect(User::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat user dibuat', function () {
    $user = User::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', User::class)
        ->where('auditable_id', $user->id)
        ->exists()
    )->toBeTrue();
});

it('tidak menyimpan password di audit log', function () {
    $user = User::factory()->create();

    $log = AuditLog::where('event', 'created')
        ->where('auditable_type', User::class)
        ->where('auditable_id', $user->id)
        ->first();

    expect($log->new_values)->not->toHaveKey('password')
        ->and($log->new_values)->not->toHaveKey('remember_token');
});

it('mencatat audit log saat user dihapus', function () {
    $user = User::factory()->create();
    $id   = $user->id;
    $user->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', User::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
