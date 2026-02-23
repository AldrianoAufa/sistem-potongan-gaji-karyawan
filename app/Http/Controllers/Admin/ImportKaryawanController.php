<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\Jabatan;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportkaryawanController extends Controller
{
    private const EXPECTED_HEADERS = ['KODE', 'NAMA', 'JABATAN', 'DEPARTEMEN'];
    private const OPTIONAL_HEADERS = ['USERNAME', 'PASSWORD'];

    public function showForm()
    {
        return view('admin.import-karyawan.index');
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

        // Validate required headers
        $missingHeaders = array_diff(self::EXPECTED_HEADERS, $headers);
        if (!empty($missingHeaders)) {
            return back()->with('error', 'Header kolom tidak sesuai! Kolom yang hilang: ' . implode(', ', $missingHeaders));
        }

        // Check if account columns exist when buat_akun is enabled
        if ($buatAkun) {
            $missingAccHeaders = array_diff(self::OPTIONAL_HEADERS, $headers);
            if (!empty($missingAccHeaders)) {
                return back()->with('error', 'Untuk membuat akun, kolom berikut diperlukan: ' . implode(', ', $missingAccHeaders));
            }
        }

        // Map header positions
        $headerMap = array_flip($headers);

        // Cache existing data
        $existingKode = karyawan::pluck('kode_karyawan')->toArray();
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

                $kode = trim($rowValues[$headerMap['KODE']] ?? '');
                $nama = trim($rowValues[$headerMap['NAMA']] ?? '');
                $jabatanNama = trim($rowValues[$headerMap['JABATAN']] ?? '');
                $departemenKode = trim($rowValues[$headerMap['DEPARTEMEN']] ?? '');

                // Validate required fields
                if (empty($kode)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => '-', 'error' => 'NIK kosong'];
                    $gagal++;
                    continue;
                }

                if (empty($nama)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $kode, 'error' => 'Nama karyawan kosong'];
                    $gagal++;
                    continue;
                }

                if (empty($jabatanNama)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $kode, 'error' => 'Nama jabatan kosong'];
                    $gagal++;
                    continue;
                }

                if (empty($departemenKode)) {
                    $errors[] = ['baris' => $rowNum, 'kode' => $kode, 'error' => 'Kode departemen kosong'];
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

                // Check if karyawan already exists — update if so
                if (in_array($kode, $existingKode)) {
                    $karyawan = karyawan::where('kode_karyawan', $kode)->first();
                    $karyawan->update([
                        'nama' => $nama,
                        'jabatan_id' => $jabatanId,
                        'departemen_id' => $departemenId,
                    ]);
                    $diperbarui++;
                } else {
                    // Create new karyawan
                    $karyawan = karyawan::create([
                        'kode_karyawan' => $kode,
                        'nama' => $nama,
                        'jabatan_id' => $jabatanId,
                        'departemen_id' => $departemenId,
                    ]);
                    $existingKode[] = $kode;
                    $berhasil++;
                }

                // Create user account if requested and columns exist
                if ($buatAkun && isset($headerMap['USERNAME']) && isset($headerMap['PASSWORD'])) {
                    $username = trim($rowValues[$headerMap['USERNAME']] ?? '');
                    $password = trim($rowValues[$headerMap['PASSWORD']] ?? '');

                    if (!empty($username) && !empty($password)) {
                        // Skip if already has account or username taken
                        if ($karyawan->user) {
                            // Already has account, skip
                        } elseif (in_array($username, $existingUsernames)) {
                            $errors[] = ['baris' => $rowNum, 'kode' => $kode, 'error' => "Username '{$username}' sudah digunakan (akun tidak dibuat, data karyawan tetap tersimpan)"];
                        } else {
                            User::create([
                                'username' => $username,
                                'password' => $password,
                                'role' => 'user',
                                'karyawan_id' => $karyawan->id,
                            ]);
                            $existingUsernames[] = $username;
                            $akunDibuat++;
                        }
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
