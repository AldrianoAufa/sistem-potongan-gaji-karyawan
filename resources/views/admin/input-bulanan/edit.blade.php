@extends('layouts.admin')
@section('title', 'Edit Potongan Bulanan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2"></i>Edit Potongan Bulanan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.input-bulanan.index') }}">Data Potongan</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

<div class="card card-custom" style="max-width: 800px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.input-bulanan.update', $inputBulanan) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">karyawan <span class="text-danger">*</span></label>
                    <select name="karyawan_id" class="form-select" required>
                        @foreach($karyawanList as $a)
                        <option value="{{ $a->id }}" {{ $inputBulanan->karyawan_id == $a->id ? 'selected' : '' }}>
                            {{ $a->kode_karyawan }} — {{ $a->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jenis Potongan <span class="text-danger">*</span></label>
                    <select name="jenis_potongan_id" class="form-select" required>
                        @foreach($jenisPotonganList as $jp)
                        <option value="{{ $jp->id }}" {{ $inputBulanan->jenis_potongan_id == $jp->id ? 'selected' : '' }}>
                            {{ $jp->kode_potongan }} — {{ $jp->nama_potongan }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                    <select name="bulan" class="form-select" required>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                        <option value="{{ $i+1 }}" {{ $inputBulanan->bulan == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" class="form-control" value="{{ $inputBulanan->tahun }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Jumlah Potongan (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_potongan" class="form-control"
                           value="{{ $inputBulanan->jumlah_potongan }}" min="0" step="1" required>
                </div>
            </div>

            <hr class="my-3">
            <h6 class="text-muted mb-3">Detail Pinjaman (Opsional)</h6>
            @php $rinci = $inputBulanan->data_rinci ?? []; @endphp
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.85rem;">Pinjaman (PINJ)</label>
                    <input type="number" name="data_rinci[PINJ]" class="form-control form-control-sm" value="{{ $rinci['PINJ'] ?? '' }}" step="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.85rem;">Saldo Awal (AWAL)</label>
                    <input type="number" name="data_rinci[AWAL]" class="form-control form-control-sm" value="{{ $rinci['AWAL'] ?? '' }}" step="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.85rem;">Bulan Ke (BULN)</label>
                    <input type="number" name="data_rinci[BULN]" class="form-control form-control-sm" value="{{ $rinci['BULN'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.85rem;">Total Kali (KALI)</label>
                    <input type="number" name="data_rinci[KALI]" class="form-control form-control-sm" value="{{ $rinci['KALI'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.85rem;">Pokok (PKOK)</label>
                    <input type="number" name="data_rinci[PKOK]" class="form-control form-control-sm" value="{{ $rinci['PKOK'] ?? '' }}" step="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.85rem;">Sisa Saldo (SALD)</label>
                    <input type="number" name="data_rinci[SALD]" class="form-control form-control-sm" value="{{ $rinci['SALD'] ?? '' }}" step="1">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin.input-bulanan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
