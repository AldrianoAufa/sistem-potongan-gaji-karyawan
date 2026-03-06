@extends('layouts.admin')
@section('title', 'Data Potongan')

@push('styles')
<style>
/* ===== Back to Top Button ===== */
#backToTop {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 1040;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4A90D9 0%, #2563eb 100%);
    color: #fff;
    border: none;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    cursor: pointer;
    opacity: 0;
    transform: translateY(16px) scale(0.85);
    transition: opacity 0.28s ease, transform 0.28s ease, box-shadow 0.2s ease;
    pointer-events: none;
}
#backToTop.visible {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
#backToTop:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
    transform: translateY(-2px) scale(1.07);
}
#backToTop:active {
    transform: scale(0.95);
}


/* ===== Loading Skeleton ===== */
#loadingOverlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1055;
    background: rgba(255, 255, 255, 0.72);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 18px;
}
#loadingOverlay.active {
    display: flex;
}
.loading-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    padding: 36px 48px;
    text-align: center;
    min-width: 260px;
}
.loading-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 18px;
}
.loading-dots span {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4A90D9;
    display: inline-block;
    animation: dotBounce 1.2s infinite ease-in-out;
}
.loading-dots span:nth-child(1) { animation-delay: 0s;    background: #4A90D9; }
.loading-dots span:nth-child(2) { animation-delay: 0.2s;  background: #6ab4ff; }
.loading-dots span:nth-child(3) { animation-delay: 0.4s;  background: #a0cfff; }

@keyframes dotBounce {
    0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
    40%           { transform: scale(1.2); opacity: 1;   }
}

/* ===== Skeleton baris tabel ===== */
.skeleton-table tbody tr td {
    padding: 12px 16px;
}
.skel {
    height: 14px;
    border-radius: 6px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
}
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Fade in table rows */
.data-table tbody tr {
    animation: fadeInRow 0.3s ease both;
}
@keyframes fadeInRow {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0);   }
}

/* ===== Info bar ===== */
.info-bar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.82rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>
@endpush

@section('content')

{{-- Loading Overlay --}}
<div id="loadingOverlay">
    <div class="loading-card">
        <div class="loading-dots">
            <span></span><span></span><span></span>
        </div>
        <div class="fw-semibold text-dark mb-1" style="font-size: 1rem;">Memuat Data...</div>
        <div class="text-muted" style="font-size: 0.82rem;">Mohon tunggu sebentar</div>
    </div>
</div>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4><i class="bi bi-cash-coin me-2"></i>Data Potongan</h4>
</div>

{{-- Filter --}}
<div class="card card-custom mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.input-bulanan.index') }}"
              class="row g-2 align-items-end" id="filterForm">
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
                <label class="form-label mb-0" style="font-size: 0.8rem;">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nama/kode..."
                       value="{{ request('search') }}" style="width: 180px;">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary btn-sm" type="submit">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.input-bulanan.index') }}"
                   class="btn btn-outline-secondary btn-sm"
                   onclick="showLoading()">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    {{-- Info bar --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
        <div class="info-bar">
            <i class="bi bi-table text-primary"></i>
            Menampilkan <strong class="mx-1 text-dark">{{ $inputBulanan->count() }}</strong> data
            @if(request('bulan') || request('tahun') || request('search'))
                <span class="text-muted">(terfilter)</span>
            @endif
        </div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0 data-table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Kode</th>
                        <th>Nama Karyawan</th>
                        <th>Jenis Potongan</th>
                        <th>Bulan/Tahun</th>
                        <th class="text-end">Jumlah</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inputBulanan as $i => $item)
                    <tr style="animation-delay: {{ min($i * 0.03, 0.6) }}s">
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $item->karyawan->kode_karyawan }}</span></td>
                        <td class="fw-medium">{{ $item->karyawan->nama }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $item->jenisPotongan->kode_potongan }}</span>
                            <span class="text-muted small ms-1">{{ $item->jenisPotongan->nama_potongan }}</span>
                        </td>
                        <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.input-bulanan.edit', $item) }}"
                               class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.input-bulanan.destroy', $item) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data potongan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($inputBulanan->count() > 0)
                <tfoot>
                    <tr class="fw-bold table-light">
                        <td colspan="5" class="text-end">Total Potongan:</td>
                        <td class="text-end text-primary">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Tambah Modal --}}
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.input-bulanan.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Potongan Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                            <select name="karyawan_id" class="form-select" required>
                                <option value="">-- Pilih karyawan --</option>
                                @foreach($karyawanList as $a)
                                <option value="{{ $a->id }}">{{ $a->kode_karyawan }} — {{ $a->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Potongan <span class="text-danger">*</span></label>
                            <select name="jenis_potongan_id" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisPotonganList as $jp)
                                <option value="{{ $jp->id }}">{{ $jp->kode_potongan }} — {{ $jp->nama_potongan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                                <option value="{{ $i+1 }}" {{ now()->month == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select" required>
                                @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Potongan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_potongan" class="form-control" min="0" step="1" required>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Detail Pinjaman (Opsional)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Pinjaman (PINJ)</label>
                            <input type="number" name="data_rinci[PINJ]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Saldo Awal (AWAL)</label>
                            <input type="number" name="data_rinci[AWAL]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Bulan Ke (BULN)</label>
                            <input type="number" name="data_rinci[BULN]" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Total Kali (KALI)</label>
                            <input type="number" name="data_rinci[KALI]" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Pokok (PKOK)</label>
                            <input type="number" name="data_rinci[PKOK]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Bunga (RPBG)</label>
                            <input type="number" name="data_rinci[RPBG]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Sisa Saldo (SALD)</label>
                            <input type="number" name="data_rinci[SALD]" class="form-control form-control-sm" step="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Back to Top Button --}}
<button id="backToTop" title="Kembali ke atas" aria-label="Scroll ke atas">
    <i class="bi bi-arrow-up"></i>
</button>

@push('scripts')
<script>
// Tampilkan loading overlay saat filter form di-submit
function showLoading() {
    document.getElementById('loadingOverlay').classList.add('active');
}

document.getElementById('filterForm').addEventListener('submit', function () {
    showLoading();
});

// Jika ada redirect/flash setelah delete atau store, juga tampilkan loading saat halaman unload
window.addEventListener('beforeunload', function (e) {
    // Hanya jika bukan karena menutup tab (heuristic)
    const activeEl = document.activeElement;
    if (activeEl && (activeEl.tagName === 'A' || activeEl.form)) {
        showLoading();
    }
});

// Sembunyikan overlay secara otomatis saat halaman sudah selesai dimuat
// ===== Back to Top =====
const backToTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', function () {
    if (window.scrollY > 300) {
        backToTopBtn.classList.add('visible');
    } else {
        backToTopBtn.classList.remove('visible');
    }
}, { passive: true });

backToTopBtn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

window.addEventListener('load', function () {
    document.getElementById('loadingOverlay').classList.remove('active');
});
</script>
@endpush
