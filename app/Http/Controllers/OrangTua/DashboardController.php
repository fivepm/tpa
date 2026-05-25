<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Perkembangan;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $anakList = Siswa::where('orangtua_id', $user->id)->with('kelas')->get();

        if ($anakList->isEmpty()) {
            return view('orangtua.dashboard_no_child');
        }

        $selectedAnakId = $request->input('anak_id', $anakList->first()->id);
        $selectedAnak = $anakList->where('id', $selectedAnakId)->first() ?? $anakList->first();

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $presensi = Presensi::where('siswa_id', $selectedAnak->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $perkembangan = Perkembangan::where('siswa_id', $selectedAnak->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('guru', 'jadwal.materi')
            ->orderBy('tanggal', 'desc')
            ->get();

        $jurnalMengajar = \App\Models\JurnalMengajar::whereHas('jadwal', function ($query) use ($selectedAnak) {
                $query->where('kelas_id', $selectedAnak->kelas_id);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with('guru', 'jadwal.materi')
            ->get();

        $jadwalPerHari = Jadwal::where('kelas_id', $selectedAnak->kelas_id)
            ->with('materi')
            ->get()->groupBy('hari');

        $logGabungan = collect();

        foreach ($presensi as $p) {
            $tgl = Carbon::parse($p->tanggal);
            $namaHari = $tgl->locale('id')->dayName;
            $detail = "Kehadiran Harian";

            if ($jadwalPerHari->has($namaHari)) {
                $materi = $jadwalPerHari[$namaHari]->map(fn($j) => $j->materi->nama_materi . " (" . Carbon::parse($j->jam_mulai)->format('H:i') . ")")->implode(', ');
                $detail = "Jadwal Materi: " . $materi;
            }

            $logGabungan->push((object)[
                'tanggal' => $tgl,
                'tipe' => 'presensi',
                'status' => $p->status,
                'detail' => $detail
            ]);
        }

        foreach ($perkembangan as $pk) {
            $logGabungan->push((object)[
                'tanggal' => Carbon::parse($pk->tanggal),
                'tipe' => 'perkembangan',
                'catatan' => $pk->catatan,
                'penilaian' => $pk->penilaian,
                'guru' => $pk->guru->nama,
                'materi' => $pk->jadwal?->materi->nama_materi,
                'jam' => $pk->jadwal ? Carbon::parse($pk->jadwal->jam_mulai)->format('H:i') : null
            ]);
        }

        foreach ($jurnalMengajar as $jurnal) {
            $logGabungan->push((object)[
                'tanggal' => Carbon::parse($jurnal->tanggal),
                'tipe' => 'jurnal',
                'materi_harian' => $jurnal->materi_harian,
                'keterangan' => $jurnal->keterangan,
                'guru' => $jurnal->guru->nama,
                'materi' => $jurnal->jadwal?->materi->nama_materi,
                'jam' => $jurnal->jadwal ? Carbon::parse($jurnal->jadwal->jam_mulai)->format('H:i') : null
            ]);
        }

        $logBulanan = $logGabungan->sortByDesc('tanggal');

        return view('orangtua.dashboard', compact('selectedAnak', 'logBulanan', 'bulan', 'tahun', 'anakList'));
    }
}
