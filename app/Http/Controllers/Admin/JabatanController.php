<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::withCount('karyawan')->orderBy('nama_jabatan')->paginate(15);
        return view('admin.jabatan.index', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan',
        ]);

        Jabatan::create($validated);

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan,' . $jabatan->id,
        ]);

        $jabatan->update($validated);

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan)
    {
        if ($jabatan->karyawan()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jabatan yang masih memiliki karyawan.');
        }

        $jabatan->delete();

        return redirect()->route('admin.jabatan.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
