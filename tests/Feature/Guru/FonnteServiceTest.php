<?php

use App\Services\FonnteService;
use Illuminate\Support\Facades\Http;

it('berhasil mengirim pesan jika nomor valid dan merespons sukses', function () {
    Http::fake([
        '*' => Http::response(['status' => true], 200),
    ]);

    $service = new FonnteService();
    $result = $service->send('08123456789', 'Halo, ini pesan test TDD.');

    expect($result)->toBeTrue();
});

it('gagal mengirim pesan jika format nomor kosong', function () {
    $service = new FonnteService();
    $result = $service->send('', 'Test Pesan');

    expect($result)->toBeFalse();
});

it('gagal mengirim pesan jika respons API Fonnte error', function () {
    Http::fake([
        '*' => Http::response(['status' => false], 500),
    ]);

    $service = new FonnteService();
    $result = $service->send('08123456789', 'Test pesan gagal.');

    expect($result)->toBeFalse();
});