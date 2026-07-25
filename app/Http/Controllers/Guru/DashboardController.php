<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\JurnalMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guruId = Auth::id();
        $todayStr = Carbon::today(config('app.timezone'))->locale('id')->dayName;

        $jadwalHariIni = Jadwal::with(['kelas', 'materi'])
            ->where('guru_id', $guruId)
            ->where('hari', $todayStr)
            ->orderBy('jam_mulai')
            ->get();

        $totalJadwalHariIni = $jadwalHariIni->count();

        $totalKelas = Jadwal::where('guru_id', $guruId)
            ->distinct('kelas_id')
            ->count('kelas_id');

        $jurnalBulanIni = JurnalMengajar::whereHas('jadwal', function ($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            })
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->count();

        $jurnalTerbaru = JurnalMengajar::with(['jadwal.kelas', 'jadwal.materi'])
            ->whereHas('jadwal', function ($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('guru.dashboard', compact(
            'totalJadwalHariIni',
            'totalKelas',
            'jurnalBulanIni',
            'jadwalHariIni',
            'jurnalTerbaru',
            'todayStr'
        ));
    }
}
