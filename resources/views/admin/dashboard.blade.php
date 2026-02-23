@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-grid-1x2-fill me-2"></i>Dashboard</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <span class="text-muted" style="font-size: 0.85rem;">
        <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
    </span>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(30,58,95,0.1); color: #1E3A5F;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalkaryawan) }}</div>
                    <div class="stat-label">Total karyawan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(40,167,69,0.1); color: #28A745;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="stat-value">{{ 'Rp ' . number_format($totalPotonganBulanIni, 0, ',', '.') }}</div>
                    <div class="stat-label">Potongan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: rgba(23,162,184,0.1); color: #17A2B8;">
                    <i class="bi bi-clipboard2-data-fill"></i>
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
                <div class="stat-icon me-3" style="background: rgba(255,193,7,0.1); color: #FFC107;">
                    <i class="bi bi-building-fill"></i>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Tren Potongan 6 Bulan Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="chartPotongan" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Potongan Terbaru</span>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama karyawan</th>
                                <th>Jenis Potongan</th>
                                <th>Bulan/Tahun</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($potonganTerbaru as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark">{{ $item->karyawan->kode_karyawan }}</span></td>
                                <td>{{ $item->karyawan->nama }}</td>
                                <td>{{ $item->jenisPotongan->nama_potongan }}</td>
                                <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data potongan</td>
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

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: grafikData.map(d => d.label),
            datasets: [{
                label: 'Total Potongan (Rp)',
                data: grafikData.map(d => d.total),
                backgroundColor: 'rgba(74, 144, 217, 0.7)',
                borderColor: '#4A90D9',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
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
</script>
@endpush
