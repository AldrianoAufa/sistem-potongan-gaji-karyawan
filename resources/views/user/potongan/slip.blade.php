@extends('layouts.user')
@section('title', 'Slip Potongan Gaji - ' . $namaBulan . ' ' . $tahun)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3 no-print">
    <div>
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Slip Bukti Potongan Gaji</h5>
        <small class="text-muted">{{ $namaBulan }} {{ $tahun }}</small>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" onclick="printSlip()">
            <i class="bi bi-printer me-1"></i>Cetak / Print
        </button>
        <a href="{{ route('user.potongan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

{{-- Slip Container --}}
<div class="slip-wrapper">
    @include('user.potongan._slip_content', [
        'id' => 'slipCetak',
        'isDashboard' => false
    ])
</div>{{-- end slip-wrapper --}}

@push('scripts')
<script>
function printSlip() {
    const el = document.getElementById('slipCetak');
    el.classList.add('slip-to-print');
    window.print();
    setTimeout(() => el.classList.remove('slip-to-print'), 1000);
}
</script>
@endpush
@endsection
