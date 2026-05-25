<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaliKelasController extends Controller
{
    public function index()
    {
        // Eager-load wali kelas (beserta datanya) untuk setiap kelas
        $dataKelas = Kelas::with(['waliKelas.guru'])->orderBy('nama_kelas')->get();
        return view('pengurus.kelola-walikelas.index', compact('dataKelas'));
    }

    public function create()
    {
        // Kelas yang BELUM punya wali kelas (guru dipilih dinamis via JS setelah kelas dipilih)
        $kelasIds      = WaliKelas::pluck('kelas_id');
        $kelasTersedia = Kelas::whereNotIn('id_kelas', $kelasIds)->orderBy('nama_kelas')->get();

        return view('pengurus.kelola-walikelas.create', compact('kelasTersedia'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id_kelas', 'unique:wali_kelas,kelas_id'],
            'user_id'  => ['required', 'exists:users,id'],
        ], [
            'kelas_id.unique' => 'Kelas ini sudah memiliki wali kelas.',
        ]);

        // Pastikan guru yang dipilih memang mengajar di kelas tersebut
        $mengajarDiKelas = DB::table('kelas_user')
            ->where('kelas_id', $request->kelas_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if (!$mengajarDiKelas) {
            return back()
                ->withInput()
                ->withErrors(['user_id' => 'Guru yang dipilih tidak mengajar di kelas ini.']);
        }

        WaliKelas::create([
            'kelas_id' => $request->kelas_id,
            'user_id'  => $request->user_id,
        ]);

        return redirect()
            ->route('pengurus.kelola-walikelas.index')
            ->with('success', 'Wali kelas berhasil ditambahkan.');
    }

    public function destroy(WaliKelas $waliKelas)
    {
        $waliKelas->delete();

        return redirect()
            ->route('pengurus.kelola-walikelas.index')
            ->with('success', 'Wali kelas berhasil dihapus.');
    }

    /**
     * API: ambil daftar guru yang mengajar di kelas tertentu
     * (digunakan oleh form tambah wali kelas via fetch JS)
     */
    public function getGuruByKelas(Kelas $kelas)
    {
        $guru = $kelas->guru()->where('role', 'guru')->orderBy('nama')->get(['users.id', 'users.nama']);
        return response()->json($guru);
    }
}
