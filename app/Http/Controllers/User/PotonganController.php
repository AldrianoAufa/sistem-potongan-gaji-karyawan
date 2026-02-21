<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;

class PotonganController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $anggota = $user->anggota;

        if (!$anggota) {
            return view('user.potongan.index', [
                'potongan' => collect(),
                'jenisPotonganList' => collect(),
            ]);
        }

        // Row-level security: only show records for this anggota
        $query = InputBulanan::with('jenisPotongan')
            ->where('anggota_id', $anggota->id);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('jenis_potongan_id')) {
            $query->where('jenis_potongan_id', $request->jenis_potongan_id);
        }

        $potongan = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(15);
        $potongan->appends($request->query());

        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();

        return view('user.potongan.index', compact('potongan', 'jenisPotonganList'));
    }

    public function show(InputBulanan $inputBulanan)
    {
        $user = auth()->user();

        // Row-level security: ensure user can only see their own data
        if (!$user->anggota || $inputBulanan->anggota_id !== $user->anggota->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $inputBulanan->load(['anggota', 'jenisPotongan']);

        return view('user.potongan.show', compact('inputBulanan'));
    }
}
