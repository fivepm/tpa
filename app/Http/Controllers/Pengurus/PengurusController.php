<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class PengurusController extends Controller
{
    public function index()
    {
        $dataPengurus = User::where('role', 'pengurus')->latest()->paginate(10);
        return view('pengurus.kelola-pengurus.index', compact('dataPengurus'));
    }

    public function create()
    {
        return view('pengurus.kelola-pengurus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->whereNull('deleted_at')],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'no_hp'    => $request->no_hp,
            'password' => Hash::make($request->password),
            'role'     => 'pengurus'
        ]);

        return redirect()->route('pengurus.kelola-pengurus.index')->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function edit(User $kelola_penguru)
    {
        $pengurus = $kelola_penguru;
        return view('pengurus.kelola-pengurus.edit', compact('pengurus'));
    }

    public function update(Request $request, User $kelola_penguru)
    {
        $pengurus = $kelola_penguru;
        $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($pengurus->id)->whereNull('deleted_at')],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $pengurus->update([
            'nama'     => $request->nama,
            'username' => $request->username,
            'no_hp'    => $request->no_hp,
        ]);

        if ($request->filled('password')) {
            $pengurus->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('pengurus.kelola-pengurus.index')->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy(User $kelola_penguru)
    {
        $kelola_penguru->delete();
        return redirect()->route('pengurus.kelola-pengurus.index')->with('success', 'Data pengurus berhasil dihapus.');
    }
}
