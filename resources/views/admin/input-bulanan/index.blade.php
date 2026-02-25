@extends('layouts.admin')
@section('title', 'Data Potongan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h4><i class="bi bi-cash-coin me-2"></i>Data Potongan</h4>
    
</div>

<!-- Filter -->
<div class="card card-custom mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.input-bulanan.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Bulan</label>
                <select name="bulan" class="form-select form-select-sm" style="width: 130px;">
                    <option value="">Semua</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                    <option value="{{ $i+1 }}" {{ request('bulan') == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Tahun</label>
                <select name="tahun" class="form-select form-select-sm" style="width: 100px;">
                    <option value="">Semua</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size: 0.8rem;">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama/kode..."
                       value="{{ request('search') }}" style="width: 180px;">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('admin.input-bulanan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama karyawan</th>
                        <th>Jenis Potongan</th>
                        <th>Bulan/Tahun</th>
                        <th class="text-end">Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inputBulanan as $i => $item)
                    <tr>
                        <td>{{ $inputBulanan->firstItem() + $i }}</td>
                        <td><span class="badge bg-light text-dark">{{ $item->karyawan->kode_karyawan }}</span></td>
                        <td>{{ $item->karyawan->nama }}</td>
                        <td><span class="badge bg-primary">{{ $item->jenisPotongan->kode_potongan }}</span> {{ $item->jenisPotongan->nama_potongan }}</td>
                        <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.input-bulanan.edit', $item) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                            <form action="{{ route('admin.input-bulanan.destroy', $item) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
                @if($inputBulanan->count() > 0)
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">Total Potongan:</td>
                        <td class="text-end text-primary">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($inputBulanan->hasPages())
    <div class="card-footer bg-white">{{ $inputBulanan->links() }}</div>
    @endif
</div>

<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.input-bulanan.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Potongan Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">karyawan <span class="text-danger">*</span></label>
                            <select name="karyawan_id" class="form-select" required>
                                <option value="">-- Pilih karyawan --</option>
                                @foreach($karyawanList as $a)
                                <option value="{{ $a->id }}">{{ $a->kode_karyawan }} — {{ $a->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Potongan <span class="text-danger">*</span></label>
                            <select name="jenis_potongan_id" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisPotonganList as $jp)
                                <option value="{{ $jp->id }}">{{ $jp->kode_potongan }} — {{ $jp->nama_potongan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                                <option value="{{ $i+1 }}" {{ now()->month == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select" required>
                                @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Potongan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_potongan" class="form-control" min="0" step="1" required>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Detail Pinjaman (Opsional)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Pinjaman (PINJ)</label>
                            <input type="number" name="data_rinci[PINJ]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Saldo Awal (AWAL)</label>
                            <input type="number" name="data_rinci[AWAL]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Bulan Ke (BULN)</label>
                            <input type="number" name="data_rinci[BULN]" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Total Kali (KALI)</label>
                            <input type="number" name="data_rinci[KALI]" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Pokok (PKOK)</label>
                            <input type="number" name="data_rinci[PKOK]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Bunga (RPBG)</label>
                            <input type="number" name="data_rinci[RPBG]" class="form-control form-control-sm" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size: 0.85rem;">Sisa Saldo (SALD)</label>
                            <input type="number" name="data_rinci[SALD]" class="form-control form-control-sm" step="1">
                        </div>
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
