<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $guruId = Auth::id();
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $startDate = Carbon::createFromDate($tahun, $bulan, 1, config('app.timezone'))->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $today = Carbon::today(config('app.timezone'));
        $capDate = $endDate->lessThanOrEqualTo($today) ? $endDate : $today;
        
        $dates = [];
        if ($startDate->lessThanOrEqualTo($today)) {
             for ($date = $startDate->copy(); $date->lte($capDate); $date->addDay()) {
                 $dates[] = $date->copy();
             }
        }

        $jadwalGuru = Jadwal::where('guru_id', $guruId)
            ->with('kelas.siswa')
            ->get();
            
        $jadwalByHari = $jadwalGuru->groupBy(function ($j) {
            return strtolower($j->hari);
        });

        $jadwalPerTanggal = [];

        foreach ($dates as $date) {
            $namaHari = strtolower($date->locale('id')->dayName);
            $tanggalStr = $date->toDateString();
            
            if ($jadwalByHari->has($namaHari)) {
                $hariLibur = HariLibur::where('tanggal', $tanggalStr)->first();
                
                $kelasHariIni = collect();
                if (!$hariLibur) {
                    $kelasHariIni = $jadwalByHari->get($namaHari)->pluck('kelas')->unique('id_kelas')->sortBy('nama_kelas');
                    $kelasData = collect();
                    foreach ($kelasHariIni as $kelas) {
                        $kelasClone = clone $kelas;
                        $siswaPertama = $kelasClone->siswa->first();
                        $sudahDiambil = false;
                        if ($siswaPertama) {
                            $sudahDiambil = Presensi::where('siswa_id', $siswaPertama->id)
                                ->whereDate('tanggal', $tanggalStr)
                                ->exists();
                        }
                        
                        $kelasClone->presensi_sudah_diambil = $sudahDiambil;
                        $kelasData->push($kelasClone);
                    }
                    $kelasHariIni = $kelasData;
                }

                $jadwalPerTanggal[$tanggalStr] = [
                    'date' => $date,
                    'namaHari' => ucfirst($namaHari),
                    'hariLibur' => $hariLibur,
                    'kelas' => $kelasHariIni
                ];
            }
        }
        
        krsort($jadwalPerTanggal);

        return view('guru.presensi.index', compact('jadwalPerTanggal', 'bulan', 'tahun'));
    }

    public function show(Request $request, Kelas $kelas)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());
        $siswaDiKelas = $kelas->siswa()->orderBy('nama')->get();
        $presensiHariIni = Presensi::whereIn('siswa_id', $siswaDiKelas->pluck('id'))
            ->whereDate('tanggal', $tanggal)
            ->pluck('status', 'siswa_id');

        return view('guru.presensi.show', compact('kelas', 'siswaDiKelas', 'presensiHariIni', 'tanggal'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $request->validate([
            'presensi' => 'required|array',
            'tanggal'  => 'required|date'
        ]);
        
        $tanggal = $request->input('tanggal');

        DB::transaction(function () use ($request, $kelas, $tanggal) {
            foreach ($request->presensi as $siswaId => $status) {
                Presensi::updateOrCreate(
                    ['siswa_id' => $siswaId, 'tanggal' => $tanggal],
                    ['status'   => $status]
                );
            }
        });

        $this->kirimNotifikasiPresensi($request->presensi, $tanggal);

        return redirect()->route('guru.presensi.index', [
            'bulan' => Carbon::parse($tanggal)->format('m'),
            'tahun' => Carbon::parse($tanggal)->format('Y')
        ])->with('success', 'Presensi kelas ' . $kelas->nama_kelas . ' tanggal ' . Carbon::parse($tanggal)->translatedFormat('d F Y') . ' berhasil disimpan.');
    }

    private function kirimNotifikasiPresensi(array $presensiData, string $tanggal): void
    {
        $siswaIds = array_keys($presensiData);
        $siswaList = Siswa::whereIn('id', $siswaIds)
            ->with('orangtua')
            ->get()
            ->keyBy('id');

        $fonnte  = new FonnteService();
        $tglFmt  = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');

        foreach ($presensiData as $siswaId => $status) {
            $siswa = $siswaList->get($siswaId);
            if (!$siswa || !$siswa->orangtua || empty($siswa->orangtua->no_hp)) {
                continue;
            }

            $statusLabel = match($status) {
                'hadir' => '✅ Hadir',
                'sakit' => '🤒 Sakit',
                'izin'  => '📋 Izin',
                'alfa'  => '❌ Tidak Hadir (Alfa)',
                default => ucfirst($status),
            };

            $pesan = "📋 *Info Presensi TPA*\n"
                   . "Assalamu'alaikum Bapak/Ibu,\n\n"
                   . "Presensi *{$siswa->nama}* pada *{$tglFmt}* telah dicatat.\n"
                   . "Status: {$statusLabel}\n\n"
                   . "Silakan cek dashboard untuk informasi lebih lengkap.";

            $fonnte->send($siswa->orangtua->no_hp, $pesan);
        }
    }
}
