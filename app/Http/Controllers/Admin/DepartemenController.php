<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::withCount('karyawan')->orderBy('kode_departemen')->paginate(15);
        return view('admin.departemen.index', compact('departemen'));
    }

    public function show(Departemen $departemen)
    {
        $karyawan = $departemen->karyawan()->with('jabatan')->orderBy('nama')->get();
        return view('admin.departemen.show', compact('departemen', 'karyawan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|string|max:50|unique:departemen,kode_departemen',
            'nama_departemen' => 'required|string|max:255',
        ]);

        Departemen::create($validated);

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'kode_departemen' => 'required|string|max:50|unique:departemen,kode_departemen,' . $departemen->id,
            'nama_departemen' => 'required|string|max:255',
        ]);

        $departemen->update($validated);

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        if ($departemen->karyawan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus departemen yang masih memiliki karyawan.');
        }

        $departemen->delete();

        return redirect()->route('admin.departemen.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
