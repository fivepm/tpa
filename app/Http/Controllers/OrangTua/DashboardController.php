<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Perkembangan;
use App\Models\JurnalMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user     = Auth::user();
        $anakList = Siswa::where('orangtua_id', $user->id)->with('kelas')->get();

        if ($anakList->isEmpty()) {
            return view('orangtua.dashboard_no_child');
        }

        $selectedAnakId = $request->input('anak_id', $anakList->first()->id);
        $selectedAnak   = $anakList->where('id', $selectedAnakId)->first() ?? $anakList->first();

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $presensi = Presensi::where('siswa_id', $selectedAnak->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('jadwal.materi')
            ->get();

        $kelasIdsPeriode = $presensi
            ->pluck('jadwal.kelas_id')
            ->unique()
            ->filter()
            ->values();

        if ($kelasIdsPeriode->isEmpty()) {
            $kelasIdsPeriode = collect([$selectedAnak->kelas_id]);
        }

        $perkembangan = Perkembangan::where('siswa_id', $selectedAnak->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('guru', 'jadwal.materi')
            ->orderBy('tanggal', 'desc')
            ->get();

        $jurnalMengajar = JurnalMengajar::whereHas('jadwal', function ($q) use ($kelasIdsPeriode) {
                $q->whereIn('kelas_id', $kelasIdsPeriode);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('guru', 'jadwal.materi')
            ->get();
        $logGabungan = collect();

        foreach ($presensi as $p) {
            $tgl    = Carbon::parse($p->tanggal);
            $detail = 'Kehadiran Harian';

            if ($p->jadwal && $p->jadwal->materi) {
                $detail = 'Materi: ' . $p->jadwal->materi->nama_materi
                    . ' (' . Carbon::parse($p->jadwal->jam_mulai)->format('H:i') . ')';
            }

            $logGabungan->push((object)[
                'tanggal' => $tgl,
                'tipe'    => 'presensi',
                'status'  => $p->status,
                'detail'  => $detail,
            ]);
        }

        foreach ($perkembangan as $pk) {
            $logGabungan->push((object)[
                'tanggal'   => Carbon::parse($pk->tanggal),
                'tipe'      => 'perkembangan',
                'catatan'   => $pk->catatan,
                'penilaian' => $pk->penilaian,
                'guru'      => $pk->guru->nama,
                'materi'    => $pk->jadwal?->materi->nama_materi,
                'jam'       => $pk->jadwal ? Carbon::parse($pk->jadwal->jam_mulai)->format('H:i') : null,
            ]);
        }

        foreach ($jurnalMengajar as $jurnal) {
            $logGabungan->push((object)[
                'tanggal'   => Carbon::parse($jurnal->tanggal),
                'tipe'      => 'jurnal',
                'topik'     => $jurnal->topik,
                'metode'    => $jurnal->metode,
                'ringkasan' => $jurnal->ringkasan,
                'catatan'   => $jurnal->catatan,
                'guru'      => $jurnal->guru->nama,
                'materi'    => $jurnal->jadwal?->materi->nama_materi,
                'jam'       => $jurnal->jadwal ? Carbon::parse($jurnal->jadwal->jam_mulai)->format('H:i') : null,
            ]);
        }

        $logBulanan = $logGabungan->sortByDesc('tanggal');

        $jurnalHarian = JurnalMengajar::whereHas('jadwal', function ($q) use ($kelasIdsPeriode) {
                $q->whereIn('kelas_id', $kelasIdsPeriode);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('guru', 'jadwal.materi')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy(fn($j) => Carbon::parse($j->tanggal)->toDateString());

        return view('orangtua.dashboard', compact(
            'selectedAnak', 'logBulanan', 'bulan', 'tahun', 'anakList', 'jurnalHarian'
        ));
    }
}
