<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Pengujian Tampil Data (Read)
it('pengurus dapat melihat halaman daftar pengguna', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    
    $response = $this->actingAs($pengurus)
                     ->get(route('pengurus.kelola-pengurus.index'));

    $response->assertStatus(200);
});

// Pengujian Tambah Data (Create)
it('pengurus dapat membuat akun pengguna baru', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);

    $payload = [
        'nama'                  => 'Ustadz Irvan',
        'username'              => 'irvan',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->actingAs($pengurus)
                     ->post(route('pengurus.kelola-pengurus.store'), $payload);

    $response->assertRedirect(route('pengurus.kelola-pengurus.index'));
    $this->assertDatabaseHas('users', [
        'username' => 'irvan',
    ]);
});

// Pengujian Ubah Data (Update)
it('pengurus dapat memperbarui data profil pengguna', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $user     = User::factory()->create(['nama' => 'Nama Lama']);

    $response = $this->actingAs($pengurus)
                     ->put(route('pengurus.kelola-pengurus.update', $user->id), [
                         'nama'     => 'Nama Baru Diubah',
                         'username' => $user->username,
                         'email'    => $user->email,
                         'role'     => $user->role,
                     ]);

    $response->assertRedirect(route('pengurus.kelola-pengurus.index'));
    expect($user->fresh()->nama)->toBe('Nama Baru Diubah');
});

// Pengujian Hapus Data (Soft Delete)
it('pengurus dapat menghapus pengguna secara soft delete', function () {
    $pengurus = User::factory()->create(['role' => 'pengurus']);
    $user     = User::factory()->create();

    $response = $this->actingAs($pengurus)
                     ->delete(route('pengurus.kelola-pengurus.destroy', $user->id));

    $response->assertRedirect(route('pengurus.kelola-pengurus.index'));
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});