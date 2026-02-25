@extends('layouts.admin')
@section('title', 'Kelola karyawan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-people-fill me-2"></i>Kelola karyawan</h4>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form class="d-flex gap-2 flex-wrap" method="GET" action="{{ route('admin.karyawan.index') }}">
            <select name="jabatan_id" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                <option value="">Semua Jabatan</option>
                @foreach($jabatan as $j)
                    <option value="{{ $j->id }}" {{ request('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                @endforeach
            </select>

            <select name="departemen_id" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                <option value="">Semua Dep</option>
                @foreach($departemen as $d)
                    <option value="{{ $d->id }}" {{ request('departemen_id') == $d->id ? 'selected' : '' }}>{{ $d->kode_departemen }}</option>
                @endforeach
            </select>

            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control form-control-sm" name="search"
                       placeholder="Cari nama/kode..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                @if(request()->anyFilled(['search', 'jabatan_id', 'departemen_id']))
                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.import-karyawan.form') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
            </a>
            <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah karyawan
            </a>
        </div>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan as $i => $item)
                    <tr>
                        <td>{{ $karyawan->firstItem() + $i }}</td>
                        <td><span class="badge bg-light text-dark fw-semibold">{{ $item->kode_karyawan }}</span></td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                        <td><span class="badge bg-secondary-subtle text-secondary fw-semibold">{{ $item->departemen->kode_departemen ?? '-' }}</span></td>
                        <td>
                            <a href="{{ route('admin.karyawan.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            @if($item->user)
                            <form action="{{ route('admin.karyawan.reset-password', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Reset password {{ $item->nama }} ke NIK ({{ $item->kode_karyawan }})?')">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm" title="Reset Password ke NIK">
                                    <i class="bi bi-key"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.karyawan.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus karyawan {{ $item->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data karyawan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($karyawan->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center px-3 py-2">
        <small class="text-muted">
            Menampilkan {{ $karyawan->firstItem() }}–{{ $karyawan->lastItem() }} dari {{ $karyawan->total() }} karyawan
        </small>
        <div class="pagination-sm-custom">
            {{ $karyawan->links() }}
        </div>
    </div>
    @endif
</div>

<style>
.pagination-sm-custom .pagination {
    margin: 0;
    gap: 2px;
}
.pagination-sm-custom .page-link {
    padding: 2px 8px;
    font-size: 0.75rem;
    border-radius: 4px !important;
    color: var(--bs-primary);
    border: 1px solid #dee2e6;
    line-height: 1.5;
}
.pagination-sm-custom .page-item.active .page-link {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: #fff;
}
.pagination-sm-custom .page-item.disabled .page-link {
    color: #adb5bd;
}
</style>
@endsection
