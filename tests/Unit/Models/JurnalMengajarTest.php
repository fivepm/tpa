<?php

use App\Models\AuditLog;
use App\Models\Jadwal;
use App\Models\JurnalMengajar;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ─────────────────────────────────────────────
// Struktur Model
// ─────────────────────────────────────────────

it('memiliki fillable yang benar', function () {
    $fillable = (new JurnalMengajar)->getFillable();
    expect($fillable)->toContain('jadwal_id', 'guru_id', 'tanggal', 'topik', 'metode', 'ringkasan', 'catatan');
});

it('menggunakan tabel jurnal_mengajar', function () {
    expect((new JurnalMengajar)->getTable())->toBe('jurnal_mengajar');
});

it('meng-cast kolom tanggal sebagai date', function () {
    $jurnal = JurnalMengajar::factory()->create();
    expect($jurnal->tanggal)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

// ─────────────────────────────────────────────
// CRUD
// ─────────────────────────────────────────────

it('dapat membuat jurnal mengajar baru', function () {
    $jurnal = JurnalMengajar::factory()->create(['topik' => 'Bab 1 Pengantar']);

    expect(JurnalMengajar::count())->toBe(1)
        ->and($jurnal->topik)->toBe('Bab 1 Pengantar');
});

it('dapat memperbarui topik jurnal', function () {
    $jurnal = JurnalMengajar::factory()->create();
    $jurnal->update(['topik' => 'Topik Baru']);

    expect($jurnal->fresh()->topik)->toBe('Topik Baru');
});

// ─────────────────────────────────────────────
// Relasi
// ─────────────────────────────────────────────

it('memiliki relasi belongsTo ke Jadwal', function () {
    $jurnal = JurnalMengajar::factory()->create();

    expect($jurnal->jadwal)->toBeInstanceOf(Jadwal::class);
});

it('memiliki relasi belongsTo ke User sebagai guru', function () {
    $jurnal = JurnalMengajar::factory()->create();

    expect($jurnal->guru)->toBeInstanceOf(User::class)
        ->and($jurnal->guru->role)->toBe('guru');
});

// ─────────────────────────────────────────────
// Soft Delete
// ─────────────────────────────────────────────

it('soft delete tidak menghapus record dari database', function () {
    $jurnal = JurnalMengajar::factory()->create();
    $id     = $jurnal->id;

    $jurnal->delete();

    expect(JurnalMengajar::find($id))->toBeNull()
        ->and(JurnalMengajar::withTrashed()->find($id))->not->toBeNull();
});

it('soft delete dapat dipulihkan', function () {
    $jurnal = JurnalMengajar::factory()->create();
    $id     = $jurnal->id;

    $jurnal->delete();
    JurnalMengajar::withTrashed()->find($id)->restore();

    expect(JurnalMengajar::find($id))->not->toBeNull();
});

// ─────────────────────────────────────────────
// Audit Trail
// ─────────────────────────────────────────────

it('mencatat audit log saat jurnal dibuat', function () {
    $jurnal = JurnalMengajar::factory()->create();

    expect(AuditLog::where('event', 'created')
        ->where('auditable_type', JurnalMengajar::class)
        ->where('auditable_id', $jurnal->id)
        ->exists()
    )->toBeTrue();
});

it('mencatat audit log saat jurnal diperbarui', function () {
    $jurnal = JurnalMengajar::factory()->create(['topik' => 'Topik Lama']);
    $jurnal->update(['topik' => 'Topik Baru']);

    $log = AuditLog::where('event', 'updated')
        ->where('auditable_type', JurnalMengajar::class)
        ->where('auditable_id', $jurnal->id)
        ->first();

    expect($log)->not->toBeNull()
        ->and($log->old_values['topik'])->toBe('Topik Lama')
        ->and($log->new_values['topik'])->toBe('Topik Baru');
});

it('mencatat audit log saat jurnal dihapus', function () {
    $jurnal = JurnalMengajar::factory()->create();
    $id     = $jurnal->id;
    $jurnal->delete();

    expect(AuditLog::where('event', 'deleted')
        ->where('auditable_type', JurnalMengajar::class)
        ->where('auditable_id', $id)
        ->exists()
    )->toBeTrue();
});
