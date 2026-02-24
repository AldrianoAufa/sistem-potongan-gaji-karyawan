<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;

class JenisPotonganController extends Controller
{
    public function index()
    {
        $jenisPotongan = JenisPotongan::withCount(['inputBulanan', 'karyawan'])
            ->with(['karyawan' => function ($q) {
                $q->select('karyawan.id', 'kode_karyawan', 'nama')->orderBy('nama');
            }])
            ->orderBy('kode_potongan')
            ->paginate(15);
        return view('admin.jenis-potongan.index', compact('jenisPotongan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_potongan' => 'required|string|max:50|unique:jenis_potongan,kode_potongan',
            'nama_potongan' => 'required|string|max:255',
        ]);

        JenisPotongan::create($validated);

        return redirect()->route('admin.jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil ditambahkan.');
    }

    public function update(Request $request, JenisPotongan $jenisPotongan)
    {
        $validated = $request->validate([
            'kode_potongan' => 'required|string|max:50|unique:jenis_potongan,kode_potongan,' . $jenisPotongan->id,
            'nama_potongan' => 'required|string|max:255',
        ]);

        $jenisPotongan->update($validated);

        return redirect()->route('admin.jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil diperbarui.');
    }

    public function destroy(JenisPotongan $jenisPotongan)
    {
        if ($jenisPotongan->inputBulanan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jenis potongan yang masih memiliki data.');
        }

        $jenisPotongan->delete();

        return redirect()->route('admin.jenis-potongan.index')
            ->with('success', 'Jenis potongan berhasil dihapus.');
    }
}
