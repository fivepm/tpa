<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index()
    {
        $dataSiswa = Siswa::with(['kelas', 'orangtua'])->latest()->paginate(10);
        return view('pengurus.kelola-siswa.index', compact('dataSiswa'));
    }

    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $orangtua = User::where('role', 'orangtua')->orderBy('nama')->get();
        return view('pengurus.kelola-siswa.create', compact('kelas', 'orangtua'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => ['required', 'string', 'max:255', Rule::unique('siswa')->whereNull('deleted_at')],
            'nama' => ['required', 'string', 'max:255'],
            'kelas_id' => ['required', 'exists:kelas,id_kelas'],
            'orangtua_id' => ['required', 'exists:users,id'],
        ]);

        Siswa::create($request->all());
        return redirect()->route('pengurus.kelola-siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $kelola_siswa)
    {
        $siswa = $kelola_siswa;
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $orangtua = User::where('role', 'orangtua')->orderBy('nama')->get();
        return view('pengurus.kelola-siswa.edit', compact('siswa', 'kelas', 'orangtua'));
    }

    public function update(Request $request, Siswa $kelola_siswa)
    {
        $siswa = $kelola_siswa;
        $request->validate([
            'nis' => ['required', 'string', 'max:255', Rule::unique('siswa')->ignore($siswa->id)->whereNull('deleted_at')],
            'nama' => ['required', 'string', 'max:255'],
            'kelas_id' => ['required', 'exists:kelas,id_kelas'],
            'orangtua_id' => ['required', 'exists:users,id'],
        ]);

        $siswa->update($request->all());
        return redirect()->route('pengurus.kelola-siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $kelola_siswa)
    {
        $kelola_siswa->delete();
        return redirect()->route('pengurus.kelola-siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
