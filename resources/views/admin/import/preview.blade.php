@extends('layouts.admin')
@section('title', 'Preview Data Import')

@push('styles')
<style>
/* ===== Preview Table Enhancements ===== */
#previewTable th, #previewTable td {
    vertical-align: middle;
    font-size: 0.845rem;
}
.row-deleted {
    opacity: 0.38;
    background: #fef2f2 !important;
    text-decoration: line-through;
}
.pkok-input {
    width: 130px;
    font-size: 0.82rem;
    padding: 4px 8px;
    border: 1.5px solid #dee2e6;
    border-radius: 6px;
    transition: border-color 0.2s, box-shadow 0.2s;
    text-align: right;
}
.pkok-input:focus {
    border-color: #4A90D9;
    box-shadow: 0 0 0 3px rgba(74,144,217,0.15);
    outline: none;
}
.pkok-input.is-dirty {
    border-color: #fd7e14;
    background: #fff8f0;
}
.angs-display {
    font-weight: 600;
    color: #1a7f5a;
    font-size: 0.86rem;
}
.rpbg-display {
    font-size: 0.82rem;
    color: #6c757d;
}
.badge-warning-row {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
    font-size: 0.7rem;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 600;
}
.badge-ok-row {
    background: #d1fae5;
    border: 1px solid #6ee7b7;
    color: #065f46;
    font-size: 0.7rem;
    padding: 2px 7px;
    border-radius: 20px;
    font-weight: 600;
}
.btn-edit-row {
    padding: 3px 9px;
    font-size: 0.78rem;
    border-radius: 5px;
}
.btn-delete-row {
    padding: 3px 9px;
    font-size: 0.78rem;
    border-radius: 5px;
}
.saving-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 16px;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    display: none;
}
.saving-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
}
.stat-card {
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
.stat-card .stat-icon {
    font-size: 2rem;
    line-height: 1;
    width: 50px;
    text-align: center;
}
.stat-card .stat-num {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1;
}
.stat-card .stat-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 3px;
}
.sticky-header th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8f9fa;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
}
.table-scroll-wrapper {
    max-height: 520px;
    overflow-y: auto;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}
</style>
@endpush

@section('content')

{{-- Saving overlay --}}
<div class="saving-overlay" id="savingOverlay">
    <div class="spinner-border text-light"></div>
    <span>Menyimpan data, mohon tunggu...</span>
</div>

<div class="page-header">
    <h4><i class="bi bi-eye text-primary me-2"></i>Preview Data Import</h4>
    <p class="text-muted mb-0">
        Periksa, edit, atau hapus baris data sebelum menyimpan.
        Nilai <strong>Angsuran</strong> dihitung otomatis dari <strong>Nilai Pokok + RPBG</strong>.
    </p>
</div>

{{-- ===== Statistik ===== --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card" style="background: #eff6ff; border-left: 4px solid #3b82f6;">
            <div class="stat-icon text-primary"><i class="bi bi-table"></i></div>
            <div>
                <div class="stat-num text-primary" id="statTotal">{{ $totalValid }}</div>
                <div class="stat-label">Total Baris Valid</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="background: #fffbeb; border-left: 4px solid #f59e0b;">
            <div class="stat-icon text-warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div>
                <div class="stat-num text-warning" id="statWarning">{{ $totalWarning }}</div>
                <div class="stat-label">Baris dengan Peringatan Hitungan</div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card" style="background: #fef2f2; border-left: 4px solid #ef4444;">
            <div class="stat-icon text-danger"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-num text-danger">{{ $gagal }}</div>
                <div class="stat-label">Baris Error (Diabaikan)</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Error Fatal ===== --}}
@if(count($errors) > 0)
<div class="alert alert-danger mb-4">
    <h5><i class="bi bi-x-circle-fill me-2"></i>Terdapat {{ $gagal }} Baris Error Fatal</h5>
    <p class="mb-2">Baris di bawah ini <strong>tidak akan diproses</strong> karena datanya tidak valid.</p>
    <ul class="mb-0 small">
        @foreach(array_slice($errors, 0, 5) as $err)
            <li>Baris {{ $err['baris'] }} (NIK: {{ $err['kode'] }}): {{ $err['error'] }}</li>
        @endforeach
        @if(count($errors) > 5)
            <li><em>Dan {{ count($errors) - 5 }} error lainnya...</em></li>
        @endif
    </ul>
</div>
@endif

{{-- ===== Info Warning ===== --}}
@if($totalWarning > 0)
<div class="alert alert-warning d-flex gap-2 mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
    <div>
        <strong>{{ $totalWarning }} baris</strong> memiliki nilai Angsuran di Excel yang berbeda dari hitungan sistem (Pokok + RPBG).
        Nilai angsuran pada tabel di bawah sudah <strong>dikoreksi otomatis</strong> menjadi Pokok + RPBG.
        Anda dapat mengedit nilai Pokok setiap baris jika diperlukan.
    </div>
</div>
@endif

{{-- ===== Tabel Preview ===== --}}
@if($totalValid > 0)
{{-- ===== Tabel Peringatan (Koreksi) ===== --}}
@php
    $warningRows = array_filter($validData, fn($r) => $r['has_warning']);
    $normalRows = array_filter($validData, fn($r) => !$r['has_warning']);
@endphp

@if(count($warningRows) > 0)
<div class="card card-custom border-warning mb-4 shadow-sm">
    <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center justify-content-between py-3">
        <div>
            <span class="fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Baris dengan Peringatan Hitungan (Butuh Koreksi)</span>
            <span class="text-muted ms-2 small">(<span id="warningCounter">{{ count($warningRows) }}</span> baris)</span>
        </div>
    </div>

    <div class="table-scroll-wrapper">
        <table class="table table-hover table-bordered mb-0">
            <thead class="sticky-header">
                <tr class="table-warning">
                    <th width="40" class="text-center">#</th>
                    <th width="50" class="text-center">Baris</th>
                    <th width="80">KDPR</th>
                    <th width="120">NMPR</th>
                    <th width="110">NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jenis Potongan</th>
                    <th width="140" class="text-end">Nilai Pokok (PKOK)</th>
                    <th width="120" class="text-end">RPBG</th>
                    <th width="140" class="text-end">Angsuran (ANGS)</th>
                    <th width="85" class="text-center">Status</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="import-preview-body" id="warningPreviewBody">
                @foreach($validData as $i => $row)
                @if($row['has_warning'])
                <tr id="row-{{ $i }}"
                    data-index="{{ $i }}"
                    data-pkok="{{ $row['pkok'] }}"
                    data-rpbg="{{ $row['rpbg'] }}"
                    data-warning="{{ $row['has_warning'] ? '1' : '0' }}">
                    <td class="text-center text-muted small row-num">{{ $i + 1 }}</td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $row['baris'] }}</span></td>
                    <td class="small text-muted">{{ $row['data_rinci']['KDPR'] ?? '-' }}</td>
                    <td class="small text-muted">{{ $row['data_rinci']['NMPR'] ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark border fw-semibold">{{ $row['kode_karyawan'] }}</span></td>
                    <td class="fw-medium">{{ $row['nama_karyawan'] }}</td>
                    <td>
                        <span class="badge bg-light text-dark border me-1">{{ $row['kode_potongan'] }}</span>
                        <small class="text-muted">{{ $row['nama_potongan'] }}</small>
                    </td>

                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <span class="text-muted small">Rp</span>
                            <input type="number"
                                class="pkok-input"
                                id="pkok-{{ $i }}"
                                value="{{ $row['pkok'] }}"
                                min="0"
                                step="1"
                                data-original="{{ $row['pkok'] }}"
                                onchange="onPkokChange({{ $i }})"
                                oninput="onPkokInput({{ $i }})"
                                title="Edit nilai pokok">
                        </div>
                    </td>

                    <td class="text-end rpbg-display" id="rpbg-{{ $i }}">
                        Rp {{ number_format($row['rpbg'], 0, ',', '.') }}
                    </td>

                    <td class="text-end angs-display" id="angs-{{ $i }}">
                        Rp {{ number_format($row['jumlah_potongan'], 0, ',', '.') }}
                    </td>

                    <td class="text-center" id="status-{{ $i }}">
                        <span class="badge-warning-row"><i class="bi bi-exclamation-triangle me-1"></i>Koreksi</span>
                    </td>

                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button type="button"
                                class="btn btn-sm btn-outline-primary btn-edit-row"
                                id="savePkok-{{ $i }}"
                                onclick="savePkok({{ $i }})"
                                title="Simpan perubahan nilai pokok"
                                style="display:none;">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger btn-delete-row"
                                onclick="confirmDeleteRow({{ $i }})"
                                title="Hapus baris ini">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ===== Tabel Data Normal / Utama ===== --}}
<div class="card card-custom mb-4 shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <div>
            <span class="fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Daftar Data yang Akan Diimpor</span>
            <span class="text-muted ms-2 small">(<span id="normalCounter">{{ count($normalRows) }}</span> baris)</span>
        </div>
        {{-- Search --}}
        <div class="input-group input-group-sm" style="width: 240px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari NIK / Nama...">
        </div>
    </div>

    <div class="table-scroll-wrapper">
        <table class="table table-hover table-bordered mb-0" id="previewTable">
            <thead class="sticky-header">
                <tr>
                    <th width="40" class="text-center">#</th>
                    <th width="50" class="text-center">Baris</th>
                    <th width="80">KDPR</th>
                    <th width="120">NMPR</th>
                    <th width="110">NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jenis Potongan</th>
                    <th width="140" class="text-end">Nilai Pokok (PKOK)</th>
                    <th width="120" class="text-end">RPBG</th>
                    <th width="140" class="text-end">Angsuran (ANGS)</th>
                    <th width="85" class="text-center">Status</th>
                    <th width="100" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="import-preview-body" id="normalPreviewBody">
                @foreach($validData as $i => $row)
                @if(!$row['has_warning'])
                <tr id="row-{{ $i }}"
                    data-index="{{ $i }}"
                    data-pkok="{{ $row['pkok'] }}"
                    data-rpbg="{{ $row['rpbg'] }}"
                    data-warning="{{ $row['has_warning'] ? '1' : '0' }}">
                    <td class="text-center text-muted small row-num">{{ $i + 1 }}</td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $row['baris'] }}</span></td>
                    <td class="small text-muted">{{ $row['data_rinci']['KDPR'] ?? '-' }}</td>
                    <td class="small text-muted">{{ $row['data_rinci']['NMPR'] ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark border fw-semibold">{{ $row['kode_karyawan'] }}</span></td>
                    <td class="fw-medium">{{ $row['nama_karyawan'] }}</td>
                    <td>
                        <span class="badge bg-light text-dark border me-1">{{ $row['kode_potongan'] }}</span>
                        <small class="text-muted">{{ $row['nama_potongan'] }}</small>
                    </td>

                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <span class="text-muted small">Rp</span>
                            <input type="number"
                                class="pkok-input"
                                id="pkok-{{ $i }}"
                                value="{{ $row['pkok'] }}"
                                min="0"
                                step="1"
                                data-original="{{ $row['pkok'] }}"
                                onchange="onPkokChange({{ $i }})"
                                oninput="onPkokInput({{ $i }})"
                                title="Edit nilai pokok">
                        </div>
                    </td>

                    <td class="text-end rpbg-display" id="rpbg-{{ $i }}">
                        Rp {{ number_format($row['rpbg'], 0, ',', '.') }}
                    </td>

                    <td class="text-end angs-display" id="angs-{{ $i }}">
                        Rp {{ number_format($row['jumlah_potongan'], 0, ',', '.') }}
                    </td>

                    <td class="text-center" id="status-{{ $i }}">
                        <span class="badge-ok-row"><i class="bi bi-check2 me-1"></i>OK</span>
                    </td>

                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button type="button"
                                class="btn btn-sm btn-outline-primary btn-edit-row"
                                id="savePkok-{{ $i }}"
                                onclick="savePkok({{ $i }})"
                                title="Simpan perubahan nilai pokok"
                                style="display:none;">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger btn-delete-row"
                                onclick="confirmDeleteRow({{ $i }})"
                                title="Hapus baris ini">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ===== Tombol Simpan ===== --}}
<div class="card card-custom p-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h5 class="mb-1"><i class="bi bi-save2 me-2 text-success"></i>Simpan Semua Data</h5>
            <p class="text-muted mb-0 small">
                Pastikan semua nilai sudah benar. Klik tombol di bawah untuk menyimpan
                <strong id="saveCount">{{ $totalValid }}</strong> baris data ke sistem.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.import.execute') }}" method="POST" id="saveForm" onsubmit="return onSave()">
                @csrf
                <input type="hidden" name="cache_key" value="{{ $cacheKey }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-success px-4" id="btnSimpan">
                    <i class="bi bi-cloud-upload me-2"></i>
                    <span>Simpan <span id="btnCount">{{ $totalValid }}</span> Data</span>
                </button>
            </form>
            <form action="{{ route('admin.import.execute') }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan import data ini?');">
                @csrf
                <input type="hidden" name="cache_key" value="{{ $cacheKey }}">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="action" value="batal">
                <button type="submit" class="btn btn-outline-danger px-4">
                    <i class="bi bi-x-circle me-2"></i>Batal Semua
                </button>
            </form>
        </div>
    </div>
</div>

@else
{{-- Tidak ada data valid --}}
<div class="alert alert-warning">
    <h5><i class="bi bi-exclamation-triangle-fill me-2"></i>Tidak Ada Data Valid</h5>
    <p class="mb-2">Semua baris pada file Excel memiliki error dan tidak dapat diproses.</p>
    <a href="{{ route('admin.import.form') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali & Upload Ulang
    </a>
</div>
@endif

{{-- ===== Modal Konfirmasi Hapus ===== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger"><i class="bi bi-trash3 me-2"></i>Hapus Baris Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-1">Anda yakin ingin menghapus data:</p>
                <div class="alert alert-light text-center fw-semibold" id="deleteModalInfo">-</div>
                <p class="text-muted text-center small mb-0">Baris ini tidak akan dimasukkan ke sistem saat disimpan.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" onclick="executeDelete()">
                    <i class="bi bi-trash3 me-1"></i>Ya, Hapus Baris Ini
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CACHE_KEY  = @json($cacheKey);
const CSRF_TOKEN = @json(csrf_token());
const UPDATE_URL = @json(route('admin.import.update-row'));
const DELETE_URL = @json(route('admin.import.delete-row'));

let pendingDeleteIndex = null;
let totalRows = {{ $totalValid }};

// ============================================================
// PKOK CHANGE HANDLERS
// ============================================================
function onPkokInput(index) {
    const input    = document.getElementById('pkok-' + index);
    const original = parseFloat(input.dataset.original) || 0;
    const current  = parseFloat(input.value) || 0;

    // Mark dirty
    if (Math.abs(current - original) > 0.5) {
        input.classList.add('is-dirty');
        document.getElementById('savePkok-' + index).style.display = '';
    } else {
        input.classList.remove('is-dirty');
        document.getElementById('savePkok-' + index).style.display = 'none';
    }

    // Live update angsuran display
    const row  = document.getElementById('row-' + index);
    const rpbg = parseFloat(row.dataset.rpbg) || 0;
    const angs = current + rpbg;
    document.getElementById('angs-' + index).textContent = 'Rp ' + formatRupiah(angs);
}

function onPkokChange(index) {
    onPkokInput(index);
}

// ============================================================
// SAVE PKOK (AJAX)
// ============================================================
function savePkok(index) {
    const input = document.getElementById('pkok-' + index);
    const pkok  = parseFloat(input.value) || 0;
    const btn   = document.getElementById('savePkok-' + index);

    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    btn.disabled  = true;

    fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ cache_key: CACHE_KEY, index: index, pkok: pkok }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.dataset.original = data.pkok;
            input.classList.remove('is-dirty');

            // Update displayed angsuran
            document.getElementById('angs-' + index).textContent = 'Rp ' + formatRupiah(data.jumlah_potongan);

            // Update status badge
            const statusCell = document.getElementById('status-' + index);
            if (data.has_warning) {
                statusCell.innerHTML = '<span class="badge-warning-row"><i class="bi bi-exclamation-triangle me-1"></i>Koreksi</span>';
            } else {
                statusCell.innerHTML = '<span class="badge-ok-row"><i class="bi bi-check2 me-1"></i>OK</span>';
            }

            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            btn.style.display = 'none';
            btn.disabled = false;

            showToast('success', 'Nilai pokok berhasil diperbarui.');
        } else {
            showToast('danger', data.message || 'Gagal memperbarui.');
            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
            btn.disabled = false;
        }
    })
    .catch(() => {
        showToast('danger', 'Terjadi kesalahan jaringan.');
        btn.innerHTML = '<i class="bi bi-check-lg"></i>';
        btn.disabled = false;
    });
}

// ============================================================
// DELETE ROW
// ============================================================
function confirmDeleteRow(index) {
    const row    = document.getElementById('row-' + index);
    const cells  = row.querySelectorAll('td');
    const nik    = cells[2] ? cells[2].textContent.trim() : '-';
    const nama   = cells[3] ? cells[3].textContent.trim() : '-';
    pendingDeleteIndex = index;
    document.getElementById('deleteModalInfo').textContent = nik + ' — ' + nama;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function executeDelete() {
    if (pendingDeleteIndex === null) return;

    const btn = document.getElementById('confirmDeleteBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...';
    btn.disabled  = true;

    const index = pendingDeleteIndex;

    fetch(DELETE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ cache_key: CACHE_KEY, index: index }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Visually remove row
            const row = document.getElementById('row-' + index);
            row.remove();

            // Re-number visible rows
            totalRows = data.total_rows;
            reindexRows();
            updateCounters();

            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            showToast('success', 'Baris berhasil dihapus dari daftar import.');
        } else {
            showToast('danger', data.message || 'Gagal menghapus baris.');
        }
        btn.innerHTML = '<i class="bi bi-trash3 me-1"></i>Ya, Hapus Baris Ini';
        btn.disabled  = false;
        pendingDeleteIndex = null;
    })
    .catch(() => {
        showToast('danger', 'Terjadi kesalahan jaringan.');
        btn.innerHTML = '<i class="bi bi-trash3 me-1"></i>Ya, Hapus Baris Ini';
        btn.disabled  = false;
    });
}

// ============================================================
// REINDEX & COUNTERS
// ============================================================
function reindexRows() {
    // Re-index both tables
    document.querySelectorAll('.import-preview-body').forEach(tbody => {
        const rows = tbody.querySelectorAll('tr:not([style*="display: none"])');
        let num = 1;
        rows.forEach(tr => {
            const numCell = tr.querySelector('.row-num');
            if (numCell) numCell.textContent = num++;
        });
    });
}

function updateCounters() {
    const totalVisible = document.querySelectorAll('.import-preview-body tr').length;
    const warningVisible = document.querySelectorAll('#warningPreviewBody tr').length;
    const normalVisible = document.querySelectorAll('#normalPreviewBody tr').length;

    document.getElementById('statTotal').textContent   = totalVisible;
    if (document.getElementById('warningCounter')) {
        document.getElementById('warningCounter').textContent = warningVisible;
    }
    document.getElementById('normalCounter').textContent = normalVisible;
    document.getElementById('saveCount').textContent   = totalVisible;
    document.getElementById('btnCount').textContent    = totalVisible;

    // Recount warnings for the stat top card
    let warnCount = 0;
    document.querySelectorAll('.import-preview-body tr').forEach(tr => {
        if (tr.querySelector('.badge-warning-row')) warnCount++;
    });
    document.getElementById('statWarning').textContent = warnCount;
}

// ============================================================
// SAVE FORM
// ============================================================
function onSave() {
    const count = document.querySelectorAll('.import-preview-body tr').length;
    if (count === 0) {
        alert('Tidak ada data untuk disimpan.');
        return false;
    }
    document.getElementById('savingOverlay').style.display = 'flex';

    // Check if any PKOK input still has unsaved dirty state
    const dirtyInputs = document.querySelectorAll('.pkok-input.is-dirty');
    if (dirtyInputs.length > 0) {
        if (!confirm(dirtyInputs.length + ' nilai Pokok belum disimpan (tombol ✓ belum diklik). Lanjut simpan dengan nilai yang tampil sekarang?')) {
            document.getElementById('savingOverlay').style.display = 'none';
            return false;
        }
    }
    return true;
}

// ============================================================
// SEARCH
// ============================================================
document.getElementById('searchInput')?.addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('.import-preview-body tr').forEach(tr => {
        const text = tr.textContent.toLowerCase();
        tr.style.display = text.includes(keyword) ? '' : 'none';
    });
    reindexRows();
});

// ============================================================
// UTILS
// ============================================================
function formatRupiah(val) {
    return Math.round(val).toLocaleString('id-ID');
}

let toastTimeout;
function showToast(type, message) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(container);
    }

    const colors = { success: '#1a7f5a', danger: '#dc3545', warning: '#f59e0b' };
    const icons  = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill' };

    const toast  = document.createElement('div');
    toast.style.cssText = `background:#fff;border-left:4px solid ${colors[type] || '#333'};padding:12px 18px;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.15);
        display:flex;align-items:center;gap:10px;font-size:0.875rem;min-width:260px;animation:slideIn 0.25s ease;`;
    toast.innerHTML = `<i class="bi ${icons[type] || 'bi-info-circle'}" style="color:${colors[type]};font-size:1.1rem;"></i><span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 350);
    }, 3500);
}
</script>
<style>
@keyframes slideIn {
    from { transform: translateX(40px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
</style>
@endpush
