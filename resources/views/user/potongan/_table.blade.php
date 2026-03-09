<div class="table-responsive">
    <table class="table table-custom table-hover mb-0">
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan/Tahun</th>
                <th>Jenis Potongan</th>
                <th class="text-end">Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($potongan as $i => $item)
            <tr>
                <td>{{ $potongan->firstItem() + $i }}</td>
                <td>{{ $item->nama_bulan }} {{ $item->tahun }}</td>
                <td>
                    <span class="badge bg-primary me-1">{{ $item->jenisPotongan->kode_potongan }}</span>
                    {{ $item->jenisPotongan->nama_potongan }}
                </td>
                <td class="text-end fw-semibold">Rp {{ number_format($item->jumlah_potongan, 0, ',', '.') }}</td>
                <td>
                    <div class="d-flex gap-1">
                        @if($item->data_rinci)
                        <a href="{{ route('user.potongan.show', $item) }}" class="btn btn-outline-info btn-sm" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                        <a href="{{ route('user.potongan.slip', [$item->bulan, $item->tahun]) }}"
                           class="btn btn-outline-primary btn-sm" title="Slip {{ $item->nama_bulan }} {{ $item->tahun }}">
                            <i class="bi bi-receipt"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data potongan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($potongan->hasPages())
<div class="card-footer bg-white pagination-ajax">
    {{ $potongan->links() }}
</div>
@endif
