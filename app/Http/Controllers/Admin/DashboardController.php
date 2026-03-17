<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\karyawan;
use App\Models\InputBulanan;
use App\Models\Jabatan;
use App\Models\JenisPotongan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalkaryawan = karyawan::count();
        $totalJabatan = Jabatan::count();
        $totalJenisPotongan = JenisPotongan::count();

        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $totalPotonganBulanIni = InputBulanan::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->sum('jumlah_potongan');

        $latestInput = InputBulanan::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();
        if ($latestInput) {
            $baseDate = \Carbon\Carbon::createFromDate($latestInput->tahun, $latestInput->bulan, 1);
        } else {
            $baseDate = now();
        }

        // Data grafik 6 bulan terakhir
        $grafikData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $baseDate->copy()->subMonths($i);
            $bulan = $date->month;
            $tahun = $date->year;
            $total = InputBulanan::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah_potongan');

            $bulanNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                          7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];

            $grafikData[] = [
                'label' => $bulanNames[$bulan] . ' ' . $tahun,
                'total' => (float) $total,
            ];
        }

        // Potongan terbaru
        $potonganTerbaru = InputBulanan::with(['karyawan', 'jenisPotongan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalkaryawan',
            'totalJabatan',
            'totalJenisPotongan',
            'totalPotonganBulanIni',
            'grafikData',
            'potonganTerbaru'
        ));
    }
}
