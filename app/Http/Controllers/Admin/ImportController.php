<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $karyawans = karyawan::select('id', 'kode_karyawan', 'nama')->get();
        $karyawanMap = [];
        foreach($karyawans as $k) {
            $karyawanMap[$k->kode_karyawan] = ['id' => $k->id, 'nama' => $k->nama];
        }
        $jenisPotonganMap = JenisPotongan::pluck('id', 'kode_potongan')->toArray();

        $gagal = 0;
        $errors = [];
        $warnings = [];
        $validData = [];

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

            $angsFloat = is_numeric($angs) ? (float) $angs : 0;
            $pkokFloat = is_numeric($pkok) ? (float) $pkok : 0;
            $rpbgFloat = is_numeric($rpbg) ? (float) $rpbg : 0;
            $awalFloat = is_numeric($awal) ? (float) $awal : 0;
            $saldFloat = is_numeric($sald) ? (float) $sald : 0;

            if (!empty($rinciValues)) {
                $dataRinci = [
                    'PINJ' => is_numeric($pinj) ? (float) $pinj : 0,
                    'AWAL' => $awalFloat,
                    'BULN' => is_numeric($buln) ? (int) $buln : 0,
                    'KALI' => is_numeric($kali) ? (int) $kali : 0,
                    'PKOK' => $pkokFloat,
                    'RPBG' => $rpbgFloat,
                    'SALD' => $saldFloat,
                ];
            }

            // --- Validasi Perhitungan / Kalkulasi ---
            $hitungAngs = $pkokFloat + $rpbgFloat;
            $hitungSald = $awalFloat - $pkokFloat;
            $hasWarning = false;
            $warningMsg = [];

            // Toleransi perbedaan (misal koma) 1 Rupiah
            if (abs($angsFloat - $hitungAngs) > 1 && $pkokFloat > 0) {
                $hasWarning = true;
                $warningMsg[] = "Angsuran Excel (Rp " . number_format($angsFloat, 0, ',', '.') . ") berbeda dari hitungan sistem Pokok+Bunga (Rp " . number_format($hitungAngs, 0, ',', '.') . ")";
            }

            if (abs($saldFloat - $hitungSald) > 1 && $awalFloat > 0 && $saldFloat > 0) {
                 $hasWarning = true;
                 $warningMsg[] = "Saldo Akhir (Rp " . number_format($saldFloat, 0, ',', '.') . ") tidak cocok dgn Saldo Awal - Pokok (Rp " . number_format($hitungSald, 0, ',', '.') . ")";
            }

            if ($hasWarning) {
                $warnings[] = [
                    'baris' => $rowNum,
                    'kode'  => $cust,
                    'nama'  => $karyawanMap[$cust]['nama'],
                    'excel_angs' => $angsFloat,
                    'sistem_angs' => $hitungAngs,
                    'pesan' => implode(' | ', $warningMsg)
                ];
            }

            $validData[] = [
                'karyawan_id' => $karyawanMap[$cust]['id'],
                'jenis_potongan_id' => $jenisPotonganMap[$grup],
                'jumlah_potongan' => $angsFloat,
                'jumlah_koreksi' => $hitungAngs,
                'data_rinci' => $dataRinci,
                'has_warning' => $hasWarning
            ];
        }

        // Cache valid data untuk proses execute nanti (expired in 60 mins)
        $cacheKey = 'import_data_' . auth()->id() . '_' . time();
        Cache::put($cacheKey, $validData, now()->addMinutes(60));

        // Tampilkan halaman konfirmasi (preview)
        $totalValid = count($validData);
        return view('admin.import.preview', compact('gagal', 'errors', 'warnings', 'totalValid', 'bulan', 'tahun', 'cacheKey'));
    }

    public function execute(Request $request)
    {
        $request->validate([
            'cache_key' => 'required',
            'action' => 'required|in:koreksi,ignore,batal',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ]);

        if ($request->action === 'batal') {
            Cache::forget($request->cache_key);
            return redirect()->route('admin.import.form')->with('info', 'Import dibatalkan oleh pengguna.');
        }

        $validData = Cache::get($request->cache_key);
        if (!$validData) {
            return redirect()->route('admin.import.form')->with('error', 'Sesi import telah kedaluwarsa. Silakan upload file Excel kembali.');
        }

        $berhasil = 0;
        $diupdate = 0;
        $mappingData = [];

        DB::beginTransaction();

        try {
            foreach ($validData as $data) {
                // Jika pilih 'koreksi', pakai hitungan sistem
                // Jika pilih 'ignore', pakai 'jumlah_potongan' (asli excel)
                $jumlah_potongan = $data['jumlah_potongan'];
                if ($request->action === 'koreksi' && $data['has_warning']) {
                    $jumlah_potongan = $data['jumlah_koreksi'] > 0 ? $data['jumlah_koreksi'] : $data['jumlah_potongan'];
                }

                $record = InputBulanan::updateOrCreate(
                    [
                        'karyawan_id' => $data['karyawan_id'],
                        'jenis_potongan_id' => $data['jenis_potongan_id'],
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                    ],
                    [
                        'jumlah_potongan' => $jumlah_potongan,
                        'data_rinci' => $data['data_rinci'],
                    ]
                );

                if ($record->wasRecentlyCreated) {
                    $berhasil++;
                } else {
                    $diupdate++;
                }

                $mappingData[$data['karyawan_id']][$data['jenis_potongan_id']] = true;
            }

            // Auto mapping
            foreach ($mappingData as $karyawanId => $potonganIds) {
                $karyawanModel = karyawan::find($karyawanId);
                if ($karyawanModel) {
                    $karyawanModel->potongan()->syncWithoutDetaching(array_keys($potonganIds));
                }
            }

            DB::commit();
            Cache::forget($request->cache_key);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.import.form')->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        $gagal = 0;
        $errors = [];
        $bulan = $request->bulan;
        $tahun = $request->tahun;
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
