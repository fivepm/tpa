<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriController extends Controller
{
    public function index()
    {
        $dataMateri = Materi::latest()->paginate(10);
        return view('pengurus.kelola-materi.index', compact('dataMateri'));
    }

    public function create()
    {
        return view('pengurus.kelola-materi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_materi' => ['required', 'string', 'max:255', Rule::unique('materi')->whereNull('deleted_at')],
        ]);

        Materi::create($request->all());

        return redirect()->route('pengurus.kelola-materi.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Materi $kelola_materi)
    {
        $materi = $kelola_materi;
        return view('pengurus.kelola-materi.edit', compact('materi'));
    }

    public function update(Request $request, Materi $kelola_materi)
    {
        $materi = $kelola_materi;
        $request->validate([
            'nama_materi' => ['required', 'string', 'max:255', Rule::unique('materi')->ignore($materi->id)->whereNull('deleted_at')],
        ]);

        $materi->update($request->all());

        return redirect()->route('pengurus.kelola-materi.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Materi $kelola_materi)
    {
        try {
            $kelola_materi->delete();
            return redirect()->route('pengurus.kelola-materi.index')->with('success', 'Materi berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('pengurus.kelola-materi.index')->with('error', 'Gagal menghapus! Materi ini masih digunakan oleh jadwal.');
        }
    }
}
