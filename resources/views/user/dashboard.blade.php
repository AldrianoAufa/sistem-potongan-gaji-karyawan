@extends('layouts.user')
@section('title', 'Dashboard')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-start">
    <div>
        <h4><i class="bi bi-house-fill me-2"></i>Selamat Datang, {{ $karyawan->nama ?? auth()->user()->username }}</h4>
        @if($karyawan)
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            <span class="badge bg-primary me-1">{{ $karyawan->kode_karyawan }}</span>
            {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
        </p>
        @endif
    </div>
    @if($karyawan && $potonganBulanIni->count() > 0)
    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Cetak
    </button>
    @endif
</div>

@if(!$karyawan)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Akun Anda belum terhubung dengan data karyawan. Silakan hubungi administrator.
</div>
@else

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(40,167,69,0.1); color: #28A745;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="stat-value">Rp {{ number_format($totalPotonganBulanIni, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Potongan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(74,144,217,0.1); color: #4A90D9;">
                    <i class="bi bi-clipboard2-data-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $jenisPotonganAktif }}</div>
                    <div class="stat-label">Jenis Potongan Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(255,193,7,0.1); color: #FFC107;">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <div class="stat-value">{{ now()->translatedFormat('F Y') }}</div>
                    <div class="stat-label">Periode Saat Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="card-header">
                <i class="bi bi-graph-up me-2 text-primary"></i>Tren Potongan 6 Bulan Terakhir
            </div>
            <div class="card-body">
                <canvas id="chartUser" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Komposisi Potongan Bulan Ini
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                @if($potonganBulanIni->count() > 0)
                <canvas id="chartPie" height="200"></canvas>
                @else
                <p class="text-muted text-center mb-0">Belum ada data potongan bulan ini</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Current month breakdown -->
<div class="card card-custom" id="printSection">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-check me-2 text-primary"></i>Rincian Potongan Bulan Ini</span>
        <a href="{{ route('user.potongan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua Riwayat</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Jenis Potongan</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($potonganBulanIni as $item)
                    <tr>
                        <td>
                            <span class="badge bg-primary me-1">{{ $item->jenisPotongan->kode_potongan }}</span>
                            {{ $item->jenisPotongan->nama_potongan }}
                        </td>
                        <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center text-muted py-4">Belum ada potongan bulan ini</td></tr>
                    @endforelse
                </tbody>
                @if($potonganBulanIni->count() > 0)
                <tfoot>
                    <tr class="fw-bold" style="background: #F8F9FA;">
                        <td class="text-end">Total:</td>
                        <td class="text-end text-primary" style="font-size: 1.05rem;">
                            Rp {{ number_format($totalPotonganBulanIni, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($karyawan)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Line Chart - Tren 6 Bulan
    const ctx = document.getElementById('chartUser').getContext('2d');
    const data = @json($grafikData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Total Potongan (Rp)',
                data: data.map(d => d.total),
                borderColor: '#4A90D9',
                backgroundColor: 'rgba(74, 144, 217, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: '#4A90D9',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
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

    // Pie Chart - Komposisi Bulan Ini
    @if($potonganBulanIni->count() > 0)
    const pieCtx = document.getElementById('chartPie').getContext('2d');
    const pieData = @json($pieChartData);

    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: pieData.map(d => d.label),
            datasets: [{
                data: pieData.map(d => d.value),
                backgroundColor: pieData.map(d => d.color),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = ((ctx.parsed / total) * 100).toFixed(1);
                            return ctx.label + ': Rp ' + ctx.parsed.toLocaleString('id-ID') + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
    @endif
</script>
@endif
@endpush

@push('styles')
<style>
    @media print {
        .sidebar, .navbar, .page-header .btn, .btn-outline-primary, .card-header .btn { display: none !important; }
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        .stat-card, .card-custom { break-inside: avoid; }
        canvas { max-height: 200px !important; }
    }
</style>
@endpush
