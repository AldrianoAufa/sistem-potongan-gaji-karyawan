@extends('layouts.admin')
@section('title', 'Kelola Jenis Potongan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4><i class="bi bi-clipboard2-data-fill me-2"></i>Kelola Jenis Potongan</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="bi bi-plus-lg me-1"></i>Tambah Jenis Potongan
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode</th>
                        <th>Nama Potongan</th>
                        <th>Jumlah Data</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisPotongan as $i => $item)
                    <tr>
                        <td>{{ $jenisPotongan->firstItem() + $i }}</td>
                        <td><span class="badge bg-primary">{{ $item->kode_potongan }}</span></td>
                        <td class="fw-semibold">{{ $item->nama_potongan }}</td>
                        <td><span class="badge bg-info">{{ $item->input_bulanan_count }} record</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.jenis-potongan.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus {{ $item->nama_potongan }}?')">
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
                                <form method="POST" action="{{ route('admin.jenis-potongan.update', $item) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Jenis Potongan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Kode Potongan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="kode_potongan"
                                                   value="{{ $item->kode_potongan }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Potongan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_potongan"
                                                   value="{{ $item->nama_potongan }}" required>
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
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data jenis potongan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jenisPotongan->hasPages())
    <div class="card-footer bg-white">{{ $jenisPotongan->links() }}</div>
    @endif
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.jenis-potongan.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Potongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Potongan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_potongan"
                               placeholder="Contoh: KOPER, PINJ.P" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Potongan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_potongan"
                               placeholder="Contoh: Koperasi" required>
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
