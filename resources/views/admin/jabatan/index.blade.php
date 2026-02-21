@extends('layouts.admin')
@section('title', 'Kelola Jabatan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4><i class="bi bi-building-fill me-2"></i>Kelola Jabatan</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Jabatan
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Jabatan</th>
                        <th>Jumlah Anggota</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatan as $i => $item)
                    <tr>
                        <td>{{ $jabatan->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $item->nama_jabatan }}</td>
                        <td><span class="badge bg-info">{{ $item->anggota_count }} anggota</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.jabatan.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus jabatan {{ $item->nama_jabatan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.jabatan.update', $item) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Jabatan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Jabatan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_jabatan"
                                                   value="{{ $item->nama_jabatan }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada data jabatan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jabatan->hasPages())
    <div class="card-footer bg-white">{{ $jabatan->links() }}</div>
    @endif
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.jabatan.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_jabatan"
                               placeholder="Masukkan nama jabatan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
