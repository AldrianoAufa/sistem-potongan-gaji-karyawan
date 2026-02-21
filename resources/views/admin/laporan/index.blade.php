@extends('layouts.admin')
@section('title', 'Laporan Potongan Gaji')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Laporan Potongan Gaji</h4>
</div>

<!-- Filter -->
<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                    <option value="{{ $i+1 }}" {{ request('bulan') == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Tahun</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Jenis Potongan</label>
                <select name="jenis_potongan_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($jenisPotonganList as $jp)
                    <option value="{{ $jp->id }}" {{ request('jenis_potongan_id') == $jp->id ? 'selected' : '' }}>
                        {{ $jp->nama_potongan }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Anggota</label>
                <select name="anggota_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($anggotaList as $a)
                    <option value="{{ $a->id }}" {{ request('anggota_id') == $a->id ? 'selected' : '' }}>
                        {{ $a->kode_anggota }} — {{ $a->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Total -->
<div class="alert alert-info mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calculator me-2"></i>Total Potongan (sesuai filter):</span>
        <strong style="font-size: 1.1rem;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</strong>
    </div>
</div>

<div class="row g-3">
    <!-- Data Table -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header">Detail Potongan</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Bulan/Tahun</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporan as $i => $item)
                            <tr>
                                <td>{{ $laporan->firstItem() + $i }}</td>
                                <td><span class="badge bg-light text-dark">{{ $item->anggota->kode_anggota }}</span></td>
                                <td>{{ $item->anggota->nama }}</td>
                                <td><span class="badge bg-primary">{{ $item->jenisPotongan->kode_potongan }}</span></td>
                                <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($laporan->hasPages())
            <div class="card-footer bg-white">{{ $laporan->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Summary by Type -->
    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Ringkasan per Jenis</div>
            <div class="card-body">
                @forelse($ringkasan as $item)
                @php
                    $persen = $totalPotongan > 0 ? ($item->total / $totalPotongan * 100) : 0;
                    $colors = ['#1E3A5F','#4A90D9','#28A745','#FFC107','#DC3545','#17A2B8','#6F42C1','#FD7E14'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-semibold">{{ $item->jenisPotongan->nama_potongan ?? 'Unknown' }}</small>
                        <small>{{ number_format($persen, 1) }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" style="width: {{ $persen }}%; background: {{ $color }};"></div>
                    </div>
                    <small class="text-muted">Rp {{ number_format($item->total, 0, ',', '.') }}</small>
                </div>
                @empty
                <p class="text-muted text-center">Tidak ada data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
