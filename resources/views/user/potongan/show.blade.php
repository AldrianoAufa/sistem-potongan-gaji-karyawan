@extends('layouts.user')
@section('title', 'Detail Potongan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2"></i>Detail Potongan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.potongan.index') }}">Riwayat Potongan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="card card-custom" style="max-width: 700px;">
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="text-muted" style="font-size: 0.8rem;">Jenis Potongan</label>
                <div class="fw-semibold">
                    <span class="badge bg-primary me-1">{{ $inputBulanan->jenisPotongan->kode_potongan }}</span>
                    {{ $inputBulanan->jenisPotongan->nama_potongan }}
                </div>
            </div>
            <div class="col-md-3">
                <label class="text-muted" style="font-size: 0.8rem;">Periode</label>
                <div class="fw-semibold">{{ $inputBulanan->nama_bulan }} {{ $inputBulanan->tahun }}</div>
            </div>
            <div class="col-md-3">
                <label class="text-muted" style="font-size: 0.8rem;">Jumlah Potongan</label>
                <div class="fw-bold text-primary" style="font-size: 1.1rem;">
                    Rp {{ number_format($inputBulanan->jumlah_potongan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        @if($inputBulanan->data_rinci)
        <hr>
        <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Detail Pinjaman</h6>
        @php $r = $inputBulanan->data_rinci; @endphp
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #F8F9FA;">
                    <small class="text-muted d-block">Total Pinjaman</small>
                    <strong>Rp {{ number_format($r['PINJ'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #F8F9FA;">
                    <small class="text-muted d-block">Saldo Awal</small>
                    <strong>Rp {{ number_format($r['AWAL'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #F8F9FA;">
                    <small class="text-muted d-block">Bulan Ke</small>
                    <strong>{{ $r['BULN'] ?? 0 }} / {{ $r['KALI'] ?? 0 }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #F8F9FA;">
                    <small class="text-muted d-block">Angsuran Pokok</small>
                    <strong>Rp {{ number_format($r['PKOK'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #F8F9FA;">
                    <small class="text-muted d-block">Sisa Saldo</small>
                    <strong class="text-danger">Rp {{ number_format($r['SALD'] ?? 0, 0, ',', '.') }}</strong>
                </div>
            </div>
            @if(isset($r['KALI']) && $r['KALI'] > 0)
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: #F8F9FA;">
                    <small class="text-muted d-block">Progress</small>
                    @php $progress = min(100, ($r['BULN'] ?? 0) / $r['KALI'] * 100); @endphp
                    <div class="progress mt-1" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ $progress }}%;"></div>
                    </div>
                    <small>{{ number_format($progress, 0) }}% selesai</small>
                </div>
            </div>
            @endif
        </div>
        @endif

        <div class="mt-4 d-flex gap-2">
            <a href="{{ route('user.potongan.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Cetak
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .sidebar, .navbar, .breadcrumb, .btn { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .card-custom { border: 1px solid #ddd !important; }
    }
</style>
@endpush
@endsection
