<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class OrangtuaController extends Controller
{
    public function index()
    {
        $dataOrangtua = User::where('role', 'orangtua')->latest()->paginate(10);
        return view('pengurus.kelola-orangtua.index', compact('dataOrangtua'));
    }

    public function create()
    {
        return view('pengurus.kelola-orangtua.create');
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
            'role'     => 'orangtua'
        ]);

        return redirect()->route('pengurus.kelola-orangtua.index')->with('success', 'Data orang tua berhasil ditambahkan.');
    }

    public function edit(User $kelola_orangtua)
    {
        $orangtua = $kelola_orangtua;
        return view('pengurus.kelola-orangtua.edit', compact('orangtua'));
    }

    public function update(Request $request, User $kelola_orangtua)
    {
        $orangtua = $kelola_orangtua;
        $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($orangtua->id)->whereNull('deleted_at')],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $orangtua->update([
            'nama'     => $request->nama,
            'username' => $request->username,
            'no_hp'    => $request->no_hp,
        ]);

        if ($request->filled('password')) {
            $orangtua->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('pengurus.kelola-orangtua.index')->with('success', 'Data orang tua berhasil diperbarui.');
    }

    public function destroy(User $kelola_orangtua)
    {
        $kelola_orangtua->delete();
        return redirect()->route('pengurus.kelola-orangtua.index')->with('success', 'Data orang tua berhasil dihapus.');
    }
}
