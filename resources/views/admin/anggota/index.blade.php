@extends('layouts.admin')
@section('title', 'Kelola Anggota')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-people-fill me-2"></i>Kelola Anggota</h4>
    </div>
    <div class="d-flex gap-2">
        <form class="d-flex" method="GET" action="{{ route('admin.anggota.index') }}">
            <div class="input-group" style="width: 250px;">
                <input type="text" class="form-control form-control-sm" name="search"
                       placeholder="Cari nama/kode..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
        <a href="{{ route('admin.anggota.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Anggota
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
                        <th>Kode Anggota</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Akun Login</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggota as $i => $item)
                    <tr>
                        <td>{{ $anggota->firstItem() + $i }}</td>
                        <td><span class="badge bg-light text-dark fw-semibold">{{ $item->kode_anggota }}</span></td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->jabatan->nama_jabatan ?? '-' }}</td>
                        <td>
                            @if($item->user)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $item->user->username }}</span>
                            @else
                                <span class="badge bg-secondary">Belum ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.anggota.edit', $item) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.anggota.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus anggota {{ $item->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data anggota</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($anggota->hasPages())
    <div class="card-footer bg-white">
        {{ $anggota->links() }}
    </div>
    @endif
</div>
@endsection
