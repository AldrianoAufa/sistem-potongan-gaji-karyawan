<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\User;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class karyawanController extends Controller
{
    public function index(Request $request)
    {
        $query = karyawan::with(['jabatan', 'departemen']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_karyawan', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $karyawan = $query->orderBy('kode_karyawan')->paginate(15);
        $karyawan->appends($request->query());

        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        $departemen = Departemen::orderBy('kode_departemen')->get();
        return view('admin.karyawan.create', compact('jabatan', 'departemen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_karyawan' => 'required|string|max:50|unique:karyawan,kode_karyawan',
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required|exists:jabatan,id',
            'departemen_id' => 'required|exists:departemen,id',
            'buat_akun' => 'nullable|boolean',
            'username' => 'nullable|required_if:buat_akun,1|string|max:50|unique:users,username',
            'password' => 'nullable|required_if:buat_akun,1|string|min:6',
        ]);

        $karyawan = karyawan::create([
            'kode_karyawan' => $validated['kode_karyawan'],
            'nama' => $validated['nama'],
            'jabatan_id' => $validated['jabatan_id'],
            'departemen_id' => $validated['departemen_id'],
        ]);

        // Optionally create user account
        if ($request->boolean('buat_akun') && $request->filled('username')) {
            User::create([
                'username' => $validated['username'],
                'password' => $validated['password'],
                'role' => 'user',
                'karyawan_id' => $karyawan->id,
            ]);
        }

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'karyawan berhasil ditambahkan.');
    }

    public function edit(karyawan $anggotum)
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        $departemen = Departemen::orderBy('kode_departemen')->get();
        $karyawan = $anggotum;
        return view('admin.karyawan.edit', compact('karyawan', 'jabatan', 'departemen'));
    }

    public function update(Request $request, karyawan $anggotum)
    {
        $karyawan = $anggotum;
        $validated = $request->validate([
            'kode_karyawan' => 'required|string|max:50|unique:karyawan,kode_karyawan,' . $karyawan->id,
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required|exists:jabatan,id',
            'departemen_id' => 'required|exists:departemen,id',
        ]);

        $karyawan->update($validated);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'karyawan berhasil diperbarui.');
    }

    public function destroy(karyawan $anggotum)
    {
        $karyawan = $anggotum;
        // Check if karyawan has input_bulanan
        if ($karyawan->inputBulanan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus karyawan yang memiliki data potongan.');
        }

        // Delete associated user if exists
        if ($karyawan->user) {
            $karyawan->user->delete();
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'karyawan berhasil dihapus.');
    }
    public function mapping(Request $request)
    {
        $query = karyawan::with(['jabatan', 'departemen', 'potongan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_karyawan', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $karyawanList = $query->orderBy('kode_karyawan')->paginate(15);
        $karyawanList->appends($request->query());
        
        $jenisPotongan = JenisPotongan::orderBy('nama_potongan')->get();

        return view('admin.karyawan.mapping', compact('karyawanList', 'jenisPotongan'));
    }

    public function updateMapping(Request $request, karyawan $karyawan)
    {
        $validated = $request->validate([
            'jenis_potongan_ids' => 'nullable|array',
            'jenis_potongan_ids.*' => 'exists:jenis_potongan,id',
        ]);

        $karyawan->potongan()->sync($validated['jenis_potongan_ids'] ?? []);

        return back()->with('success', 'Mapping potongan untuk ' . $karyawan->nama . ' berhasil diperbarui.');
    }
}
