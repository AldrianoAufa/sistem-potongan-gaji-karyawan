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
                        <th>Karyawan</th>
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
                        <td>
                            @if($item->karyawan->count() > 0)
                                <button type="button" class="btn btn-sm btn-outline-info py-0 px-2"
                                        data-bs-toggle="modal" data-bs-target="#karyawanModal{{ $item->id }}"
                                        style="font-size: 0.8rem;">
                                    <i class="bi bi-people-fill me-1"></i>{{ $item->karyawan->count() }} karyawan
                                </button>
                            @else
                                <span class="text-muted small"><i class="bi bi-dash"></i> 0 karyawan</span>
                            @endif
                        </td>
                        <td><span class="badge bg-info">{{ $item->input_bulanan_count }} record</span></td>
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#showModal{{ $item->id }}" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>
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

                    <!-- Karyawan Modal -->
                    @if($item->karyawan->count() > 0)
                    <div class="modal fade" id="karyawanModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-info text-white border-0">
                                    <h6 class="modal-title mb-0">
                                        <i class="bi bi-people-fill me-2"></i>Karyawan — {{ $item->nama_potongan }}
                                    </h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:40px;" class="ps-3">No</th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($item->karyawan as $idx => $k)
                                            <tr>
                                                <td class="ps-3">{{ $idx + 1 }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $k->kode_karyawan }}</span></td>
                                                <td>{{ $k->nama }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer bg-light border-0 py-2">
                                    <small class="text-muted me-auto">Total: {{ $item->karyawan->count() }} karyawan</small>
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Show Detail Modal -->
                    <div class="modal fade" id="showModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-eye me-2"></i>Detail Jenis Potongan — {{ $item->nama_potongan }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Kode Potongan:</label>
                                            <p><span class="badge bg-primary fs-6">{{ $item->kode_potongan }}</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nama Potongan:</label>
                                            <p class="fw-semibold">{{ $item->nama_potongan }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Jumlah Data Input:</label>
                                            <p><span class="badge bg-info">{{ $item->input_bulanan_count }} record</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Jumlah Karyawan:</label>
                                            <p><span class="badge bg-success">{{ $item->karyawan->count() }} karyawan</span></p>
                                        </div>
                                    </div>

                                    <hr>
                                    
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-people-fill me-2"></i>Daftar Karyawan Terkait
                                    </h6>
                                    
                                    @if($item->karyawan->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width:40px;">No</th>
                                                        <th>NIK</th>
                                                        <th>Nama Karyawan</th>
                                                        <th>Jabatan</th>
                                                        <th>Departemen</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($item->karyawan as $idx => $k)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td><span class="badge bg-light text-dark">{{ $k->kode_karyawan }}</span></td>
                                                        <td>{{ $k->nama }}</td>
                                                        <td>{{ $k->jabatan->nama_jabatan ?? '-' }}</td>
                                                        <td>{{ $k->departemen->nama_departemen ?? '-' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-light text-center">
                                            <i class="bi bi-info-circle me-2"></i>Belum ada karyawan yang terhubung dengan jenis potongan ini.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data jenis potongan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jenisPotongan->hasPages())
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center px-3 py-2">
        <small class="text-muted">
            Menampilkan {{ $jenisPotongan->firstItem() }}–{{ $jenisPotongan->lastItem() }} dari {{ $jenisPotongan->total() }}
        </small>
        {{ $jenisPotongan->links() }}
    </div>
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
