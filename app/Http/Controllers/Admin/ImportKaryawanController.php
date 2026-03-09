<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ImportkaryawanController extends Controller
{
    private const EXPECTED_HEADERS = ['NIK', 'NAMA', 'JABATAN', 'DEPARTEMEN'];

    public function showForm()
    {
        return view('admin.import-karyawan.index');
    }

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        foreach (self::EXPECTED_HEADERS as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
        }

        // Add an example row
        $sheet->setCellValue('A2', '123456');
        $sheet->setCellValue('B2', 'Budi Santoso');
        $sheet->setCellValue('C2', 'Staff Utama');
        $sheet->setCellValue('D2', 'IT');

        // Auto size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="template_import_karyawan.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }


    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
            'buat_akun' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $buatAkun = $request->boolean('buat_akun');

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, false, true);
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

        // Validate required headers
        $missingHeaders = array_diff(self::EXPECTED_HEADERS, $headers);
        if (!empty($missingHeaders)) {
            return back()->with('error', 'Header kolom tidak sesuai! Kolom yang hilang: ' . implode(', ', $missingHeaders));
        }

        // No need to check for username/password columns — auto-generated from NIK

        // Map header positions
        $headerMap = array_flip($headers);

        // Cache existing data
        $existingKode = Karyawan::pluck('kode_karyawan')->toArray();
        $existingUsernames = User::pluck('username')->toArray();
        $jabatanMap = Jabatan::pluck('id', 'nama_jabatan')->toArray();
        $departemenMap = Departemen::pluck('id', 'kode_departemen')->toArray();

        $berhasil = 0;
        $diperbarui = 0;
        $gagal = 0;
        $jabatanBaru = 0;
        $departemenBaru = 0;
        $akunDibuat = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $rowIndex => $row) {
                $rowValues = array_values($row);
                $rowNum = $rowIndex + 2; // +2 karena header = baris 1

                // Skip empty rows
                if (empty(array_filter($rowValues, fn($v) => $v !== null && $v !== ''))) {
                    continue;
                }

                $kode = trim($rowValues[$headerMap['NIK']] ?? '');
                $nama = trim($rowValues[$headerMap['NAMA']] ?? '');
                $jabatanNama = trim($rowValues[$headerMap['JABATAN']] ?? '');
                $departemenKode = trim($rowValues[$headerMap['DEPARTEMEN']] ?? '');

                // Validate required fields
                if (empty($kode)) {
                    $errors[] = ['baris' => $rowNum, 'nik' => '-', 'error' => 'NIK kosong'];
                    $gagal++;
                    continue;
                }

                if (empty($nama)) {
                    $errors[] = ['baris' => $rowNum, 'nik' => $kode, 'error' => 'Nama karyawan kosong'];
                    $gagal++;
                    continue;
                }

                if (empty($jabatanNama)) {
                    $errors[] = ['baris' => $rowNum, 'nik' => $kode, 'error' => 'Nama jabatan kosong'];
                    $gagal++;
                    continue;
                }

                if (empty($departemenKode)) {
                    $errors[] = ['baris' => $rowNum, 'nik' => $kode, 'error' => 'Kode departemen kosong'];
                    $gagal++;
                    continue;
                }

                // Find or create jabatan
                if (!isset($jabatanMap[$jabatanNama])) {
                    $jabatan = Jabatan::create(['nama_jabatan' => $jabatanNama]);
                    $jabatanMap[$jabatanNama] = $jabatan->id;
                    $jabatanBaru++;
                }
                $jabatanId = $jabatanMap[$jabatanNama];

                // Find or create departemen
                if (!isset($departemenMap[$departemenKode])) {
                    $departemen = Departemen::create([
                        'kode_departemen' => $departemenKode,
                        'nama_departemen' => $departemenKode, // Use code as name for now if new
                    ]);
                    $departemenMap[$departemenKode] = $departemen->id;
                    $departemenBaru++;
                }
                $departemenId = $departemenMap[$departemenKode];

                // Smart Merge Logic: Check if NIK is numeric and has a "base" version already in DB
                $targetKaryawan = null;
                $isNumeric = is_numeric($kode);
                $baseKode = $isNumeric ? (string)floor((float)$kode) : $kode;

                // Try exact match first
                $targetKaryawan = Karyawan::where('kode_karyawan', $kode)->first();

                // If no exact match and numeric, try matching by the "base" (integer part)
                if (!$targetKaryawan && $isNumeric) {
                    // Find any existing karyawan whose NIK has the same integer base
                    // e.g., if we are importing 4994.24, check if 4994 exists.
                    // Or if we are importing 4994, check if 4994.something exists.
                    $targetKaryawan = Karyawan::where('kode_karyawan', $baseKode)
                        ->orWhere('kode_karyawan', 'like', $baseKode . '.%')
                        ->first();
                    
                    if ($targetKaryawan) {
                        // If we found a base match, and the new NIK is "more specific" (has decimal), 
                        // update the old one's NIK to the new one.
                        // Logic: 4994.24 is better than 4994.
                        $existingNik = $targetKaryawan->kode_karyawan;
                        $hasDecimalNew = (strpos((string)$kode, '.') !== false);
                        $hasDecimalOld = (strpos((string)$existingNik, '.') !== false);

                        if ($hasDecimalNew && !$hasDecimalOld) {
                            $targetKaryawan->kode_karyawan = $kode;
                        }
                    }
                }

                if ($targetKaryawan) {
                    $targetKaryawan->update([
                        'nama' => $nama,
                        'jabatan_id' => $jabatanId,
                        'departemen_id' => $departemenId,
                    ]);
                    $diperbarui++;
                    $karyawan = $targetKaryawan;
                } else {
                    $karyawan = Karyawan::create([
                        'kode_karyawan' => $kode,
                        'nama' => $nama,
                        'jabatan_id' => $jabatanId,
                        'departemen_id' => $departemenId,
                    ]);
                    $berhasil++;
                }

                // Auto-create user account using NIK as username & password
                if ($buatAkun) {
                    $username = $karyawan->kode_karyawan; // Use the current (possibly updated) NIK
                    if ($karyawan->user) {
                        // Already has account, sync both username and password with current NIK
                        $karyawan->user->update([
                            'username' => $username,
                            'password' => $username
                        ]);
                    } elseif (User::where('username', $username)->exists()) {
                        $errors[] = ['baris' => $rowNum, 'nik' => $kode, 'error' => "Username '{$username}' sudah digunakan"];
                    } else {
                        User::create([
                            'username' => $username,
                            'password' => $username,
                            'role' => 'user',
                            'karyawan_id' => $karyawan->id,
                        ]);
                        $akunDibuat++;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }

        return view('admin.import-karyawan.result', compact(
            'berhasil', 'diperbarui', 'gagal', 'jabatanBaru', 'departemenBaru', 'akunDibuat', 'errors'
        ));
    }
}
