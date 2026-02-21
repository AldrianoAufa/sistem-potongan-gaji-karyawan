<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;

class InputBulananController extends Controller
{
    public function index(Request $request)
    {
        $query = InputBulanan::with(['anggota', 'jenisPotongan']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('anggota', function ($q) use ($search) {
                $q->where('kode_anggota', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $inputBulanan = $query->orderBy('created_at', 'desc')->paginate(15);
        $inputBulanan->appends($request->query());

        $totalPotongan = $query->sum('jumlah_potongan');

        $anggotaList = Anggota::orderBy('nama')->get();
        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();

        return view('admin.input-bulanan.index', compact(
            'inputBulanan', 'totalPotongan', 'anggotaList', 'jenisPotonganList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2099',
            'jumlah_potongan' => 'required|numeric|min:0',
            'data_rinci' => 'nullable|array',
            'data_rinci.PINJ' => 'nullable|numeric',
            'data_rinci.AWAL' => 'nullable|numeric',
            'data_rinci.BULN' => 'nullable|integer',
            'data_rinci.KALI' => 'nullable|integer',
            'data_rinci.PKOK' => 'nullable|numeric',
            'data_rinci.RPBG' => 'nullable|numeric',
            'data_rinci.SALD' => 'nullable|numeric',
        ]);

        // Clean data_rinci: only include if any value is non-empty
        if (isset($validated['data_rinci'])) {
            $rinci = array_filter($validated['data_rinci'], fn($v) => $v !== null && $v !== '');
            $validated['data_rinci'] = !empty($rinci) ? $rinci : null;
        }

        InputBulanan::create($validated);

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', 'Data potongan bulanan berhasil ditambahkan.');
    }

    public function edit(InputBulanan $inputBulanan)
    {
        $anggotaList = Anggota::orderBy('nama')->get();
        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();

        return view('admin.input-bulanan.edit', compact('inputBulanan', 'anggotaList', 'jenisPotonganList'));
    }

    public function update(Request $request, InputBulanan $inputBulanan)
    {
        $validated = $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2099',
            'jumlah_potongan' => 'required|numeric|min:0',
            'data_rinci' => 'nullable|array',
            'data_rinci.PINJ' => 'nullable|numeric',
            'data_rinci.AWAL' => 'nullable|numeric',
            'data_rinci.BULN' => 'nullable|integer',
            'data_rinci.KALI' => 'nullable|integer',
            'data_rinci.PKOK' => 'nullable|numeric',
            'data_rinci.RPBG' => 'nullable|numeric',
            'data_rinci.SALD' => 'nullable|numeric',
        ]);

        if (isset($validated['data_rinci'])) {
            $rinci = array_filter($validated['data_rinci'], fn($v) => $v !== null && $v !== '');
            $validated['data_rinci'] = !empty($rinci) ? $rinci : null;
        }

        $inputBulanan->update($validated);

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', 'Data potongan bulanan berhasil diperbarui.');
    }

    public function destroy(InputBulanan $inputBulanan)
    {
        $inputBulanan->delete();

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', 'Data potongan bulanan berhasil dihapus.');
    }
}
