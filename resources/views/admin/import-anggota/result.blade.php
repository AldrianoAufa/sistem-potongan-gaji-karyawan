@extends('layouts.admin')
@section('title', 'Hasil Import Anggota')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-check2-circle me-2"></i>Hasil Import Data Anggota</h4>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #28A745;">
            <div class="card-body py-2">
                <div style="font-size: 1.75rem; font-weight: 700; color: #28A745;">{{ $berhasil }}</div>
                <div class="text-muted" style="font-size: 0.85rem;">Anggota Baru</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #FFC107;">
            <div class="card-body py-2">
                <div style="font-size: 1.75rem; font-weight: 700; color: #FFC107;">{{ $diperbarui }}</div>
                <div class="text-muted" style="font-size: 0.85rem;">Diperbarui</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #DC3545;">
            <div class="card-body py-2">
                <div style="font-size: 1.75rem; font-weight: 700; color: #DC3545;">{{ $gagal }}</div>
                <div class="text-muted" style="font-size: 0.85rem;">Gagal / Error</div>
            </div>
        </div>
    </div>
    @if($jabatanBaru > 0)
    <div class="col-md-4 col-6">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #6F42C1;">
            <div class="card-body py-2">
                <div style="font-size: 1.75rem; font-weight: 700; color: #6F42C1;">{{ $jabatanBaru }}</div>
                <div class="text-muted" style="font-size: 0.85rem;">Jabatan Baru Dibuat</div>
            </div>
        </div>
    </div>
    @endif
    @if($akunDibuat > 0)
    <div class="col-md-4 col-6">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #17A2B8;">
            <div class="card-body py-2">
                <div style="font-size: 1.75rem; font-weight: 700; color: #17A2B8;">{{ $akunDibuat }}</div>
                <div class="text-muted" style="font-size: 0.85rem;">Akun Login Dibuat</div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="alert alert-{{ $gagal == 0 ? 'success' : 'warning' }}">
    <i class="bi bi-{{ $gagal == 0 ? 'check-circle-fill' : 'exclamation-triangle-fill' }} me-2"></i>
    Import data anggota selesai:
    <strong>{{ $berhasil }}</strong> baru,
    <strong>{{ $diperbarui }}</strong> diperbarui{{ $gagal > 0 ? ", <strong>{$gagal}</strong> gagal" : '' }}.
    @if($jabatanBaru > 0)
        <br><i class="bi bi-building me-1"></i><strong>{{ $jabatanBaru }}</strong> jabatan baru otomatis dibuat.
    @endif
    @if($akunDibuat > 0)
        <br><i class="bi bi-person-badge me-1"></i><strong>{{ $akunDibuat }}</strong> akun login berhasil dibuat.
    @endif
</div>

@if(count($errors) > 0)
<div class="card card-custom mb-3">
    <div class="card-header">
        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Baris dengan Error / Catatan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Baris</th>
                        <th>Kode</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($errors as $err)
                    <tr>
                        <td><span class="badge bg-light text-dark">{{ $err['baris'] }}</span></td>
                        <td>{{ $err['kode'] }}</td>
                        <td class="text-danger"><i class="bi bi-x-circle me-1"></i>{{ $err['error'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('admin.import-anggota.form') }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i>Import Lagi
    </a>
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-people me-1"></i>Lihat Daftar Anggota
    </a>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-house me-1"></i>Dashboard
    </a>
</div>
@endsection
