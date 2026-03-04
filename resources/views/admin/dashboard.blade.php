@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4 class="mb-1"><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Admin Dashboard</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Permulaan</a></li>
                <li class="breadcrumb-item active">Ikhtisar Utama</li>
            </ol>
        </nav>
    </div>
    <div class="d-none d-sm-flex px-3 py-2 bg-white rounded-3 shadow-sm border border-light align-items-center" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">
        <i class="bi bi-calendar3 me-2 text-primary"></i>{{ now()->translatedFormat('l, d F Y') }}
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(19, 127, 236, 0.1); color: var(--primary);">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalkaryawan) }}</div>
                    <div class="stat-label">Total Karyawan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="stat-value text-success">{{ 'Rp ' . number_format($totalPotonganBulanIni / 1000000, 1, ',', '.') }}<span style="font-size:0.9rem; margin-left:2px;">jt</span></div>
                    <div class="stat-label">Potongan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalJenisPotongan }}</div>
                    <div class="stat-label">Jenis Potongan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                    <i class="bi bi-building-fill-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalJabatan }}</div>
                    <div class="stat-label">Total Jabatan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom-0 pb-0">
                <span class="fs-5"><i class="bi bi-bar-chart-fill me-2" style="color: var(--primary);"></i>Tren Potongan 6 Bulan Terakhir</span>
            </div>
            <div class="card-body pt-2">
                <div style="height: 320px; width: 100%;">
                    <canvas id="chartPotongan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fs-5"><i class="bi bi-clock-history me-2" style="color: var(--primary);"></i>Riwayat Potongan Terbaru</span>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm text-primary fw-bold" style="background: var(--primary-light);">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Jenis Potongan</th>
                                <th>Periode</th>
                                <th class="text-end">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($potonganTerbaru as $i => $item)
                            <tr>
                                <td class="text-muted fw-bold">{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->karyawan->kode_karyawan }}</span></td>
                                <td class="fw-bold">{{ $item->karyawan->nama }}</td>
                                <td><span class="badge" style="background: var(--primary-light); color: var(--primary);">{{ $item->jenisPotongan->nama_potongan }}</span></td>
                                <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                                <td class="text-end fw-bold" style="color: var(--text-main);">{{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted disabled mb-2"><i class="bi bi-inbox fs-1"></i></div>
                                    <div class="fw-bold fs-6">Belum ada data potongan</div>
                                    <div class="small">Input data via menu Impor Excel.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartPotongan').getContext('2d');
    const grafikData = @json($grafikData);

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(19, 127, 236, 0.85)');
    gradient.addColorStop(1, 'rgba(19, 127, 236, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: grafikData.map(d => d.label),
            datasets: [{
                label: 'Total Potongan (Rp)',
                data: grafikData.map(d => d.total),
                backgroundColor: gradient,
                hoverBackgroundColor: '#0f66be',
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    padding: 12,
                    titleFont: { family: 'Manrope', size: 14, weight: 'bold' },
                    bodyFont: { family: 'Manrope', size: 13 },
                    displayColors: false,
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: 'Manrope', weight: '600' }, color: '#64748b' }
                },
                y: {
                    grid: { color: '#f1f5f9', drawBorder: false },
                    border: { display: false },
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Manrope', weight: '500' },
                        color: '#94a3b8',
                        padding: 10,
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
