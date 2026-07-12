<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public function send(string $noHp, string $pesan): bool
    {
        $target = $this->normalizeNumber($noHp);

        if (empty($target)) {
            Log::warning('FonnteService: nomor HP kosong atau tidak valid.', ['raw' => $noHp]);
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => config('fonnte.token')])
                ->post(config('fonnte.url'), [
                    'target'  => $target,
                    'message' => $pesan,
                ]);

            if ($response->successful()) {
                Log::info('FonnteService: pesan terkirim.', ['target' => $target]);
                return true;
            }

            Log::warning('FonnteService: gagal kirim pesan.', [
                'target' => $target,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('FonnteService: exception saat kirim pesan.', [
                'target'  => $target,
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function normalizeNumber(string $noHp): string
    {
        $noHp = preg_replace('/\D/', '', $noHp);

        if (empty($noHp)) {
            return '';
        }

        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        return $noHp;
    }
}
