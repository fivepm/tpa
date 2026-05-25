<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Materi;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $dataJadwal = Jadwal::with(['kelas', 'guru', 'materi'])->latest()->paginate(10);
        return view('pengurus.kelola-jadwal.index', compact('dataJadwal'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru = User::where('role', 'guru')->orderBy('nama')->get();
        $materi = Materi::orderBy('nama_materi')->get();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pengurus.kelola-jadwal.create', compact('kelas', 'guru', 'materi', 'hari'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id_kelas'],
            'guru_id' => ['required', 'exists:users,id'],
            'materi_id' => ['required', 'exists:materi,id'],
            'hari' => ['required', 'string'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        Jadwal::create($request->all());

        return redirect()->route('pengurus.kelola-jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $kelola_jadwal)
    {
        $jadwal = $kelola_jadwal;
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru = User::where('role', 'guru')->orderBy('nama')->get();
        $materi = Materi::orderBy('nama_materi')->get();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('pengurus.kelola-jadwal.edit', compact('jadwal', 'kelas', 'guru', 'materi', 'hari'));
    }

    public function update(Request $request, Jadwal $kelola_jadwal)
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id_kelas'],
            'guru_id' => ['required', 'exists:users,id'],
            'materi_id' => ['required', 'exists:materi,id'],
            'hari' => ['required', 'string'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $kelola_jadwal->update($request->all());

        return redirect()->route('pengurus.kelola-jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $kelola_jadwal)
    {
        $kelola_jadwal->delete();
        return redirect()->route('pengurus.kelola-jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function getGuruByKelas(Kelas $kelas)
    {
        $guru = $kelas->guru()->orderBy('nama')->get();
        return response()->json($guru);
    }
}
