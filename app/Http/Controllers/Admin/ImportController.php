<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    private const EXPECTED_HEADERS = [
        'URUT', 'KDPR', 'NMPR', 'CUST', 'NAMA', 'GRUP', 'NMGR',
        'PINJ', 'AWAL', 'BULN', 'KALI', 'PKOK', 'RPBG', 'ANGS', 'SALD'
    ];

    public function showForm(Request $request)
    {
        $departemenList = \App\Models\Departemen::with(['karyawan' => function ($q) {
            $q->orderBy('nama')->with('jabatan');
        }])->withCount('karyawan')->orderBy('nama_departemen')->get();

        // Data untuk input kolektif
        $jenisPotonganAll = JenisPotongan::orderBy('nama_potongan')->get();
        $bulanOptions = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Jika jenis potongan dipilih, load karyawan yang terdaftar
        $selectedPotongan = null;
        $karyawanKolektif = collect();
        if ($request->filled('jenis_potongan_id')) {
            $selectedPotongan = JenisPotongan::find($request->jenis_potongan_id);
            if ($selectedPotongan) {
                $karyawanKolektif = karyawan::whereHas('potongan', function($q) use ($request) {
                    $q->where('jenis_potongan_id', $request->jenis_potongan_id);
                })->orderBy('nama')->get();
            }
        }

        return view('admin.import.index', compact(
            'departemenList', 'jenisPotonganAll', 'bulanOptions',
            'selectedPotongan', 'karyawanKolektif'
        ));
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        $file = $request->file('file');
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'File Excel kosong.');
        }

        // Get headers from first row
        $headerRow = array_shift($rows);
        $headers = array_map(function ($h) {
            return strtoupper(trim($h ?? ''));
        }, array_values($headerRow));

        // Validate headers
        $missingHeaders = array_diff(self::EXPECTED_HEADERS, $headers);
        if (!empty($missingHeaders)) {
            return back()->with('error', 'Header kolom tidak sesuai! Kolom yang hilang: ' . implode(', ', $missingHeaders));
        }

        // Map header positions
        $headerMap = array_flip($headers);

        // Cache karyawan and jenis potongan
        $karyawanMap = karyawan::pluck('id', 'kode_karyawan')->toArray();
        $jenisPotonganMap = JenisPotongan::pluck('id', 'kode_potongan')->toArray();

        $berhasil = 0;
        $diupdate = 0;
        $gagal = 0;
        $errors = [];
        $mappingData = []; // Kumpulkan pasangan karyawan-potongan untuk auto mapping

        DB::beginTransaction();

        try {
            foreach ($rows as $rowIndex => $row) {
                $rowValues = array_values($row);
                $rowNum = $rowIndex + 2; // +2 karena header = baris 1

                // Skip empty rows
                if (empty(array_filter($rowValues, fn($v) => $v !== null && $v !== ''))) {
                    continue;
                }

                $cust = trim($rowValues[$headerMap['CUST']] ?? '');
                $grup = trim($rowValues[$headerMap['GRUP']] ?? '');
                $angs = $rowValues[$headerMap['ANGS']] ?? 0;

                // Validasi CUST
                if (empty($cust)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => 'NIK (CUST) kosong'];
                    $gagal++;
                    continue;
                }

                if (!isset($karyawanMap[$cust])) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => 'NIK tidak ditemukan'];
                    $gagal++;
                    continue;
                }

                // Validasi GRUP
                if (empty($grup)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => 'Kode jenis potongan (GRUP) kosong'];
                    $gagal++;
                    continue;
                }

                if (!isset($jenisPotonganMap[$grup])) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => "Jenis potongan '{$grup}' tidak ditemukan"];
                    $gagal++;
                    continue;
                }

                // Validasi angka
                if (!is_numeric($angs)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => 'Jumlah angsuran (ANGS) bukan angka'];
                    $gagal++;
                    continue;
                }

                // Build data_rinci from loan columns
                $dataRinci = null;
                $pinj = $rowValues[$headerMap['PINJ']] ?? null;
                $awal = $rowValues[$headerMap['AWAL']] ?? null;
                $buln = $rowValues[$headerMap['BULN']] ?? null;
                $kali = $rowValues[$headerMap['KALI']] ?? null;
                $pkok = $rowValues[$headerMap['PKOK']] ?? null;
                $rpbg = $rowValues[$headerMap['RPBG']] ?? null;
                $sald = $rowValues[$headerMap['SALD']] ?? null;

                $rinciValues = compact('pinj', 'awal', 'buln', 'kali', 'pkok', 'rpbg', 'sald');
                $rinciValues = array_filter($rinciValues, fn($v) => $v !== null && $v !== '' && $v !== 0);

                if (!empty($rinciValues)) {
                    $dataRinci = [
                        'PINJ' => is_numeric($pinj) ? (float) $pinj : 0,
                        'AWAL' => is_numeric($awal) ? (float) $awal : 0,
                        'BULN' => is_numeric($buln) ? (int) $buln : 0,
                        'KALI' => is_numeric($kali) ? (int) $kali : 0,
                        'PKOK' => is_numeric($pkok) ? (float) $pkok : 0,
                        'RPBG' => is_numeric($rpbg) ? (float) $rpbg : 0,
                        'SALD' => is_numeric($sald) ? (float) $sald : 0,
                    ];
                }

                $record = InputBulanan::updateOrCreate(
                    [
                        'karyawan_id' => $karyawanMap[$cust],
                        'jenis_potongan_id' => $jenisPotonganMap[$grup],
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ],
                    [
                        'jumlah_potongan' => (float) $angs,
                        'data_rinci' => $dataRinci,
                    ]
                );

                if ($record->wasRecentlyCreated) {
                    $berhasil++;
                } else {
                    $diupdate++;
                }

                // Simpan pasangan untuk auto mapping
                $karyawanId = $karyawanMap[$cust];
                $potonganId = $jenisPotonganMap[$grup];
                $mappingData[$karyawanId][$potonganId] = true;
            }

            // Auto mapping: tambahkan ke tabel pivot karyawan_potongan tanpa menghapus yang sudah ada
            foreach ($mappingData as $karyawanId => $potonganIds) {
                $karyawanModel = karyawan::find($karyawanId);
                if ($karyawanModel) {
                    $karyawanModel->potongan()->syncWithoutDetaching(array_keys($potonganIds));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }

        return view('admin.import.result', compact('berhasil', 'diupdate', 'gagal', 'errors', 'bulan', 'tahun'));
    }

    public function collectiveStore(Request $request)
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
                InputBulanan::updateOrCreate(
                    [
                        'karyawan_id' => $item['karyawan_id'],
                        'jenis_potongan_id' => $validated['jenis_potongan_id'],
                        'bulan' => $validated['bulan'],
                        'tahun' => $validated['tahun'],
                    ],
                    [
                        'jumlah_potongan' => $item['jumlah'],
                    ]
                );
                $count++;
            }
        }

        return redirect()->route('admin.import.form')
            ->with('success', $count . ' data potongan berhasil disimpan secara kolektif.');
    }
}
