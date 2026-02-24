<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use App\Models\karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanController extends Controller
{
    private const BULAN_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

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

        // Tahun yang tersedia di data
        $availableYears = InputBulanan::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('admin.laporan.index', compact(
            'laporan', 'totalPotongan', 'ringkasan',
            'jenisPotonganList', 'karyawanList', 'availableYears'
        ));
    }

    /**
     * Export data as multi-sheet Excel backup.
     * Each month becomes a separate sheet.
     */
    public function exportBackup(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $data = InputBulanan::with(['karyawan', 'jenisPotongan'])
            ->where('tahun', $tahun)
            ->orderBy('bulan')
            ->orderBy('karyawan_id')
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk tahun ' . $tahun);
        }

        $grouped = $data->groupBy('bulan');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default sheet

        $headers = ['No', 'NIK', 'Nama Karyawan', 'Jenis Potongan', 'Kode Potongan',
                     'Jumlah Potongan', 'PINJ', 'AWAL', 'BULN', 'KALI', 'PKOK', 'RPBG', 'SALD'];

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        foreach (self::BULAN_NAMES as $bulanNum => $bulanName) {
            if (!$grouped->has($bulanNum)) continue;

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($bulanName);

            // Title row
            $sheet->setCellValue('A1', "Data Potongan Gaji - {$bulanName} {$tahun}");
            $sheet->mergeCells('A1:M1');
            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Headers at row 3
            foreach ($headers as $colIndex => $header) {
                $col = chr(65 + $colIndex); // A, B, C...
                $sheet->setCellValue("{$col}3", $header);
            }
            $sheet->getStyle('A3:M3')->applyFromArray($headerStyle);
            $sheet->getRowDimension(3)->setRowHeight(25);

            // Data
            $rowNum = 4;
            $no = 1;
            $totalJumlah = 0;

            foreach ($grouped[$bulanNum] as $item) {
                $rinci = $item->data_rinci ?? [];

                $sheet->setCellValue("A{$rowNum}", $no);
                $sheet->setCellValue("B{$rowNum}", $item->karyawan->kode_karyawan ?? '-');
                $sheet->setCellValue("C{$rowNum}", $item->karyawan->nama ?? '-');
                $sheet->setCellValue("D{$rowNum}", $item->jenisPotongan->nama_potongan ?? '-');
                $sheet->setCellValue("E{$rowNum}", $item->jenisPotongan->kode_potongan ?? '-');
                $sheet->setCellValue("F{$rowNum}", $item->jumlah_potongan);
                $sheet->setCellValue("G{$rowNum}", $rinci['PINJ'] ?? 0);
                $sheet->setCellValue("H{$rowNum}", $rinci['AWAL'] ?? 0);
                $sheet->setCellValue("I{$rowNum}", $rinci['BULN'] ?? 0);
                $sheet->setCellValue("J{$rowNum}", $rinci['KALI'] ?? 0);
                $sheet->setCellValue("K{$rowNum}", $rinci['PKOK'] ?? 0);
                $sheet->setCellValue("L{$rowNum}", $rinci['RPBG'] ?? 0);
                $sheet->setCellValue("M{$rowNum}", $rinci['SALD'] ?? 0);

                $totalJumlah += $item->jumlah_potongan;
                $no++;
                $rowNum++;
            }

            // Apply data style
            $sheet->getStyle("A4:M" . ($rowNum - 1))->applyFromArray($dataStyle);

            // Number format for currency columns
            $sheet->getStyle("F4:F" . ($rowNum - 1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("G4:M" . ($rowNum - 1))->getNumberFormat()->setFormatCode('#,##0');

            // Total row
            $sheet->setCellValue("E{$rowNum}", 'TOTAL');
            $sheet->setCellValue("F{$rowNum}", $totalJumlah);
            $sheet->getStyle("E{$rowNum}:F{$rowNum}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("F{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');

            // Auto-size columns
            foreach (range('A', 'M') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        $fileName = "backup_potongan_gaji_{$tahun}.xlsx";
        $tempFile = storage_path("app/{$fileName}");

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Delete old data (older than 6 months) with confirmation.
     */
    public function deleteOldData(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
        ]);

        $tahun = (int) $request->tahun;

        $count = InputBulanan::where('tahun', $tahun)->count();

        if ($count === 0) {
            return back()->with('error', 'Tidak ada data untuk tahun ' . $tahun);
        }

        InputBulanan::where('tahun', $tahun)->delete();

        return back()->with('success', "Berhasil menghapus {$count} data potongan tahun {$tahun}. Pastikan Anda sudah mengunduh backup sebelumnya.");
    }
}
