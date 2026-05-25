<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HariLiburController extends Controller
{
    public function index()
    {
        $dataHariLibur = HariLibur::orderBy('tanggal', 'desc')->paginate(10);
        return view('pengurus.kelola-harilibur.index', compact('dataHariLibur'));
    }

    public function create()
    {
        return view('pengurus.kelola-harilibur.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => ['required', 'date', Rule::unique('hari_libur', 'tanggal')->whereNull('deleted_at')],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        HariLibur::create($request->all());

        return redirect()->route('pengurus.kelola-harilibur.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(HariLibur $kelola_harilibur)
    {
        $hariLibur = $kelola_harilibur;
        return view('pengurus.kelola-harilibur.edit', compact('hariLibur'));
    }

    public function update(Request $request, HariLibur $kelola_harilibur)
    {
        $hariLibur = $kelola_harilibur;
        $request->validate([
            'tanggal' => ['required', 'date', Rule::unique('hari_libur', 'tanggal')->ignore($hariLibur->id)->whereNull('deleted_at')],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        $hariLibur->update($request->all());

        return redirect()->route('pengurus.kelola-harilibur.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(HariLibur $kelola_harilibur)
    {
        $kelola_harilibur->delete();
        return redirect()->route('pengurus.kelola-harilibur.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
