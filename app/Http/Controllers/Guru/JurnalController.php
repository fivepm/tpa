<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\JurnalMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
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
                        $newJadwal->sudah_ada_jurnal = JurnalMengajar::where('jadwal_id', $jadwal->id)
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

        $riwayatJurnal = JurnalMengajar::where('guru_id', $guruId)
            ->where('tanggal', '<=', $today->toDateString())
            ->with(['jadwal.kelas', 'jadwal.materi'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('guru.jurnal.index', compact('jadwalPerTanggal', 'bulan', 'tahun', 'riwayatJurnal'));
    }

    public function create(Request $request, Jadwal $jadwal)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());
        abort_if($jadwal->guru_id !== Auth::id(), 403, 'Akses tidak diizinkan.');

        $jurnal = JurnalMengajar::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($jurnal) {
            return redirect()->route('guru.jurnal.edit', ['jadwal' => $jadwal, 'tanggal' => $tanggal])
                ->with('info', 'Jurnal untuk jadwal ini sudah ada. Silakan ubah di sini.');
        }

        $jadwal->load(['kelas', 'materi']);

        $metodeOptions = [
            'Ceramah', 'Tanya Jawab', 'Hafalan', 'Praktek Langsung',
            'Diskusi Kelompok', 'Penugasan', 'Demonstrasi',
        ];

        return view('guru.jurnal.create', compact('jadwal', 'tanggal', 'metodeOptions'));
    }

    public function store(Request $request, Jadwal $jadwal)
    {
        abort_if($jadwal->guru_id !== Auth::id(), 403);

        $validated = $request->validate([
            'topik'     => 'required|string|max:255',
            'metode'    => 'nullable|string|max:100',
            'ringkasan' => 'required|string',
            'catatan'   => 'nullable|string',
            'tanggal'   => 'required|date',
        ]);
        
        $tanggal = $request->input('tanggal');

        JurnalMengajar::create([
            'jadwal_id'  => $jadwal->id,
            'guru_id'    => Auth::id(),
            'tanggal'    => $tanggal,
            'topik'      => $validated['topik'],
            'metode'     => $validated['metode'],
            'ringkasan'  => $validated['ringkasan'],
            'catatan'    => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('guru.jurnal.index', [
            'bulan' => Carbon::parse($tanggal)->format('m'),
            'tahun' => Carbon::parse($tanggal)->format('Y')
        ])->with('success', 'Jurnal mengajar berhasil disimpan!');
    }

    public function edit(Request $request, Jadwal $jadwal)
    {
        $tanggal = $request->input('tanggal', today()->toDateString());
        abort_if($jadwal->guru_id !== Auth::id(), 403);

        $jurnal = JurnalMengajar::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->firstOrFail();

        $jadwal->load(['kelas', 'materi']);

        $metodeOptions = [
            'Ceramah', 'Tanya Jawab', 'Hafalan', 'Praktek Langsung',
            'Diskusi Kelompok', 'Penugasan', 'Demonstrasi',
        ];

        return view('guru.jurnal.edit', compact('jadwal', 'jurnal', 'tanggal', 'metodeOptions'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        abort_if($jadwal->guru_id !== Auth::id(), 403);

        $validated = $request->validate([
            'topik'     => 'required|string|max:255',
            'metode'    => 'nullable|string|max:100',
            'ringkasan' => 'required|string',
            'catatan'   => 'nullable|string',
            'tanggal'   => 'required|date',
        ]);
        
        $tanggal = $request->input('tanggal');

        JurnalMengajar::where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->update([
                'topik'     => $validated['topik'],
                'metode'    => $validated['metode'],
                'ringkasan' => $validated['ringkasan'],
                'catatan'   => $validated['catatan'] ?? null,
            ]);

        return redirect()->route('guru.jurnal.index', [
            'bulan' => Carbon::parse($tanggal)->format('m'),
            'tahun' => Carbon::parse($tanggal)->format('Y')
        ])->with('success', 'Jurnal mengajar berhasil diperbarui!');
    }
}
