@extends('layouts.admin')
@section('title', 'Detail Departemen')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-diagram-3-fill me-2"></i>Detail Departemen: {{ $departemen->nama_departemen }} (ID: {{ $departemen->id }})</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.departemen.index') }}">Departemen</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card card-custom mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informasi Departemen</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small d-block">Kode Departemen</label>
                    <span class="fw-bold text-primary">{{ $departemen->kode_departemen }}</span>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Nama Departemen</label>
                    <span class="fw-semibold">{{ $departemen->nama_departemen }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar karyawan</h6>
                <span class="badge bg-info">{{ $karyawan->count() }} orang</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0 text-sm">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawan as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><span class="badge bg-light text-dark">{{ $item->kode_karyawan }}</span></td>
                                <td class="fw-semibold">{{ $item->nama }}</td>
                                <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada karyawan di departemen ini</td>
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
