<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Perkembangan;
use App\Models\Siswa;
use App\Models\HariLibur;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PerkembanganController extends Controller
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
            ->with(['kelas', 'materi'])
            ->orderBy('jam_mulai')
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
                $jadwalHariIni = collect();
                
                if (!$hariLibur) {
                    $jadwalHariIni = $jadwalByHari->get($namaHari)->map(function ($jadwal) use ($tanggalStr) {
                        $newJadwal = clone $jadwal;
                        $newJadwal->sudah_ada_catatan = Perkembangan::where('jadwal_id', $jadwal->id)
                            ->where('tanggal', $tanggalStr)
                            ->exists();
                        return $newJadwal;
                    });
                }

                $jadwalPerTanggal[$tanggalStr] = [
                    'date' => $date,
                    'namaHari' => ucfirst($namaHari),
                    'hariLibur' => $hariLibur,
                    'jadwal' => $jadwalHariIni
                ];
            }
        }

        krsort($jadwalPerTanggal);

        return view('guru.perkembangan.index', compact('jadwalPerTanggal', 'bulan', 'tahun'));
    }

    public function create(Request $request, Jadwal $jadwal)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());
        $jadwal->load(['kelas.siswa' => fn($q) => $q->orderBy('nama'), 'materi']);
        $penilaianOptions = ['Sangat Baik', 'Baik', 'Cukup', 'Perlu Bimbingan'];

        return view('guru.perkembangan.create', compact('jadwal', 'penilaianOptions', 'tanggal'));
    }

    public function store(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'catatan' => 'required|array',
            'tanggal' => 'required|date'
        ]);
        $tanggal    = $request->input('tanggal');
        $savedItems = [];

        DB::transaction(function () use ($request, $jadwal, $tanggal, &$savedItems) {
            foreach ($request->catatan as $siswaId => $catatan) {
                if (!empty($catatan)) {
                    Perkembangan::create([
                        'siswa_id'  => $siswaId,
                        'jadwal_id' => $jadwal->id,
                        'guru_id'   => Auth::id(),
                        'tanggal'   => $tanggal,
                        'penilaian' => $request->penilaian[$siswaId] ?? null,
                        'catatan'   => $catatan,
                    ]);

                    $savedItems[$siswaId] = [
                        'catatan'   => $catatan,
                        'penilaian' => $request->penilaian[$siswaId] ?? null,
                    ];
                }
            }
        });

        $this->kirimNotifikasiPerkembangan($savedItems, $jadwal, $tanggal);

        return redirect()->route('guru.perkembangan.index', [
            'bulan' => Carbon::parse($tanggal)->format('m'),
            'tahun' => Carbon::parse($tanggal)->format('Y')
        ])->with('success', 'Catatan perkembangan berhasil disimpan.');
    }

    public function edit(Request $request, Jadwal $jadwal)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());
        $jadwal->load(['kelas.siswa' => fn($q) => $q->orderBy('nama'), 'materi']);
        $penilaianOptions = ['Sangat Baik', 'Baik', 'Cukup', 'Perlu Bimbingan'];
        $catatanTersimpan = Perkembangan::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.perkembangan.edit', compact('jadwal', 'penilaianOptions', 'catatanTersimpan', 'tanggal'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'catatan' => 'required|array',
            'tanggal' => 'required|date'
        ]);
        $tanggal = $request->input('tanggal');

        DB::transaction(function () use ($request, $jadwal, $tanggal) {
            foreach ($request->catatan as $siswaId => $catatan) {
                if (!empty($catatan)) {
                    Perkembangan::updateOrCreate(
                        ['siswa_id' => $siswaId, 'jadwal_id' => $jadwal->id, 'tanggal' => $tanggal],
                        ['guru_id' => Auth::id(), 'penilaian' => $request->penilaian[$siswaId] ?? null, 'catatan' => $catatan]
                    );
                } else {
                    Perkembangan::where('siswa_id', $siswaId)
                        ->where('jadwal_id', $jadwal->id)
                        ->where('tanggal', $tanggal)
                        ->delete();
                }
            }
        });

        return redirect()->route('guru.perkembangan.index', [
            'bulan' => Carbon::parse($tanggal)->format('m'),
            'tahun' => Carbon::parse($tanggal)->format('Y')
        ])->with('success', 'Catatan perkembangan berhasil diperbarui.');
    }

    private function kirimNotifikasiPerkembangan(array $savedItems, Jadwal $jadwal, string $tanggal): void
    {
        if (empty($savedItems)) return;

        $jadwal->loadMissing('materi');

        $siswaList = Siswa::whereIn('id', array_keys($savedItems))
            ->with('orangtua')
            ->get()
            ->keyBy('id');

        $fonnte  = new FonnteService();
        $tglFmt  = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY');
        $materi  = $jadwal->materi->nama_materi ?? '—';

        foreach ($savedItems as $siswaId => $data) {
            $siswa = $siswaList->get($siswaId);
            if (!$siswa || !$siswa->orangtua || empty($siswa->orangtua->no_hp)) {
                continue;
            }

            $penilaian = $data['penilaian'] ? "Penilaian: *{$data['penilaian']}*\n" : '';

            $pesan = "📝 *Catatan Perkembangan TPA*\n"
                   . "Assalamu'alaikum Bapak/Ibu,\n\n"
                   . "Terdapat catatan perkembangan untuk *{$siswa->nama}*.\n"
                   . "Tanggal: *{$tglFmt}*\n"
                   . "Materi: *{$materi}*\n"
                   . $penilaian
                   . "Catatan: _{$data['catatan']}_\n\n"
                   . "Silakan cek dashboard untuk informasi lebih lengkap.";

            $fonnte->send($siswa->orangtua->no_hp, $pesan);
        }
    }
}
