<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggota::with('jabatan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_anggota', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $anggota = $query->orderBy('kode_anggota')->paginate(15);
        $anggota->appends($request->query());

        return view('admin.anggota.index', compact('anggota'));
    }

    public function create()
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        return view('admin.anggota.create', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_anggota' => 'required|string|max:50|unique:anggota,kode_anggota',
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required|exists:jabatan,id',
            'buat_akun' => 'nullable|boolean',
            'username' => 'nullable|required_if:buat_akun,1|string|max:50|unique:users,username',
            'password' => 'nullable|required_if:buat_akun,1|string|min:6',
        ]);

        $anggota = Anggota::create([
            'kode_anggota' => $validated['kode_anggota'],
            'nama' => $validated['nama'],
            'jabatan_id' => $validated['jabatan_id'],
        ]);

        // Optionally create user account
        if ($request->boolean('buat_akun') && $request->filled('username')) {
            User::create([
                'username' => $validated['username'],
                'password' => $validated['password'],
                'role' => 'user',
                'anggota_id' => $anggota->id,
            ]);
        }

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Anggota $anggotum)
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        $anggota = $anggotum;
        return view('admin.anggota.edit', compact('anggota', 'jabatan'));
    }

    public function update(Request $request, Anggota $anggotum)
    {
        $anggota = $anggotum;
        $validated = $request->validate([
            'kode_anggota' => 'required|string|max:50|unique:anggota,kode_anggota,' . $anggota->id,
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required|exists:jabatan,id',
        ]);

        $anggota->update($validated);

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggotum)
    {
        $anggota = $anggotum;
        // Check if anggota has input_bulanan
        if ($anggota->inputBulanan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus anggota yang memiliki data potongan.');
        }

        // Delete associated user if exists
        if ($anggota->user) {
            $anggota->user->delete();
        }

        $anggota->delete();

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
