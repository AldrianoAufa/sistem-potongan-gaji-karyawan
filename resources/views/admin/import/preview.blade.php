@extends('layouts.admin')
@section('title', 'Preview Data Import')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-eye text-primary me-2"></i>Preview Data Import</h4>
    <p class="text-muted">Periksa kembali data yang akan di-import. Sistem menemukan kemungkinan ketidaksesuaian perhitungan pada file Excel Anda.</p>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #17A2B8;">
            <div class="card-body">
                <div style="font-size: 2rem; font-weight: 700; color: #17A2B8;">{{ $totalValid }}</div>
                <div class="text-muted">Total Baris Valid Ditemukan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #FD7E14;">
            <div class="card-body">
                <div style="font-size: 2rem; font-weight: 700; color: #FD7E14;">{{ count($warnings) }}</div>
                <div class="text-muted">Baris dengan Peringatan (Hitungan)</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #DC3545;">
            <div class="card-body">
                <div style="font-size: 2rem; font-weight: 700; color: #DC3545;">{{ $gagal }}</div>
                <div class="text-muted">Baris Error (Diabaikan)</div>
            </div>
        </div>
    </div>
</div>

@if(count($errors) > 0)
<div class="alert alert-danger mb-4">
    <h5><i class="bi bi-x-circle-fill me-2"></i>Terdapat {{ $gagal }} Baris Error Fatal</h5>
    <p class="mb-2">Baris di bawah ini <strong>tidak akan diproses</strong> karena datanya tidak valid (misal: NIK kosong atau salah).</p>
    <ul class="mb-0 text-sm">
        @foreach(array_slice($errors, 0, 5) as $err)
            <li>Baris {{ $err['baris'] }} (NIK: {{ $err['kode'] }}): {{ $err['error'] }}</li>
        @endforeach
        @if(count($errors) > 5)
            <li><em>Dan {{ count($errors) - 5 }} error lainnya...</em></li>
        @endif
    </ul>
</div>
@endif

@if(count($warnings) > 0)
<div class="card card-custom mb-4 border-warning">
    <div class="card-header bg-warning text-dark border-bottom-0 pb-0">
        <span class="fs-5 fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Kesalahan Hitung di Excel</span>
    </div>
    <div class="card-body mt-2">
        <p>Sistem menemukan nilai <strong>Angsuran</strong> / <strong>Saldo</strong> pada Excel berbeda dengan hitungan sistem (Kolom Pokok + Bunga). Anda dapat melihat rinciannya di bawah ini:</p>
        <div class="table-responsive">
            <table class="table table-custom table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">Baris</th>
                        <th width="15%">NIK</th>
                        <th width="20%">Nama</th>
                        <th width="15%" class="text-end">Angka di Excel</th>
                        <th width="15%" class="text-end">Hitungan Sistem</th>
                        <th width="30%">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warnings as $warn)
                    <tr>
                        <td class="text-center"><span class="badge bg-secondary">{{ $warn['baris'] }}</span></td>
                        <td>{{ $warn['kode'] }}</td>
                        <td>{{ $warn['nama'] }}</td>
                        <td class="text-danger text-end fw-bold">Rp {{ number_format($warn['excel_angs'], 0, ',', '.') }}</td>
                        <td class="text-success text-end fw-bold">Rp {{ number_format($warn['sistem_angs'], 0, ',', '.') }}</td>
                        <td class="small">{{ $warn['pesan'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert alert-success">
    <h5><i class="bi bi-check-circle-fill me-2"></i>Data Valid dan Sesuai</h5>
    <p class="mb-0">Tidak ditemukan kesalahan perhitungan (Pokok + Bunga = Angsuran) pada file Excel Anda. Data siap untuk di-import seluruhnya.</p>
</div>
@endif

<div class="card card-custom p-4">
    <h5 class="mb-3">Tindakan Selanjutnya</h5>
    <form action="{{ route('admin.import.execute') }}" method="POST">
        @csrf
        <input type="hidden" name="cache_key" value="{{ $cacheKey }}">
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <div class="d-flex flex-column flex-md-row gap-3">
            @if(count($warnings) > 0)
            <button type="submit" name="action" value="koreksi" class="btn btn-success d-flex align-items-center justify-content-center">
                <i class="bi bi-stars me-2 fs-5"></i> 
                <div class="text-start">
                    <span class="d-block fw-bold">Setuju & Lanjutkan</span>
                    <small>Gunakan hasil koreksi hitungan sistem</small>
                </div>
            </button>
            <button type="submit" name="action" value="ignore" class="btn btn-warning d-flex align-items-center justify-content-center text-dark border">
                <i class="bi bi-file-earmark-spreadsheet me-2 fs-5"></i>
                <div class="text-start">
                    <span class="d-block fw-bold">Tetap Pakai Angka Excel</span>
                    <small>Abaikan peringatan di atas</small>
                </div>
            </button>
            @else
            <button type="submit" name="action" value="ignore" class="btn btn-primary d-flex align-items-center justify-content-center">
                <i class="bi bi-save me-2 fs-5"></i>
                <div class="text-start">
                    <span class="d-block fw-bold">Simpan Import Data</span>
                    <small>Simpan {{ $totalValid }} baris data</small>
                </div>
            </button>
            @endif

            <button type="submit" name="action" value="batal" class="btn btn-outline-danger d-flex align-items-center justify-content-center ms-md-auto mt-3 mt-md-0" onclick="return confirm('Yakin ingin membatalkan import data ini?');">
                <i class="bi bi-x-circle me-2 fs-5"></i> Batal Semua
            </button>
        </div>
    </form>
</div>
@endsection
