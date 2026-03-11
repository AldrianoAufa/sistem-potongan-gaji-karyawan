<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;

class InputBulananController extends Controller
{
    public function index(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        $query = InputBulanan::with(['karyawan', 'jenisPotongan']);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('kode_karyawan', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $totalPotongan = (clone $query)->sum('jumlah_potongan');

        $perPage = $request->get('per_page');
        if ($perPage === 'all') {
            $perPage = 5000;
        } else {
            $perPage = in_array((int) $perPage, [25, 50, 100, 500, 1000]) ? (int) $perPage : 25;
        }
        
        $inputBulanan  = $query->orderBy('id', 'asc')->paginate($perPage)->withQueryString();

        $karyawanList = karyawan::orderBy('nama')->get();
        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();

        return view('admin.input-bulanan.index', compact(
            'inputBulanan', 'totalPotongan', 'karyawanList', 'jenisPotonganList', 'perPage'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
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

        // Check for duplicate entry
        $exists = InputBulanan::where('karyawan_id', $validated['karyawan_id'])
            ->where('jenis_potongan_id', $validated['jenis_potongan_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error',
                'Data potongan untuk karyawan ini dengan jenis potongan, bulan, dan tahun yang sama sudah ada. Silakan edit data yang sudah ada.');
        }

        // Clean data_rinci: only include if any value is non-empty
        if (isset($validated['data_rinci'])) {
            $rinci = array_filter($validated['data_rinci'], fn($v) => $v !== null && $v !== '');
            if (!empty($rinci)) {
                $rinci['ANGS'] = (float) $validated['jumlah_potongan'];
                $validated['data_rinci'] = $rinci;
            } else {
                $validated['data_rinci'] = null;
            }
        }

        InputBulanan::create($validated);

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', 'Data potongan bulanan berhasil ditambahkan.');
    }

    public function edit(InputBulanan $inputBulanan)
    {
        $karyawanList = karyawan::orderBy('nama')->get();
        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();

        return view('admin.input-bulanan.edit', compact('inputBulanan', 'karyawanList', 'jenisPotonganList'));
    }

    public function update(Request $request, InputBulanan $inputBulanan)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
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

        // Check for duplicate entry (exclude current record)
        $exists = InputBulanan::where('karyawan_id', $validated['karyawan_id'])
            ->where('jenis_potongan_id', $validated['jenis_potongan_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->where('id', '!=', $inputBulanan->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error',
                'Data potongan untuk karyawan ini dengan jenis potongan, bulan, dan tahun yang sama sudah ada.');
        }

        if (isset($validated['data_rinci'])) {
            $rinciInput = array_filter($validated['data_rinci'], fn($v) => $v !== null && $v !== '');
            $rinciExisting = $inputBulanan->data_rinci ?? [];
            
            // Merge: new input overrides existing, but existing fields like KDPR/NMPR are kept
            $validated['data_rinci'] = array_merge($rinciExisting, $rinciInput);
            
            // Sync ANGS in data_rinci with top-level jumlah_potongan
            $validated['data_rinci']['ANGS'] = (float) $validated['jumlah_potongan'];
        }

        $inputBulanan->update($validated);

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', 'Data potongan bulanan berhasil diperbarui.');
    }

    public function create(Request $request)
    {
        $jenisPotonganId = $request->query('jenis_potongan_id');
        $selectedPotongan = null;
        $karyawanList = [];

        if ($jenisPotonganId) {
            $selectedPotongan = JenisPotongan::findOrFail($jenisPotonganId);
            $karyawanList = karyawan::whereHas('potongan', function($q) use ($jenisPotonganId) {
                $q->where('jenis_potongan_id', $jenisPotonganId);
            })->orderBy('nama')->get();
        }

        $jenisPotonganAll = JenisPotongan::orderBy('nama_potongan')->get();
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('admin.input-bulanan.create', compact(
            'jenisPotonganAll', 'selectedPotongan', 'karyawanList', 'bulanOptions'
        ));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2099',
            'potongan' => 'required|array',
            'potongan.*.karyawan_id' => 'required|exists:karyawan,id',
            'potongan.*.jumlah' => 'nullable|numeric|min:0',
        ]);

        $count = 0;
        foreach ($validated['potongan'] as $item) {
            if ($item['jumlah'] !== null && $item['jumlah'] > 0) {
                $existing = InputBulanan::where([
                    'karyawan_id' => $item['karyawan_id'],
                    'jenis_potongan_id' => $validated['jenis_potongan_id'],
                    'bulan' => $validated['bulan'],
                    'tahun' => $validated['tahun'],
                ])->first();

                if ($existing) {
                    $rinci = $existing->data_rinci ?? [];
                    $rinci['ANGS'] = (float) $item['jumlah'];
                    // We keep other fields like KDPR, NMPR intact
                    $existing->update([
                        'jumlah_potongan' => $item['jumlah'],
                        'data_rinci' => $rinci,
                    ]);
                } else {
                    InputBulanan::create([
                        'karyawan_id' => $item['karyawan_id'],
                        'jenis_potongan_id' => $validated['jenis_potongan_id'],
                        'bulan' => $validated['bulan'],
                        'tahun' => $validated['tahun'],
                        'jumlah_potongan' => $item['jumlah'],
                    ]);
                }
                $count++;
            }
        }

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', $count . ' data potongan berhasil disimpan secara kolektif.');
    }

    public function destroy(InputBulanan $inputBulanan)
    {
        $inputBulanan->delete();

        return redirect()->route('admin.input-bulanan.index')
            ->with('success', 'Data potongan bulanan berhasil dihapus.');
    }
}
