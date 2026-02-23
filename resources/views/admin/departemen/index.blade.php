@extends('layouts.admin')
@section('title', 'Kelola Departemen')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4><i class="bi bi-diagram-3-fill me-2"></i>Kelola Departemen</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Departemen
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Departemen</th>
                        <th>Nama Departemen</th>
                        <th>Jumlah karyawan</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departemen as $i => $item)
                    <tr>
                        <td>{{ $departemen->firstItem() + $i }}</td>
                        <td class="fw-bold text-primary">{{ $item->kode_departemen }}</td>
                        <td class="fw-semibold">{{ $item->nama_departemen }}</td>
                        <td><span class="badge bg-info">{{ $item->karyawan_count }} karyawan</span></td>
                        <td>
                            <a href="{{ route('admin.departemen.show', $item) }}" class="btn btn-info btn-sm text-white" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.departemen.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus departemen {{ $item->nama_departemen }}?')">
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
                                <form method="POST" action="{{ route('admin.departemen.update', $item) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Departemen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Kode Departemen <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="kode_departemen"
                                                   value="{{ $item->kode_departemen }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Departemen <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_departemen"
                                                   value="{{ $item->nama_departemen }}" required>
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
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data departemen</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($departemen->hasPages())
    <div class="card-footer bg-white">{{ $departemen->links() }}</div>
    @endif
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.departemen.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Departemen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_departemen"
                               placeholder="Contoh: IT, HRD, FIN" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Departemen <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_departemen"
                               placeholder="Masukkan nama departemen" required>
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
