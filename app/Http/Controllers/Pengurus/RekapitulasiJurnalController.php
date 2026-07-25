<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Perkembangan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RekapitulasiJurnalController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $semuaKelas = Kelas::with('siswa')->orderBy('nama_kelas')->get();

        $rekapKelas = [];

        foreach ($semuaKelas as $kelas) {
            $siswaIds = $kelas->siswa->pluck('id');

            $totalJurnal = Perkembangan::whereIn('siswa_id', $siswaIds)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->count();

            $totalSiswaAdaJurnal = Perkembangan::whereIn('siswa_id', $siswaIds)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->distinct('siswa_id')
                ->count('siswa_id');

            $distribusiPenilaian = Perkembangan::whereIn('siswa_id', $siswaIds)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->selectRaw('penilaian, COUNT(*) as jumlah')
                ->groupBy('penilaian')
                ->pluck('jumlah', 'penilaian');

            $rekapKelas[] = [
                'kelas'                 => $kelas,
                'total_siswa'           => $kelas->siswa->count(),
                'total_jurnal'          => $totalJurnal,
                'siswa_ada_jurnal'      => $totalSiswaAdaJurnal,
                'distribusi_penilaian'  => $distribusiPenilaian,
            ];
        }

        return view('pengurus.rekap-jurnal.index', compact('rekapKelas', 'bulan', 'tahun'));
    }

    public function showKelas(Request $request, Kelas $kelas)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $semuaSiswa = $kelas->siswa()->orderBy('nama')->get();
        $siswaIds   = $semuaSiswa->pluck('id');

        $semuaJurnal = Perkembangan::whereIn('siswa_id', $siswaIds)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->with(['siswa', 'guru', 'jadwal.materi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $rekapSiswa = [];
        foreach ($semuaSiswa as $siswa) {
            $jurnalSiswa = $semuaJurnal->where('siswa_id', $siswa->id);

            $distribusi = [
                'Sangat Baik'      => $jurnalSiswa->where('penilaian', 'Sangat Baik')->count(),
                'Baik'             => $jurnalSiswa->where('penilaian', 'Baik')->count(),
                'Cukup'            => $jurnalSiswa->where('penilaian', 'Cukup')->count(),
                'Perlu Bimbingan'  => $jurnalSiswa->where('penilaian', 'Perlu Bimbingan')->count(),
            ];

            $rekapSiswa[] = [
                'siswa'       => $siswa,
                'total'       => $jurnalSiswa->count(),
                'distribusi'  => $distribusi,
                'jurnal'      => $jurnalSiswa->sortByDesc('tanggal'),
            ];
        }

        return view('pengurus.rekap-jurnal.show-kelas', compact(
            'kelas', 'rekapSiswa', 'bulan', 'tahun'
        ));
    }
}
