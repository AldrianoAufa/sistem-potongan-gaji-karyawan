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
                'jenisPotonganAktif' => 0,
                'potonganBulanIni' => collect(),
                'grafikData' => [],
            ]);
        }

        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $potonganBulanIni = InputBulanan::with('jenisPotongan')
            ->where('karyawan_id', $karyawan->id)
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->get();

        $totalPotonganBulanIni = $potonganBulanIni->sum('jumlah_potongan');

        $jenisPotonganAktif = $potonganBulanIni->pluck('jenis_potongan_id')->unique()->count();

        // Grafik 6 bulan terakhir
        $grafikData = [];
        $bulanNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                      7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan = $date->month;
            $tahun = $date->year;

            $total = InputBulanan::where('karyawan_id', $karyawan->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->sum('jumlah_potongan');

            $grafikData[] = [
                'label' => $bulanNames[$bulan] . ' ' . $tahun,
                'total' => (float) $total,
            ];
        }

        // Pie chart data bulan ini - per jenis potongan
        $pieChartData = [];
        $pieColors = ['#1E3A5F','#4A90D9','#28A745','#FFC107','#DC3545','#17A2B8','#6F42C1','#FD7E14','#20C997','#E83E8C'];
        $colorIndex = 0;
        foreach ($potonganBulanIni as $item) {
            $pieChartData[] = [
                'label' => $item->jenisPotongan->nama_potongan,
                'value' => (float) $item->jumlah_potongan,
                'color' => $pieColors[$colorIndex % count($pieColors)],
            ];
            $colorIndex++;
        }

        return view('user.dashboard', compact(
            'karyawan', 'totalPotonganBulanIni', 'jenisPotonganAktif',
            'potonganBulanIni', 'grafikData', 'pieChartData'
        ));
    }
}
