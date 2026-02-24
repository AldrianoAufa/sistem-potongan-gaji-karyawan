@extends('layouts.user')
@section('title', 'Riwayat Potongan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h4><i class="bi bi-clock-history me-2"></i>Riwayat Potongan Gaji</h4>
        <p class="text-muted">Riwayat potongan gaji Anda</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card card-custom mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('user.potongan.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Bulan</label>
                <select name="bulan" class="form-select form-select-sm" style="width: 130px;">
                    <option value="">Semua</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                    <option value="{{ $i+1 }}" {{ request('bulan') == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Tahun</label>
                <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
                    <option value="">Semua</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Jenis</label>
                <select name="jenis_potongan_id" class="form-select form-select-sm" style="width: 180px;">
                    <option value="">Semua</option>
                    @foreach($jenisPotonganList as $jp)
                    <option value="{{ $jp->id }}" {{ request('jenis_potongan_id') == $jp->id ? 'selected' : '' }}>
                        {{ $jp->nama_potongan }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('user.potongan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan/Tahun</th>
                        <th>Jenis Potongan</th>
                        <th class="text-end">Jumlah</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($potongan as $i => $item)
                    <tr>
                        <td>{{ $potongan->firstItem() + $i }}</td>
                        <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                        <td>
                            <span class="badge bg-primary me-1">{{ $item->jenisPotongan->kode_potongan }}</span>
                            {{ $item->jenisPotongan->nama_potongan }}
                        </td>
                        <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                        <td>
                            @if($item->data_rinci)
                            <a href="{{ route('user.potongan.show', $item) }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @else
                            <span class="text-muted" style="font-size: 0.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data potongan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($potongan->hasPages())
    <div class="card-footer bg-white">{{ $potongan->links() }}</div>
    @endif
</div>

@push('styles')
<style>
    @media print {
        .sidebar, .navbar, .page-header .btn, .page-header .d-flex.gap-2,
        .card-custom.mb-3, .card-footer, .btn-outline-info { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .page-header { margin-bottom: 10px !important; }
    }
</style>
@endpush
@endsection
