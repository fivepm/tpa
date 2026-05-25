<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class KelasController extends Controller
{
    public function index()
    {
        $dataKelas = Kelas::latest()->paginate(10);
        return view('pengurus.kelola-kelas.index', compact('dataKelas'));
    }

    public function create()
    {
        return view('pengurus.kelola-kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas')->whereNull('deleted_at')],
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
        ]);

        return redirect()->route('pengurus.kelola-kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelola_kela)
    {
        $kelas = $kelola_kela;
        return view('pengurus.kelola-kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelola_kela)
    {
        $kelas = $kelola_kela;
        $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas')->ignore($kelas->id_kelas, 'id_kelas')->whereNull('deleted_at')],
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
        ]);

        return redirect()->route('pengurus.kelola-kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelola_kela)
    {
        try {
            $kelola_kela->delete();
            return redirect()->route('pengurus.kelola-kelas.index')->with('success', 'Data kelas berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('pengurus.kelola-kelas.index')->with('error', 'Gagal menghapus! Kelas ini masih digunakan oleh data guru.');
        }
    }
}
