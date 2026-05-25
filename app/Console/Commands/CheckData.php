<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jadwal;

class CheckData extends Command
{
    protected $signature = 'debug:check-data';
    protected $description = 'Verify data integrity for presensi feature';

    public function handle()
    {
        $this->info('Memulai pengecekan integritas data...');

        // Cek Guru
        $guru = User::where('username', 'budi.guru')->first();
        if (!$guru) {
            $this->error('-> GURU "budi.guru" tidak ditemukan!');
            return 1;
        }
        $this->line('-> Guru "budi.guru" ditemukan (ID: ' . $guru->id . ').');

        // Cek koneksi Guru ke Kelas
        $kelasDiajar = $guru->kelas()->count();
        if ($kelasDiajar === 0) {
            $this->error('-> FATAL: Guru "budi.guru" tidak terhubung ke kelas manapun!');
            return 1;
        }
        $this->info('-> SUKSES: Guru "budi.guru" mengajar di ' . $kelasDiajar . ' kelas.');

        // Cek Jadwal
        $jadwal = Jadwal::where('guru_id', $guru->id)->first();
        if (!$jadwal) {
            $this->warn('-> PERINGATAN: Guru "budi.guru" tidak memiliki jadwal sama sekali.');
        } else {
            $this->info('-> SUKSES: Jadwal ditemukan untuk guru "budi.guru".');
            $this->line('   |-> Jadwal ID: ' . $jadwal->id . ', Kelas ID: ' . $jadwal->kelas_id);

            // Cek relasi dari jadwal
            if ($jadwal->kelas && $jadwal->materi) {
                $this->info('   |-> SUKSES: Relasi Jadwal -> Kelas & Materi berhasil dimuat.');
                $this->line('   |   |-> Nama Kelas: ' . $jadwal->kelas->nama_kelas);

                // Cek siswa di dalam kelas dari jadwal tersebut
                $siswaDiKelas = Siswa::where('kelas_id', $jadwal->kelas_id)->count();
                if ($siswaDiKelas === 0) {
                    $this->error('   |   |-> FATAL: Tidak ada siswa yang ditemukan di Kelas ID ' . $jadwal->kelas_id);
                } else {
                    $this->info('   |   |-> SUKSES: Ditemukan ' . $siswaDiKelas . ' siswa di kelas ini.');
                }
            } else {
                $this->error('   |-> FATAL: Relasi Jadwal -> Kelas & Materi GAGAL dimuat!');
            }
        }

        $this->info('Pengecekan selesai.');
        return 0;
    }
}
