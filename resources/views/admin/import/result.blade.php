@extends('layouts.admin')
@section('title', 'Hasil Import Excel')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-check2-circle me-2"></i>Hasil Import Excel</h4>
</div>

@php
    $bulanNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                  7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #28A745;">
            <div class="card-body">
                <div style="font-size: 2rem; font-weight: 700; color: #28A745;">{{ $berhasil }}</div>
                <div class="text-muted">Berhasil Diimpor</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #DC3545;">
            <div class="card-body">
                <div style="font-size: 2rem; font-weight: 700; color: #DC3545;">{{ $gagal }}</div>
                <div class="text-muted">Gagal / Error</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom text-center py-3" style="border-left: 4px solid #17A2B8;">
            <div class="card-body">
                <div style="font-size: 2rem; font-weight: 700; color: #17A2B8;">{{ $berhasil + $gagal }}</div>
                <div class="text-muted">Total Baris</div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-{{ $gagal == 0 ? 'success' : 'warning' }}">
    <i class="bi bi-{{ $gagal == 0 ? 'check-circle-fill' : 'exclamation-triangle-fill' }} me-2"></i>
    Import untuk periode <strong>{{ $bulanNames[$bulan] }} {{ $tahun }}</strong>:
    {{ $berhasil }} baris berhasil{{ $gagal > 0 ? ", $gagal baris gagal" : '' }}.
</div>

@if(count($errors) > 0)
<div class="card card-custom">
    <div class="card-header">
        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Baris dengan Error
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Baris</th>
                        <th>Kode</th>
                        <th>Keterangan Error</th>
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

<div class="mt-3 d-flex gap-2">
    <a href="{{ route('admin.import.form') }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i>Import Lagi
    </a>
    <a href="{{ route('admin.input-bulanan.index') }}?bulan={{ $bulan }}&tahun={{ $tahun }}" class="btn btn-outline-primary">
        <i class="bi bi-table me-1"></i>Lihat Data
    </a>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-house me-1"></i>Dashboard
    </a>
</div>
@endsection
