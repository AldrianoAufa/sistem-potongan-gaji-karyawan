@extends('layouts.admin')
@section('title', 'Kelola karyawan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-people-fill me-2"></i>Kelola karyawan</h4>
    </div>
    <div class="d-flex gap-2">
        <form class="d-flex" method="GET" action="{{ route('admin.karyawan.index') }}">
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control form-control-sm" name="search"
                       placeholder="Cari nama/kode..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
        <a href="{{ route('admin.import-karyawan.form') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
        </a>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah karyawan
        </a>
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
                        <th>Akun</th>
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
                            @if($item->user)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $item->user->username }}</span>
                            @else
                                <span class="badge bg-secondary">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.karyawan.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
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
    <div class="card-footer bg-white">
        {{ $karyawan->links() }}
    </div>
    @endif
</div>
@endsection
