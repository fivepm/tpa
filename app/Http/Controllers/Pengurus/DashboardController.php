<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\JurnalMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $todayStr = Carbon::today(config('app.timezone'))->locale('id')->dayName;

        // Metrik utama
        $totalGuru = User::where('role', 'guru')->count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalJadwalHariIni = Jadwal::where('hari', $todayStr)->count();

        // Jurnal bulan ini
        $jurnalBulanIni = JurnalMengajar::whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->count();

        // Jadwal hari ini lengkap dengan relasi
        $jadwalHariIni = Jadwal::with(['guru', 'kelas', 'materi'])
            ->where('hari', $todayStr)
            ->orderBy('jam_mulai')
            ->get();

        // Jurnal terbaru
        $jurnalTerbaru = JurnalMengajar::with(['jadwal.guru', 'jadwal.kelas', 'jadwal.materi'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pengurus.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'totalJadwalHariIni',
            'jurnalBulanIni',
            'jadwalHariIni',
            'jurnalTerbaru',
            'todayStr'
        ));
    }
}
