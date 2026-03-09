<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\User;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with(['jabatan', 'departemen', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_karyawan', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jabatan_id')) {
            $query->where('jabatan_id', $request->jabatan_id);
        }

        if ($request->filled('departemen_id')) {
            $query->where('departemen_id', $request->departemen_id);
        }

        $karyawan = $query->orderBy('kode_karyawan')->paginate(15);
        $karyawan->appends($request->query());

        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        $departemen = Departemen::orderBy('kode_departemen')->get();

        return view('admin.karyawan.index', compact('karyawan', 'jabatan', 'departemen'));
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
        ]);

        $karyawan = Karyawan::create([
            'kode_karyawan' => $validated['kode_karyawan'],
            'nama' => $validated['nama'],
            'jabatan_id' => $validated['jabatan_id'],
            'departemen_id' => $validated['departemen_id'],
        ]);

        // Create user account using NIK as username and password
        if ($request->boolean('buat_akun')) {
            $nik = $validated['kode_karyawan'];
            
            // Check if username already exists for someone else
            if (User::where('username', $nik)->where('karyawan_id', '!=', $karyawan->id)->exists()) {
                session()->flash('error', "Gagal membuat akun: Username '{$nik}' sudah digunakan oleh orang lain.");
            } else {
                User::updateOrCreate(
                    ['karyawan_id' => $karyawan->id],
                    [
                        'username' => $nik,
                        'password' => $nik,
                        'role' => 'user'
                    ]
                );
            }
        }

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'karyawan berhasil ditambahkan.');
    }

    public function edit(Karyawan $karyawan)
    {
        $jabatan = Jabatan::orderBy('nama_jabatan')->get();
        $departemen = Departemen::orderBy('kode_departemen')->get();
        return view('admin.karyawan.edit', compact('karyawan', 'jabatan', 'departemen'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'kode_karyawan' => 'required|string|max:50|unique:karyawan,kode_karyawan,' . $karyawan->id,
            'nama' => 'required|string|max:255',
            'jabatan_id' => 'required|exists:jabatan,id',
            'departemen_id' => 'required|exists:departemen,id',
        ]);

        $karyawan->update($validated);

        // Sync user account if exists
        if ($karyawan->user) {
            $nik = $validated['kode_karyawan'];
            $karyawan->user->update([
                'username' => $nik,
                'password' => $nik,
            ]);
        }

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'karyawan berhasil diperbarui serta akun user disinkronkan.');
    }

    public function destroy(Karyawan $karyawan)
    {
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

    public function destroyAll()
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Delete regular users first (those associated with karyawan)
            User::where('role', 'user')->delete();
            
            // Delete all mappings in pivot table
            \Illuminate\Support\Facades\DB::table('karyawan_potongan')->delete();
            
            // Delete all karyawan (this will cascade delete input_bulanan)
            Karyawan::query()->delete();
            
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.karyawan.index')
                ->with('success', 'Semua data karyawan, akun user (non-admin), dan data potongan telah dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function mapping(Request $request)
    {
        $query = Karyawan::with(['jabatan', 'departemen', 'potongan']);

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

    public function updateMapping(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'jenis_potongan_ids' => 'nullable|array',
            'jenis_potongan_ids.*' => 'exists:jenis_potongan,id',
        ]);

        $karyawan->potongan()->sync($validated['jenis_potongan_ids'] ?? []);

        return back()->with('success', 'Mapping potongan untuk ' . $karyawan->nama . ' berhasil diperbarui.');
    }

    public function resetPassword(Karyawan $karyawan)
    {
        $user = $karyawan->user;

        if (!$user) {
            return back()->with('error', 'Karyawan ' . $karyawan->nama . ' belum memiliki akun user.');
        }

        $user->update(['password' => $karyawan->kode_karyawan]);

        return back()->with('success', 'Password ' . $karyawan->nama . ' berhasil di-reset ke NIK (' . $karyawan->kode_karyawan . ').');
    }
}
