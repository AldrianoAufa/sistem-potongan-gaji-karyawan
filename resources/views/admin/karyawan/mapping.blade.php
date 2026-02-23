@extends('layouts.admin')
@section('title', 'Mapping Potongan Karyawan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-clipboard-check me-2"></i>Mapping Potongan Karyawan</h4>
        <p class="text-muted small mb-0">Tentukan jenis potongan yang berlaku untuk setiap karyawan.</p>
    </div>
    <form class="d-flex" method="GET" action="{{ route('admin.karyawan.mapping') }}">
        <div class="input-group" style="width: 250px;">
            <input type="text" class="form-control form-control-sm" name="search" 
                   placeholder="Cari nama/NIK..." value="{{ request('search') }}">
            <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jabatan / Departemen</th>
                        <th>Jenis Potongan Aktif</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawanList as $i => $item)
                    <tr>
                        <td>{{ $karyawanList->firstItem() + $i }}</td>
                        <td><span class="badge bg-light text-dark fw-semibold">{{ $item->kode_karyawan }}</span></td>
                        <td class="fw-semibold">{{ $item->nama }}</td>
                        <td>
                            <div class="small fw-medium">{{ $item->jabatan->nama_jabatan ?? '-' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $item->departemen->kode_departemen ?? '-' }}</div>
                        </td>
                        <td>
                            @php $count = 0; @endphp
                            @forelse($item->potongan as $p)
                                <span class="badge bg-info-subtle text-info border border-info-subtle mb-1" style="font-size: 0.7rem;">
                                    {{ $p->nama_potongan }}
                                </span>
                                @php $count++; @endphp
                            @empty
                                <span class="text-muted small italic">Belum ada potongan</span>
                            @endforelse
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#modalMapping{{ $item->id }}">
                                <i class="bi bi-gear-fill me-1"></i> Atur Potongan
                            </button>

                            <!-- Modal Mapping -->
                            <div class="modal fade" id="modalMapping{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-primary text-white border-0">
                                            <h5 class="modal-title"><i class="bi bi-person-fill me-2"></i>Atur Potongan: {{ $item->nama }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.karyawan.mapping.update', $item) }}" method="POST">
                                            @csrf
                                            <div class="modal-body py-4">
                                                <p class="fw-semibold mb-3 border-bottom pb-2">Pilih jenis potongan yang berlaku:</p>
                                                <div class="row g-2">
                                                    @php $activeIds = $item->potongan->pluck('id')->toArray(); @endphp
                                                    @foreach($jenisPotongan as $jp)
                                                    <div class="col-12">
                                                        <div class="form-check p-2 border rounded hover-bg-light transition-all cursor-pointer">
                                                            <input class="form-check-input ms-1 mt-2" type="checkbox" name="jenis_potongan_ids[]" 
                                                                   value="{{ $jp->id }}" id="chk{{ $item->id }}_{{ $jp->id }}"
                                                                   {{ in_array($jp->id, $activeIds) ? 'checked' : '' }}>
                                                            <label class="form-check-label d-block ms-4" for="chk{{ $item->id }}_{{ $jp->id }}">
                                                                <span class="fw-semibold d-block text-dark">{{ $jp->nama_potongan }}</span>
                                                                <span class="text-muted small">Kode: {{ $jp->kode_potongan }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 bg-light rounded-bottom">
                                                <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-person-exclamation fs-3 d-block mb-2 text-warning"></i>
                            Karyawan tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($karyawanList->hasPages())
    <div class="card-footer bg-white border-0">
        {{ $karyawanList->links() }}
    </div>
    @endif
</div>

<style>
.hover-bg-light:hover { 
    background-color: #f8f9fa; 
    border-color: #dee2e6 !important;
}
.transition-all { transition: all 0.2s ease; }
.cursor-pointer { cursor: pointer; }
.form-check-input:checked + .form-check-label .fw-semibold {
    color: var(--primary) !important;
}
</style>
@endsection
