<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InputBulanan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return view('user.dashboard', [
                'karyawan' => null,
                'totalPotonganBulanIni' => 0,
                'potonganBulanIni' => collect(),
                'availablePeriods' => collect(),
            ]);
        }

        $bulan = now()->month;
        $tahun = now()->year;

        $potonganList = InputBulanan::with('jenisPotongan')
            ->where('karyawan_id', $karyawan->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $totalPotongan = $potonganList->sum('jumlah_potongan');

        // Prepare slip data for preview
        $totalPokok = $potonganList->sum(fn($p) => $p->data_rinci['PKOK'] ?? $p->jumlah_potongan);
        $totalJasa  = $potonganList->sum(fn($p) => $p->data_rinci['RPBG'] ?? 0);
        $terbilang = $this->terbilang((int) $totalPotongan) . ' Rupiah';
        $namaBulan = $this->getMonthName($bulan);

        // Available periods for selection
        $availablePeriods = InputBulanan::where('karyawan_id', $karyawan->id)
            ->selectRaw('bulan, tahun')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'bulan' => $p->bulan,
                    'tahun' => $p->tahun,
                    'label' => $this->getMonthName($p->bulan) . ' ' . $p->tahun
                ];
            });

        return view('user.dashboard', compact(
            'karyawan', 'totalPotongan',
            'potonganList', 'availablePeriods',
            'totalPokok', 'totalJasa', 'terbilang', 'namaBulan', 'bulan', 'tahun'
        ));
    }

    private function getMonthName($month)
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$month];
    }

    private function terbilang(int $angka): string
    {
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan',
                   'Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
                   'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];

        if ($angka < 0) return 'Minus ' . $this->terbilang(abs($angka));
        if ($angka === 0) return 'Nol';
        if ($angka < 20) return $satuan[$angka];
        if ($angka < 100) {
            $puluh = intdiv($angka, 10);
            $sisa  = $angka % 10;
            return $satuan[$puluh] . ' Puluh' . ($sisa ? ' ' . $satuan[$sisa] : '');
        }
        if ($angka < 200) return 'Seratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        if ($angka < 1000) {
            return $satuan[intdiv($angka, 100)] . ' Ratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        }
        if ($angka < 2000) return 'Seribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        if ($angka < 1000000) {
            return $this->terbilang(intdiv($angka, 1000)) . ' Ribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        }
        if ($angka < 1000000000) {
            return $this->terbilang(intdiv($angka, 1000000)) . ' Juta' . ($angka % 1000000 ? ' ' . $this->terbilang($angka % 1000000) : '');
        }
        return $this->terbilang(intdiv($angka, 1000000000)) . ' Milyar' . ($angka % 1000000000 ? ' ' . $this->terbilang($angka % 1000000000) : '');
    }
}
