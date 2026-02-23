<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use App\Models\karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = InputBulanan::with(['karyawan', 'jenisPotongan']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('jenis_potongan_id')) {
            $query->where('jenis_potongan_id', $request->jenis_potongan_id);
        }
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $totalPotongan = (clone $query)->sum('jumlah_potongan');

        $laporan = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('karyawan_id')
            ->paginate(20);
        $laporan->appends($request->query());

        // Ringkasan per jenis potongan
        $ringkasan = InputBulanan::query();
        if ($request->filled('bulan')) $ringkasan->where('bulan', $request->bulan);
        if ($request->filled('tahun')) $ringkasan->where('tahun', $request->tahun);
        if ($request->filled('karyawan_id')) $ringkasan->where('karyawan_id', $request->karyawan_id);

        $ringkasan = $ringkasan->select('jenis_potongan_id', DB::raw('SUM(jumlah_potongan) as total'))
            ->groupBy('jenis_potongan_id')
            ->with('jenisPotongan')
            ->get();

        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();
        $karyawanList = karyawan::orderBy('nama')->get();

        return view('admin.laporan.index', compact(
            'laporan', 'totalPotongan', 'ringkasan',
            'jenisPotonganList', 'karyawanList'
        ));
    }
}
