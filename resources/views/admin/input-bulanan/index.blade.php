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

/* Menghapus animasi untuk performa data besar */
.data-table tbody tr {
    /* animation: fadeInRow 0.3s ease both; */
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
                <label class="form-label mb-0" style="font-size: 0.8rem;">Tampilkan</label>
                <select name="per_page" class="form-select form-select-sm" style="width: 100px;" onchange="this.form.submit()">
                    @foreach([25, 50, 100, 500, 1000] as $opt)
                    <option value="{{ $opt }}" {{ request('per_page') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary btn-sm" type="submit">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="{{ route('admin.input-bulanan.index') }}"
                   class="btn btn-outline-secondary btn-sm">Reset</a>
                <a href="{{ route('admin.input-bulanan.index', ['bulan' => '', 'tahun' => '', 'per_page' => request('per_page')]) }}"
                   class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-circle me-1"></i>Bersihkan Filter
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    {{-- Info bar --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
        <div class="info-bar">
            <i class="bi bi-table text-primary"></i>
            Menampilkan <strong class="mx-1 text-dark">{{ $inputBulanan->firstItem() ?? 0 }}–{{ $inputBulanan->lastItem() ?? 0 }}</strong>
            dari <strong class="mx-1 text-dark">{{ $inputBulanan->total() }}</strong> data
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
                        <th width="30">No</th>
                        <th width="70">KDPR</th>
                        <th width="120">NMPR</th>
                        <th width="90">CUST</th>
                        <th width="150">NAMA</th>
                        <th width="60">GRUP</th>
                        <th width="140">NMGR</th>
                        <th class="text-end" width="100">PINJ</th>
                        <th class="text-end" width="100">AWAL</th>
                        <th width="50" class="text-center">BULN</th>
                        <th width="50" class="text-center">KALI</th>
                        <th class="text-end" width="100">PKOK</th>
                        <th class="text-end" width="100">RPBG</th>
                        <th class="text-end" width="100">ANGS</th>
                        <th class="text-end" width="100">SALD</th>
                        <th width="90">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inputBulanan as $i => $item)
                    @php $rinci = $item->data_rinci ?? []; @endphp
                    <tr style="animation-delay: {{ min($i * 0.03, 0.6) }}s; font-size: 0.85rem;">
                        <td class="text-muted small">{{ $rinci['URUT'] ?? ($inputBulanan->firstItem() + $i) }}</td>
                        <td>{{ $rinci['KDPR'] ?? '-' }}</td>
                        <td class="small">{{ $rinci['NMPR'] ?? '-' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $rinci['CUST'] ?? ($item->karyawan->kode_karyawan ?? '-') }}</span></td>
                        <td class="fw-medium">{{ $rinci['NAMA'] ?? ($item->karyawan->nama ?? '-') }}</td>
                        <td><span class="badge bg-primary">{{ $rinci['GRUP'] ?? ($item->jenisPotongan->kode_potongan ?? '-') }}</span></td>
                        <td class="small">{{ $rinci['NMGR'] ?? ($item->jenisPotongan->nama_potongan ?? '-') }}</td>
                        <td class="text-end">{{ number_format($rinci['PINJ'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($rinci['AWAL'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $rinci['BULN'] ?? '-' }}</td>
                        <td class="text-center">{{ $rinci['KALI'] ?? '-' }}</td>
                        <td class="text-end">{{ number_format($rinci['PKOK'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($rinci['RPBG'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($rinci['SALD'] ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.input-bulanan.edit', $item) }}"
                                   class="btn btn-warning btn-sm p-1" style="line-height: 1;">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.input-bulanan.destroy', $item) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm p-1" style="line-height: 1;"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="16" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Tidak ada data potongan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($inputBulanan->count() > 0)
                <tfoot>
                    <tr class="fw-bold table-light">
                        <td colspan="13" class="text-end small">Total Angsuran (ANGS):</td>
                        <td class="text-end text-primary">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($inputBulanan->hasPages())
    <div class="card-footer bg-white border-top py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted" style="font-size: 0.82rem;">
            Halaman {{ $inputBulanan->currentPage() }} dari {{ $inputBulanan->lastPage() }}
        </div>
        <div>
            {{ $inputBulanan->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
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
                            <label class="form-label fw-semibold">Jumlah Potongan (Rp) <span class="text-secondary small">(Otomatis PKOK+RPBG)</span></label>
                            <input type="number" name="jumlah_potongan" class="form-control bg-light" min="0" step="1" readonly>
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

// Auto-calculate Total in Modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('tambahModal');
    if (modal) {
        const pkokInput = modal.querySelector('input[name="data_rinci[PKOK]"]');
        const rpbgInput = modal.querySelector('input[name="data_rinci[RPBG]"]');
        const totalInput = modal.querySelector('input[name="jumlah_potongan"]');

        function calculateTotal() {
            const pkok = parseFloat(pkokInput.value) || 0;
            const rpbg = parseFloat(rpbgInput.value) || 0;
            totalInput.value = (pkok + rpbg).toFixed(2);
        }

        if (pkokInput && rpbgInput && totalInput) {
            pkokInput.addEventListener('input', calculateTotal);
            rpbgInput.addEventListener('input', calculateTotal);
        }
    }
});
</script>
@endpush
