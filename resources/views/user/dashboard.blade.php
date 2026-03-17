@extends('layouts.user')
@section('title', 'Beranda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <!-- Welcome Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #1e3a5f 0%, #137fec 100%); color: white;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $karyawan->nama ?? auth()->user()->username }}</h3>
                        <p class="mb-0 opacity-75">{{ $karyawan->kode_karyawan ?? auth()->user()->username }}</p>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-25">
                    <div>
                        <small class="d-block opacity-75">Jabatan</small>
                        <span class="fw-semibold">{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                    </div>
                    <div>
                        <small class="d-block opacity-75 text-end">Departemen</small>
                        <span class="fw-semibold">{{ $karyawan->departemen->nama_departemen ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!$karyawan)
        <div class="alert alert-warning border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Akun Anda belum terhubung dengan data karyawan.
        </div>
        @else

        <!-- MAIN ACTION: SLIP PRINTING -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-4 text-center">
                <h4 class="mb-4 fw-bold">Cetak Slip Bukti Potongan</h4>
                
                <!-- Quick Print Current Month -->
                <div class="mb-5">
                    <p class="text-muted mb-3">Klik tombol di bawah untuk cetak slip bulan ini:</p>
                    @if($potonganList->count() > 0)
                        <div class="d-flex gap-2 mb-3">
                            <button onclick="printSlip()" 
                               class="btn btn-primary btn-lg flex-grow-1 py-3 shadow-sm" 
                               style="border-radius: 12px; font-weight: 700;">
                                <i class="bi bi-printer-fill me-2"></i> CETAK SLIP BULAN INI
                            </button>
                            <a href="{{ route('user.potongan.slip', [now()->month, now()->year]) }}" 
                               class="btn btn-outline-primary btn-lg py-3" 
                               style="border-radius: 12px;" title="Lihat Full">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                        <p class="mt-2 text-success small fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Data periode {{ now()->translatedFormat('F Y') }} sudah tersedia.
                        </p>
                    @else
                        <button class="btn btn-secondary btn-lg w-100 py-3 disabled" style="border-radius: 12px; opacity: 0.6;">
                            Belum Ada Data Bulan Ini
                        </button>
                    @endif
                </div>

                <hr class="my-4">

                <!-- Print Other Months -->
                <div class="mb-2">
                    <p class="text-muted mb-3">Atau pilih periode lainnya untuk dicetak:</p>
                    <div class="mb-3">
                        <select id="selectPeriode" class="form-select form-select-lg text-center" style="border-radius: 12px;">
                            <option value="">-- Klik untuk Pilih Bulan Lain --</option>
                            @foreach($availablePeriods as $p)
                                <option value="{{ route('user.potongan.slip', [$p['bulan'], $p['tahun']]) }}">
                                    {{ $p['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIP PREVIEW -->
        @if($potonganList->count() > 0)
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Preview Slip Bulan Ini</h5>
                <span class="badge bg-light text-primary border">{{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="preview-container" style="background: #f1f5f9; padding: 20px; max-height: 500px; overflow-y: auto;">
                    @include('user.potongan._slip_content', [
                        'id' => 'slipPreview',
                        'isDashboard' => true
                    ])
                </div>
                <div class="p-3 bg-white border-top text-center">
                    <button onclick="printSlip()" class="btn btn-sm btn-primary px-4">
                        <i class="bi bi-printer me-1"></i> Cetak Preview Ini
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Current Data Summary (Minimal) -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">Riwayat & Detail</h5>
            </div>
            <div class="card-body p-4">
                @if($potonganList->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3">
                        <span class="text-muted">Total Potongan Bulan Ini:</span>
                        <span class="fs-4 fw-bold text-primary">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="text-center">
                    <a href="{{ route('user.potongan.index') }}" class="btn btn-outline-primary w-100 py-2" style="border-radius: 10px;">
                        Lihat Semua Riwayat Potongan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Logout for convenience -->
        <div class="text-center mt-4 mb-5">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm px-4" style="border-radius: 10px;">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar / Logout
                </button>
            </form>
            <p class="text-muted small mt-2">Pastikan Anda keluar setelah selesai menggunakan sistem.</p>
        </div>

        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('selectPeriode').addEventListener('change', function() {
    var url = this.value;
    if (url) {
        window.location.href = url;
    }
});

function printSlip() {
    const el = document.getElementById('slipPreview') || document.getElementById('slipCetak');
    if (!el) return;
    el.classList.add('slip-to-print');
    window.print();
    setTimeout(() => el.classList.remove('slip-to-print'), 1000);
}
</script>
@endpush

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    .btn-primary {
        background: #137fec;
        border: none;
    }
    .btn-primary:hover {
        background: #0d6efd;
        transform: translateY(-2px);
    }
    .card {
        transition: all 0.3s ease;
    }
</style>
@endpush
