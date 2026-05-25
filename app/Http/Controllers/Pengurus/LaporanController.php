<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Perkembangan;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $semuaKelas = Kelas::with('siswa')->orderBy('nama_kelas')->get();
        $hariLibur = HariLibur::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->pluck('tanggal')->map(fn($t) => Carbon::parse($t)->toDateString());

        $rekapKelas = [];
        $totalHadirSekolah = 0;
        $totalHariEfektifSekolah = 0;

        foreach ($semuaKelas as $kelas) {
            $jadwalPerHari = Jadwal::where('kelas_id', $kelas->id_kelas)->get()->groupBy('hari');

            $totalHariEfektifKelas = 0;
            for ($date = $tanggalAwal->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
                if ($hariLibur->contains($date->toDateString())) continue;
                $namaHari = $date->locale('id')->dayName;
                if ($jadwalPerHari->has($namaHari)) {
                }
            }

            if ($totalHariEfektifKelas == 0 || $kelas->siswa->isEmpty()) {
                $rekapKelas[] = ['kelas' => $kelas, 'persentase_kehadiran' => 0, 'total_siswa' => $kelas->siswa->count()];
                continue;
            }

            $totalPersentaseSiswaDiKelas = 0;
            $totalHadirDiKelas = 0;
            foreach ($kelas->siswa as $siswa) {
                $jumlahHadir = Presensi::where('siswa_id', $siswa->id)
                    ->where('status', 'hadir')
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->count();

                $persentaseSiswa = ($totalHariEfektifKelas > 0) ? ($jumlahHadir / $totalHariEfektifKelas) * 100 : 0;
                $totalPersentaseSiswaDiKelas += $persentaseSiswa;
                $totalHadirDiKelas += $jumlahHadir;
            }

            $rataRataKelas = $kelas->siswa->count() > 0 ? $totalPersentaseSiswaDiKelas / $kelas->siswa->count() : 0;
            $rekapKelas[] = ['kelas' => $kelas, 'persentase_kehadiran' => round($rataRataKelas), 'total_siswa' => $kelas->siswa->count()];

            $totalHadirSekolah += $totalHadirDiKelas;
            $totalHariEfektifSekolah += ($totalHariEfektifKelas * $kelas->siswa->count());
        }

        $persentaseSekolah = ($totalHariEfektifSekolah > 0) ? round(($totalHadirSekolah / $totalHariEfektifSekolah) * 100) : 0;
        return view('pengurus.laporan.index', compact('rekapKelas', 'bulan', 'tahun', 'persentaseSekolah'));
    }

    public function showKelas(Request $request, Kelas $kelas)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $semuaSiswa = $kelas->siswa()->orderBy('nama')->get();
        $hariLibur = HariLibur::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->pluck('tanggal')->map(fn($t) => Carbon::parse($t)->toDateString());
        $jadwalPerHari = Jadwal::where('kelas_id', $kelas->id_kelas)->with('materi')->get()->groupBy('hari');

        $daftarHari = [];
        for ($date = $tanggalAwal->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
            $namaHari = $date->locale('id')->dayName;
            if (!$hariLibur->contains($date->toDateString()) && $jadwalPerHari->has($namaHari)) {
                $daftarHari[] = $date->copy();
            }
        }
        $totalHariEfektifKelas = count($daftarHari);

        $presensiBulanIni = Presensi::whereIn('siswa_id', $semuaSiswa->pluck('id'))
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->get()->groupBy('siswa_id');

        $rekapSiswa = [];
        foreach ($semuaSiswa as $siswa) {
            $presensiSiswa = $presensiBulanIni->get($siswa->id, collect());
            $hadir = $presensiSiswa->where('status', 'hadir')->count();

            $riwayatHarian = [];
            foreach ($daftarHari as $hari) {
                $presensiDiHariItu = $presensiSiswa->first(fn($p) => Carbon::parse($p->tanggal)->isSameDay($hari));
                $riwayatHarian[$hari->toDateString()] = $presensiDiHariItu ? $presensiDiHariItu->status : 'kosong';
            }

            $rekapSiswa[] = [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'sakit' => $presensiSiswa->where('status', 'sakit')->count(),
                'izin' => $presensiSiswa->where('status', 'izin')->count(),
                'alfa' => $presensiSiswa->where('status', 'alfa')->count(),
                'persentase' => ($totalHariEfektifKelas > 0) ? round(($hadir / $totalHariEfektifKelas) * 100) : 0,
                'riwayat_harian' => $riwayatHarian,
            ];
        }

        return view('pengurus.laporan.show-kelas', compact('kelas', 'rekapSiswa', 'daftarHari', 'bulan', 'tahun'));
    }

    public function showSiswa(Request $request, Siswa $siswa)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $siswa->load('kelas');

        $jadwalPerHari = Jadwal::where('kelas_id', $siswa->kelas_id)
            ->with('materi')
            ->get()->groupBy('hari');

        $presensiHarian = Presensi::where('siswa_id', $siswa->id)
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->get();

        $logPresensi = collect();
        foreach ($presensiHarian as $presensi) {
            $tanggal = Carbon::parse($presensi->tanggal);
            $namaHari = $tanggal->locale('id')->dayName;
            $detailMateri = 'Kehadiran Harian';

            if ($jadwalPerHari->has($namaHari)) {
                $daftarMateriHariIni = $jadwalPerHari[$namaHari]->map(function ($jadwal) {
                    return $jadwal->materi->nama_materi . ' (' . Carbon::parse($jadwal->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($jadwal->jam_selesai)->format('H:i') . ')';
                })->implode(', ');
                $detailMateri = 'Materi Hari Ini: ' . $daftarMateriHariIni;
            }

            $logPresensi->push((object)[
                'tanggal' => $tanggal,
                'tipe' => 'presensi',
                'detail' => $detailMateri,
                'status' => $presensi->status,
            ]);
        }

        $logPerkembangan = Perkembangan::where('siswa_id', $siswa->id)
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->with('guru', 'jadwal.materi')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal' => Carbon::parse($item->tanggal),
                    'tipe' => 'perkembangan',
                    'detail' => $item->catatan,
                    'penilaian' => $item->penilaian,
                    'guru' => $item->guru->nama,
                    'materi' => $item->jadwal?->materi->nama_materi,
                    'jam' => $item->jadwal ? Carbon::parse($item->jadwal->jam_mulai)->format('H:i') : null,
                ];
            });

        $logBulanan = $logPresensi->concat($logPerkembangan)->sortBy('tanggal');

        return view('pengurus.laporan.show-siswa', compact('siswa', 'logBulanan', 'bulan', 'tahun'));
    }

    public function exportKelasPdf(Request $request, Kelas $kelas)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $data = $this->getDataForLaporanKelas($kelas, $bulan, $tahun);
        $namaFile = 'Laporan Kehadiran - ' . $kelas->nama_kelas . ' - ' . Carbon::create(null, $bulan, 1)->translatedFormat('F') . ' ' . $tahun . '.pdf';

        $pdf = PDF::loadView('pengurus.laporan.pdf.kelas-pdf', $data);
        return $pdf->stream($namaFile);
    }

    private function getDataForLaporanKelas(Kelas $kelas, int $bulan, int $tahun)
    {
        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $semuaSiswa = $kelas->siswa()->orderBy('nama')->get();
        $hariLibur = HariLibur::whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->pluck('tanggal')->map(fn($t) => Carbon::parse($t)->toDateString());
        $jadwalPerHari = Jadwal::where('kelas_id', $kelas->id_kelas)->get()->groupBy('hari');

        $daftarHari = [];
        $totalHariEfektifKelas = 0;
        for ($date = $tanggalAwal->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
            $namaHari = $date->locale('id')->dayName;
            if (!$hariLibur->contains($date->toDateString()) && $jadwalPerHari->has($namaHari)) {
                $daftarHari[] = $date->copy();
                $totalHariEfektifKelas++;
            }
        }

        $presensiBulanIni = Presensi::whereIn('siswa_id', $semuaSiswa->pluck('id'))
            ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
            ->get()->groupBy('siswa_id');

        $rekapSiswa = [];
        foreach ($semuaSiswa as $siswa) {
            $presensiSiswa = $presensiBulanIni->get($siswa->id, collect());
            $hadir = $presensiSiswa->where('status', 'hadir')->count();

            $riwayatHarian = [];
            foreach ($daftarHari as $hari) {
                $presensiDiHariItu = $presensiSiswa->first(fn($p) => Carbon::parse($p->tanggal)->isSameDay($hari));
                $riwayatHarian[$hari->toDateString()] = $presensiDiHariItu ? $presensiDiHariItu->status : 'kosong';
            }

            $rekapSiswa[] = [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'sakit' => $presensiSiswa->where('status', 'sakit')->count(),
                'izin' => $presensiSiswa->where('status', 'izin')->count(),
                'alfa' => $presensiSiswa->where('status', 'alfa')->count(),
                'persentase' => ($totalHariEfektifKelas > 0) ? round(($hadir / $totalHariEfektifKelas) * 100) : 0,
                'riwayat_harian' => $riwayatHarian,
            ];
        }

        return compact('kelas', 'rekapSiswa', 'daftarHari', 'bulan', 'tahun');
    }
}
