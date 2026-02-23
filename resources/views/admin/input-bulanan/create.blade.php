@extends('layouts.admin')
@section('title', 'Input Potongan Kolektif')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cash-stack me-2"></i>Input Potongan Kolektif</h4>
    <p class="text-muted">Masukkan potongan gaji untuk banyak karyawan sekaligus berdasarkan jenis potongan.</p>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Step 1: Select Deduction Type -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-1-circle me-2"></i>Pilih Jenis Potongan & Periode</h6>
            </div>
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('admin.input-bulanan.create') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Potongan</label>
                            <select class="form-select" name="jenis_potongan_id" onchange="this.form.submit()" required>
                                <option value="">-- Pilih Jenis Potongan --</option>
                                @foreach($jenisPotonganAll as $jp)
                                    <option value="{{ $jp->id }}" {{ request('jenis_potongan_id') == $jp->id ? 'selected' : '' }}>
                                        {{ $jp->nama_potongan }} ({{ $jp->kode_potongan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select class="form-select" name="bulan">
                                @foreach($bulanOptions as $num => $name)
                                    <option value="{{ $num }}" {{ (request('bulan', date('n')) == $num) ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tahun</label>
                            <input type="number" class="form-control" name="tahun" value="{{ request('tahun', date('Y')) }}" min="2020" max="2099">
                        </div>
                        <div class="col-md-3">
                            @if(request('jenis_potongan_id'))
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-arrow-repeat me-1"></i> Refresh Daftar Karyawan
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedPotongan)
            <!-- Step 2: Input Values -->
            <div class="card card-custom">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-2-circle me-2"></i>Input Nominal Potongan: <strong>{{ $selectedPotongan->nama_potongan }}</strong></h6>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light">Set Semua Rp</span>
                            <input type="number" id="setAllNominal" class="form-control" placeholder="Nominal...">
                            <button class="btn btn-outline-secondary" type="button" onclick="applySetAll()">Terapkan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('admin.input-bulanan.bulk-store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis_potongan_id" value="{{ $selectedPotongan->id }}">
                        <input type="hidden" name="bulan" value="{{ request('bulan', date('n')) }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">

                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th style="width: 150px;">NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th style="width: 250px;">Nominal Potongan (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($karyawanList as $i => $k)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td><span class="badge bg-light text-dark fw-semibold">{{ $k->kode_karyawan }}</span></td>
                                            <td class="fw-medium">{{ $k->nama }}</td>
                                            <td>
                                                <input type="hidden" name="potongan[{{ $i }}][karyawan_id]" value="{{ $k->id }}">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" name="potongan[{{ $i }}][jumlah]" 
                                                           class="form-control nominal-input" 
                                                           placeholder="Masukkan nominal..."
                                                           min="0">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-person-exclamation fs-3 d-block mb-2"></i>
                                                Belum ada karyawan yang terdaftar untuk jenis potongan ini.<br>
                                                Silakan atur di menu <strong>Mapping Potongan</strong>.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(count($karyawanList) > 0)
                            <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle me-1"></i> Data yang kosong atau bernilai 0 tidak akan disimpan.
                                </div>
                                <div>
                                    <a href="{{ route('admin.input-bulanan.index') }}" class="btn btn-secondary me-2">Batal</a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Simpan Semua Potongan
                                    </button>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-info border-0 shadow-sm">
                <i class="bi bi-info-circle-fill me-2"></i> Silakan pilih <strong>Jenis Potongan</strong> di atas untuk memulai input data.
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function applySetAll() {
        const value = document.getElementById('setAllNominal').value;
        if (value === '') return;
        
        const inputs = document.querySelectorAll('.nominal-input');
        inputs.forEach(input => {
            input.value = value;
        });
    }
</script>
@endpush
@endsection
