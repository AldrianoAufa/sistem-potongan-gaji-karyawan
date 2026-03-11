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

    /**
     * Cek apakah sudah ada data input bulanan untuk periode tertentu (AJAX)
     */
    public function checkPeriod(Request $request)
    {
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        if (!$bulan || !$tahun) {
            return response()->json(['exists' => false, 'count' => 0]);
        }

        $count = InputBulanan::where('bulan', $bulan)->where('tahun', $tahun)->count();

        return response()->json([
            'exists' => $count > 0,
            'count'  => $count,
        ]);
    }

    public function showForm(Request $request)
    {
        // Cek apakah ada sesi import yang belum selesai
        $activeImport = null;
        $activeCacheKey = session('import_active_cache_key');
        if ($activeCacheKey) {
            $cachedData = Cache::store('file')->get($activeCacheKey);
            if ($cachedData) {
                $bulanNames = [
                    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                    5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                    9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                ];
                $activeImport = [
                    'cache_key'    => $activeCacheKey,
                    'total'        => count($cachedData['rows']),
                    'bulan_nama'   => $bulanNames[$cachedData['bulan']] ?? '-',
                    'tahun'        => $cachedData['tahun'],
                    'total_warning'=> count(array_filter($cachedData['rows'], fn($r) => $r['has_warning'])),
                    'expires_at'   => session('import_active_expires_at'),
                ];
            } else {
                // Cache sudah kadaluwarsa, hapus session
                session()->forget(['import_active_cache_key', 'import_active_expires_at']);
            }
        }

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
            'selectedPotongan', 'karyawanKolektif', 'activeImport'
        ));
    }

    public function process(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        $file = $request->file('file');
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, false, false, true);
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

        // Cache karyawan
        $karyawans = karyawan::select('id', 'kode_karyawan', 'nama')->get();
        $karyawanMap = [];
        foreach($karyawans as $k) {
            $karyawanMap[$k->kode_karyawan] = ['id' => $k->id, 'nama' => $k->nama];
        }

        // Cache jenis potongan dengan normalisasi untuk menangani padding (02 vs 2)
        $rawJenisPotongan = JenisPotongan::all();
        $jenisPotonganMap = [];
        $jenisPotonganNamaMap = [];
        foreach ($rawJenisPotongan as $jp) {
            $code = (string) $jp->kode_potongan;
            $jenisPotonganMap[$code] = $jp->id;
            if (is_numeric($code)) {
                $jenisPotonganMap[(string)(float)$code] = $jp->id;
            }
            $jenisPotonganNamaMap[$jp->id] = $jp->nama_potongan;
        }

        $gagal     = 0;
        $errors    = [];
        $validData = [];

        foreach ($rows as $rowIndex => $row) {
            $rowValues = array_values($row);
            $rowNum    = $rowIndex + 2; // +2 karena header = baris 1

            // Skip empty rows
            if (empty(array_filter($rowValues, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            $cust = trim($rowValues[$headerMap['CUST']] ?? '');
            
            // Normalisasi NIK numeric: hapus .0 yang tidak perlu (agar 1111.0 jadi 1111)
            if (is_numeric($cust)) {
                if (strpos($cust, '.') !== false && floatval($cust) == intval($cust)) {
                    $cust = (string)intval($cust);
                }
            }
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

            // Validasi GRUP (Normalisasi agar 02 cocok dengan 2)
            if (empty($grup)) {
                $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => 'Kode jenis potongan (GRUP) kosong'];
                $gagal++;
                continue;
            }

            $lookupGrup = $grup;
            if (is_numeric($grup)) {
                $lookupGrup = (string)(float)$grup;
            }

            if (!isset($jenisPotonganMap[$lookupGrup]) && !isset($jenisPotonganMap[$grup])) {
                $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => "Jenis potongan '{$grup}' tidak ditemukan"];
                $gagal++;
                continue;
            }

            $jenisPotonganId = $jenisPotonganMap[$lookupGrup] ?? $jenisPotonganMap[$grup];

            // Validasi angka
            if (!is_numeric($angs)) {
                $errors[] = ['baris' => $rowNum, 'kode' => $cust, 'error' => 'Jumlah angsuran (ANGS) bukan angka'];
                $gagal++;
                continue;
            }

            // Build data_rinci from loan columns
            $pinj = $rowValues[$headerMap['PINJ']] ?? null;
            $awal = $rowValues[$headerMap['AWAL']] ?? null;
            $buln = $rowValues[$headerMap['BULN']] ?? null;
            $kali = $rowValues[$headerMap['KALI']] ?? null;
            $pkok = $rowValues[$headerMap['PKOK']] ?? null;
            $rpbg = $rowValues[$headerMap['RPBG']] ?? null;
            $sald = $rowValues[$headerMap['SALD']] ?? null;
            $nmpr = trim($rowValues[$headerMap['NMPR']] ?? '');

            $angsFloat = is_numeric($angs) ? (float) $angs : 0;
            $pkokFloat = is_numeric($pkok) ? (float) $pkok : 0;
            $rpbgFloat = is_numeric($rpbg) ? (float) $rpbg : 0;
            $awalFloat = is_numeric($awal) ? (float) $awal : 0;
            $saldFloat = is_numeric($sald) ? (float) $sald : 0;

            $dataRinci = [
                'URUT' => $rowValues[$headerMap['URUT']] ?? null,
                'KDPR' => trim($rowValues[$headerMap['KDPR']] ?? ''),
                'NMPR' => $nmpr,
                'CUST' => $cust,
                'NAMA' => trim($rowValues[$headerMap['NAMA']] ?? ''),
                'GRUP' => $grup,
                'NMGR' => trim($rowValues[$headerMap['NMGR']] ?? ''),
                'PINJ' => is_numeric($pinj) ? (float) $pinj : 0,
                'AWAL' => $awalFloat,
                'BULN' => is_numeric($buln) ? (int) $buln : 0,
                'KALI' => is_numeric($kali) ? (int) $kali : 0,
                'PKOK' => $pkokFloat,
                'RPBG' => $rpbgFloat,
                'ANGS' => $angsFloat,
                'SALD' => $saldFloat,
            ];

            // Hitung angsuran berdasarkan sistem: PKOK + RPBG
            $hitungAngs = $pkokFloat + $rpbgFloat;

            // Cek apakah ada warning perhitungan
            $hasWarning = (abs($angsFloat - $hitungAngs) > 1 && $pkokFloat > 0);

            // $jenisPotonganId sudah didapat di atas

            $validData[] = [
                'baris'             => $rowNum,
                'karyawan_id'       => $karyawanMap[$cust]['id'],
                'kode_karyawan'     => $cust,
                'nama_karyawan'     => $karyawanMap[$cust]['nama'],
                'jenis_potongan_id' => $jenisPotonganId,
                'nama_potongan'     => $jenisPotonganNamaMap[$jenisPotonganId] ?? $grup,
                'kode_potongan'     => $grup,
                'nama_produk'       => $nmpr,
                // Nilai dari Excel (asli)
                'excel_angs'        => $angsFloat,
                // Nilai yang akan tersimpan (bisa diubah user lewat preview)
                'jumlah_potongan'   => $hitungAngs > 0 ? $hitungAngs : $angsFloat,
                // Komponen yang bisa diedit
                'pkok'              => $pkokFloat,
                'rpbg'              => $rpbgFloat,
                // Status
                'has_warning'       => $hasWarning,
                // Data rinci (akan diupdate bersamaan jika pkok diubah)
                'data_rinci'        => $dataRinci,
            ];
        }

        // Cache valid data untuk proses execute nanti (expired in 60 mins)
        $cacheKey = 'import_data_' . auth()->id() . '_' . time();
        $expiresAt = now()->addMinutes(60);
        Cache::store('file')->put($cacheKey, [
            'rows'   => $validData,
            'bulan'  => $bulan,
            'tahun'  => $tahun,
            'gagal'  => $gagal,
            'errors' => $errors,
        ], $expiresAt);

        // Simpan cache_key di session agar bisa di-resume jika user keluar halaman
        session([
            'import_active_cache_key'  => $cacheKey,
            'import_active_expires_at' => $expiresAt->format('H:i'),
        ]);

        // Redirect ke halaman preview (GET) agar browser Back button tetap bisa load dari cache
        return redirect()->route('admin.import.preview', ['key' => $cacheKey]);
    }

    /**
     * Tampilkan halaman preview dari cache (GET — aman untuk Back/Refresh)
     */
    public function showPreview(Request $request)
    {
        $cacheKey = $request->query('key');

        if (!$cacheKey) {
            return redirect()->route('admin.import.form')
                ->with('error', 'Parameter preview tidak valid.');
        }

        $cached = Cache::store('file')->get($cacheKey);
        if (!$cached) {
            session()->forget(['import_active_cache_key', 'import_active_expires_at']);
            return redirect()->route('admin.import.form')
                ->with('error', 'Sesi preview telah kedaluwarsa (lebih dari 60 menit). Silakan upload ulang file Excel.');
        }

        $validData    = $cached['rows'];
        $bulan        = $cached['bulan'];
        $tahun        = $cached['tahun'];
        $gagal        = $cached['gagal']  ?? 0;
        $errors       = $cached['errors'] ?? [];
        $totalValid   = count($validData);
        $totalWarning = count(array_filter($validData, fn($d) => $d['has_warning']));

        return view('admin.import.preview', compact(
            'gagal', 'errors', 'validData', 'totalValid', 'totalWarning',
            'bulan', 'tahun', 'cacheKey'
        ));
    }

    /**
     * Lanjutkan preview dari cache (resume session) — redirect ke showPreview
     */
    public function resume()
    {
        $cacheKey = session('import_active_cache_key');
        if (!$cacheKey) {
            return redirect()->route('admin.import.form')
                ->with('error', 'Tidak ada sesi import yang aktif.');
        }

        $cached = Cache::store('file')->get($cacheKey);
        if (!$cached) {
            session()->forget(['import_active_cache_key', 'import_active_expires_at']);
            return redirect()->route('admin.import.form')
                ->with('error', 'Sesi import telah kedaluwarsa. Silakan upload ulang file Excel.');
        }

        // Redirect ke halaman preview GET agar data persist
        return redirect()->route('admin.import.preview', ['key' => $cacheKey]);
    }

    /**
     * Update nilai PKOK sebuah baris di cache (AJAX)
     */
    public function updateRow(Request $request)
    {
        $request->validate([
            'cache_key' => 'required|string',
            'index'     => 'required|integer|min:0',
            'pkok'      => 'required|numeric|min:0',
        ]);

        $cached = Cache::store('file')->get($request->cache_key);
        if (!$cached) {
            return response()->json(['success' => false, 'message' => 'Sesi import kedaluwarsa.'], 422);
        }

        $index = (int) $request->index;
        if (!isset($cached['rows'][$index])) {
            return response()->json(['success' => false, 'message' => 'Baris tidak ditemukan.'], 422);
        }

        $pkokBaru  = (float) $request->pkok;
        $rpbg      = (float) $cached['rows'][$index]['rpbg'];
        $angsBaru  = $pkokBaru + $rpbg;

        // Update top-level values
        $cached['rows'][$index]['pkok']             = $pkokBaru;
        $cached['rows'][$index]['jumlah_potongan']  = $angsBaru;
        
        // Sync to data_rinci (ensuring we don't overwrite the whole array)
        if (!isset($cached['rows'][$index]['data_rinci'])) {
             $cached['rows'][$index]['data_rinci'] = [];
        }
        $cached['rows'][$index]['data_rinci']['PKOK'] = $pkokBaru;
        $cached['rows'][$index]['data_rinci']['ANGS'] = $angsBaru;

        // Re-calculate warning based on discrepancy with ORIGINAL excel_angs
        $excelAngs = $cached['rows'][$index]['excel_angs'];
        $cached['rows'][$index]['has_warning'] = (abs($excelAngs - $angsBaru) > 1 && $pkokBaru > 0);

        Cache::store('file')->put($request->cache_key, $cached, now()->addMinutes(60));

        return response()->json([
            'success'          => true,
            'pkok'             => $pkokBaru,
            'rpbg'             => $rpbg,
            'jumlah_potongan'  => $angsBaru,
            'has_warning'      => $cached['rows'][$index]['has_warning'],
            // Send back the updated data_rinci for verification if needed
            'data_rinci'       => $cached['rows'][$index]['data_rinci'],
        ]);
    }

    /**
     * Hapus sebuah baris dari cache (AJAX)
     */
    public function deleteRow(Request $request)
    {
        $request->validate([
            'cache_key' => 'required|string',
            'index'     => 'required|integer|min:0',
        ]);

        $cached = Cache::store('file')->get($request->cache_key);
        if (!$cached) {
            return response()->json(['success' => false, 'message' => 'Sesi import kedaluwarsa.'], 422);
        }

        $index = (int) $request->index;
        if (!isset($cached['rows'][$index])) {
            return response()->json(['success' => false, 'message' => 'Baris tidak ditemukan.'], 422);
        }

        // Hapus baris dan re-index
        array_splice($cached['rows'], $index, 1);
        Cache::store('file')->put($request->cache_key, $cached, now()->addMinutes(60));

        return response()->json([
            'success'    => true,
            'total_rows' => count($cached['rows']),
        ]);
    }

    public function execute(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(600);
        $request->validate([
            'cache_key' => 'required',
            'bulan'     => 'required|integer',
            'tahun'     => 'required|integer',
        ]);

        if ($request->action === 'batal') {
            Cache::store('file')->forget($request->cache_key);
            session()->forget(['import_active_cache_key', 'import_active_expires_at']);
            return redirect()->route('admin.import.form')->with('info', 'Import dibatalkan oleh pengguna.');
        }

        $cached = Cache::store('file')->get($request->cache_key);
        if (!$cached) {
            return redirect()->route('admin.import.form')->with('error', 'Sesi import telah kedaluwarsa. Silakan upload file Excel kembali.');
        }

        $validData   = $cached['rows'];
        $berhasil    = 0;
        $diupdate    = 0;
        $mappingData = [];

        DB::beginTransaction();

        try {
            foreach ($validData as $data) {
                $record = InputBulanan::updateOrCreate(
                    [
                        'karyawan_id'       => $data['karyawan_id'],
                        'jenis_potongan_id' => $data['jenis_potongan_id'],
                        'bulan'             => $request->bulan,
                        'tahun'             => $request->tahun,
                    ],
                    [
                        'jumlah_potongan' => $data['jumlah_potongan'],
                        'data_rinci'      => $data['data_rinci'],
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
            Cache::store('file')->forget($request->cache_key);
            session()->forget(['import_active_cache_key', 'import_active_expires_at']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.import.form')->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }

        $gagal  = 0;
        $errors = [];
        $bulan  = $request->bulan;
        $tahun  = $request->tahun;
        return view('admin.import.result', compact('berhasil', 'diupdate', 'gagal', 'errors', 'bulan', 'tahun'));
    }

    public function collectiveStore(Request $request)
    {
        $validated = $request->validate([
            'jenis_potongan_id'            => 'required|exists:jenis_potongan,id',
            'bulan'                        => 'required|integer|between:1,12',
            'tahun'                        => 'required|integer|min:2020|max:2099',
            'potongan'                     => 'required|array',
            'potongan.*.karyawan_id'       => 'required|exists:karyawan,id',
            'potongan.*.jumlah'            => 'nullable|numeric|min:0',
        ]);

        $count = 0;
        foreach ($validated['potongan'] as $item) {
            if ($item['jumlah'] !== null && $item['jumlah'] > 0) {
                InputBulanan::updateOrCreate(
                    [
                        'karyawan_id'       => $item['karyawan_id'],
                        'jenis_potongan_id' => $validated['jenis_potongan_id'],
                        'bulan'             => $validated['bulan'],
                        'tahun'             => $validated['tahun'],
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
