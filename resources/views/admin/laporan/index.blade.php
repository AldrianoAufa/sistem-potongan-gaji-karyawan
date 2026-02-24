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
                <label class="form-label mb-0" style="font-size: 0.8rem;">Karyawan</label>
                <select name="karyawan_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($karyawanList as $a)
                    <option value="{{ $a->id }}" {{ request('karyawan_id') == $a->id ? 'selected' : '' }}>
                        {{ $a->kode_karyawan }} — {{ $a->nama }}
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
                                <td><span class="badge bg-light text-dark">{{ $item->karyawan->kode_karyawan }}</span></td>
                                <td>{{ $item->karyawan->nama }}</td>
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
        <div class="card card-custom mb-3">
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

        {{-- Backup & Kelola Data --}}
        <div class="card card-custom border-warning">
            <div class="card-header bg-warning bg-opacity-10">
                <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Backup & Kelola Data</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Download backup Excel sebelum menghapus data lama. Setiap bulan menjadi sheet terpisah.
                </p>

                <div class="mb-3">
                    <label class="form-label fw-semibold mb-1" style="font-size: 0.85rem;">Pilih Tahun</label>
                    <select class="form-select form-select-sm" id="backupTahun">
                        @forelse($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                        @empty
                        <option value="">Tidak ada data</option>
                        @endforelse
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-success btn-sm" id="btnDownloadBackup">
                        <i class="bi bi-download me-1"></i>Download Backup Excel
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteData"
                            {{ $availableYears->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-trash me-1"></i>Hapus Data Tahun Ini
                    </button>
                </div>

                <div class="mt-2">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Disarankan hapus data yang sudah lebih dari 6 bulan setelah backup.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h6 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Anda yakin ingin <strong>menghapus semua data potongan</strong> untuk tahun:</p>
                <h3 class="text-center text-danger fw-bold" id="deleteYearDisplay"></h3>
                <div class="alert alert-warning py-2 mb-0 mt-2">
                    <small><i class="bi bi-exclamation-circle me-1"></i>Data yang dihapus tidak bisa dikembalikan. Pastikan Anda sudah mengunduh backup!</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('admin.laporan.delete-old') }}" id="deleteForm">
                    @csrf
                    <input type="hidden" name="tahun" id="deleteTahunInput">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const backupSelect = document.getElementById('backupTahun');
    const btnDownload = document.getElementById('btnDownloadBackup');
    const btnDelete = document.getElementById('btnDeleteData');

    // Update download link when year changes
    function updateDownloadLink() {
        const tahun = backupSelect.value;
        if (tahun) {
            btnDownload.href = "{{ route('admin.laporan.export') }}?tahun=" + tahun;
            btnDownload.classList.remove('disabled');
        } else {
            btnDownload.href = '#';
            btnDownload.classList.add('disabled');
        }
    }

    backupSelect.addEventListener('change', updateDownloadLink);
    updateDownloadLink(); // init

    // Delete button → open modal
    btnDelete.addEventListener('click', function() {
        const tahun = backupSelect.value;
        if (!tahun) return;
        document.getElementById('deleteYearDisplay').textContent = tahun;
        document.getElementById('deleteTahunInput').value = tahun;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
</script>
@endpush
@endsection
