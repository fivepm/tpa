<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class GuruController extends Controller
{
    public function index()
    {
        $dataGuru = User::where('role', 'guru')->with('kelas')->paginate(10);
        return view('pengurus.kelola-guru.index', compact('dataGuru'));
    }

    public function create()
    {
        $daftarKelas = Kelas::all();
        return view('pengurus.kelola-guru.create', compact('daftarKelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'kelas' => ['nullable', 'array'],
            'kelas.*' => ['exists:kelas,id_kelas']
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'guru'
        ]);

        if ($request->has('kelas')) {
            $user->kelas()->attach($request->kelas);
        }

        return redirect()->route('pengurus.kelola-guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(User $kelola_guru)
    {
        $daftarKelas = Kelas::all();
        $guru = $kelola_guru;
        return view('pengurus.kelola-guru.edit', compact('guru', 'daftarKelas'));
    }

    public function update(Request $request, User $kelola_guru)
    {
        $guru = $kelola_guru;
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($guru->id)->whereNull('deleted_at')],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'kelas' => ['nullable', 'array'],
            'kelas.*' => ['exists:kelas,id_kelas'],
        ]);

        $guru->update([
            'nama' => $request->nama,
            'username' => $request->username,
        ]);

        if ($request->filled('password')) {
            $guru->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $kelasBaru = $request->kelas ?? [];
        $guru->kelas()->sync($kelasBaru);

        // Hapus otomatis wali kelas jika guru sudah tidak mengajar di kelas tersebut
        WaliKelas::where('user_id', $guru->id)
            ->whereNotIn('kelas_id', $kelasBaru)
            ->delete();

        return redirect()->route('pengurus.kelola-guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(User $kelola_guru)
    {
        $guru = $kelola_guru;
        // Hapus wali kelas yang terkait guru ini sebelum dihapus
        WaliKelas::where('user_id', $guru->id)->delete();
        $guru->kelas()->detach();
        $guru->delete();

        return redirect()->route('pengurus.kelola-guru.index')->with('success', 'Data guru berhasil dihapus.');
    }
}
