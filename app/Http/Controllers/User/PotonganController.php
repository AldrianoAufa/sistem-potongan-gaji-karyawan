<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InputBulanan;
use App\Models\JenisPotongan;
use Illuminate\Http\Request;

class PotonganController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return view('user.potongan.index', [
                'potongan' => collect(),
                'jenisPotonganList' => collect(),
                'periodeList' => collect(),
            ]);
        }

        // Row-level security: only show records for this karyawan
        $query = InputBulanan::with('jenisPotongan')
            ->where('karyawan_id', $karyawan->id);

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('jenis_potongan_id')) {
            $query->where('jenis_potongan_id', $request->jenis_potongan_id);
        }

        $potongan = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(15);
        $potongan->appends($request->query());

        $jenisPotonganList = JenisPotongan::orderBy('nama_potongan')->get();

        // Distinct periods for slip buttons
        $periodeList = InputBulanan::where('karyawan_id', $karyawan->id)
            ->selectRaw('bulan, tahun')
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('user.potongan.index', compact('potongan', 'jenisPotonganList', 'periodeList'));
    }

    public function show(InputBulanan $inputBulanan)
    {
        $user = auth()->user();

        // Row-level security: ensure user can only see their own data
        if (!$user->karyawan || $inputBulanan->karyawan_id !== $user->karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $inputBulanan->load(['karyawan', 'jenisPotongan']);

        return view('user.potongan.show', compact('inputBulanan'));
    }

    public function slip(Request $request, $bulan, $tahun)
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            abort(403, 'Akun Anda belum terhubung dengan data karyawan.');
        }

        $bulan = (int) $bulan;
        $tahun = (int) $tahun;

        // Validate range
        if ($bulan < 1 || $bulan > 12 || $tahun < 2000 || $tahun > 2100) {
            abort(404);
        }

        $potonganList = InputBulanan::with('jenisPotongan')
            ->where('karyawan_id', $karyawan->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->orderBy('jenis_potongan_id')
            ->get();

        // Load karyawan relations for slip display
        $karyawan->load(['jabatan', 'departemen']);

        if ($potonganList->isEmpty()) {
            return back()->with('error', 'Tidak ada data potongan untuk periode ' . $this->namaBulan($bulan) . ' ' . $tahun . '.');
        }

        $totalPokok = $potonganList->sum(fn($p) => $p->data_rinci['PKOK'] ?? $p->jumlah_potongan);
        $totalJasa  = $potonganList->sum(fn($p) => $p->data_rinci['RPBG'] ?? 0);
        $totalPotongan = $potonganList->sum('jumlah_potongan');

        $namaBulan = $this->namaBulan($bulan);

        // Terbilang helper
        $terbilang = $this->terbilang((int) $totalPotongan) . ' Rupiah';

        return view('user.potongan.slip', compact(
            'karyawan', 'potonganList', 'bulan', 'tahun',
            'namaBulan', 'totalPokok', 'totalJasa', 'totalPotongan', 'terbilang'
        ));
    }

    private function namaBulan(int $bulan): string
    {
        $names = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',       6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',   9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $names[$bulan] ?? '';
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
