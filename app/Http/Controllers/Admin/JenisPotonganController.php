<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;

class JenisPotonganController extends Controller
{
    public function index()
    {
        // Ambil semua jenis potongan beserta count karyawan dan input bulanan
        $jenisPotongan = JenisPotongan::withCount('inputBulanan')
            ->orderBy('kode_potongan')
            ->paginate(15);

        // Load relasi karyawan secara terpisah agar tidak konflik dengan withCount
        $jenisPotongan->load(['karyawan' => function ($q) {
            $q->select('karyawan.id', 'kode_karyawan', 'nama', 'jabatan_id', 'departemen_id')
              ->with(['jabatan:id,nama_jabatan', 'departemen:id,nama_departemen'])
              ->orderBy('nama');
        }]);

        // Debug: Log the data being passed to view
        \Log::info('JenisPotongan Index - Total records: ' . $jenisPotongan->count());
        \Log::info('JenisPotongan Index - Total in database: ' . JenisPotongan::count());
        foreach ($jenisPotongan as $item) {
            \Log::info('Item: ' . $item->kode_potongan . ' - ' . $item->nama_potongan . ' (karyawan: ' . $item->karyawan->count() . ')');
        }

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
