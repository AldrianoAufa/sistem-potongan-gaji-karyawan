@extends('layouts.admin')
@section('title', 'Input Potongan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-excel-fill me-2"></i>Input Potongan Gaji</h4>
    <p class="text-muted mb-0">Pilih metode input: Import file Excel atau Input Kolektif per jenis potongan</p>
</div>

{{-- Method Toggle Tabs --}}
<ul class="nav nav-tabs mb-4" id="inputMethodTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ !request('jenis_potongan_id') ? 'active' : '' }}" id="import-tab" data-bs-toggle="tab" data-bs-target="#importTab" type="button" role="tab">
            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request('jenis_potongan_id') ? 'active' : '' }}" id="collective-tab" data-bs-toggle="tab" data-bs-target="#collectiveTab" type="button" role="tab">
            <i class="bi bi-collection-fill me-1"></i>Input Kolektif
        </button>
    </li>
</ul>

<div class="tab-content" id="inputMethodTabContent">
    {{-- ========= TAB 1: IMPORT EXCEL ========= --}}
    <div class="tab-pane fade {{ !request('jenis_potongan_id') ? 'show active' : '' }}" id="importTab" role="tabpanel">
        {{-- Info Box --}}
        <div class="card card-custom mb-4" style="border-left: 4px solid #17A2B8;">
            <div class="card-body">
                <h6 class="text-info mb-2"><i class="bi bi-info-circle-fill me-1"></i>Format Kolom yang Diharapkan</h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach(['URUT','KDPR','NMPR','CUST','NAMA','GRUP','NMGR','PINJ','AWAL','BULN','KALI','PKOK','RPBG','ANGS','SALD'] as $col)
                    <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">{{ $col }}</span>
                    @endforeach
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Header kolom pada file Excel harus <strong>persis sesuai</strong> dengan daftar di atas.
                </small>
            </div>
        </div>

        {{-- Upload Form --}}
        <div class="card card-custom" style="max-width: 600px;">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.import.process') }}" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                                <option value="{{ $i+1 }}" {{ now()->month == $i+1 ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                            <select name="tahun" class="form-select" required>
                                @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Drag & Drop Zone --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Excel <span class="text-danger">*</span></label>
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center" id="dropZone"
                             style="cursor: pointer; transition: all 0.2s;">
                            <i class="bi bi-cloud-upload text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mb-1 mt-2">Drag & drop file Excel di sini</p>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">atau klik untuk memilih file</p>
                            <p class="text-muted mb-0" style="font-size: 0.75rem;">Format: .xlsx, .xls | Maks: 10MB</p>
                            <input type="file" name="file" id="fileInput" accept=".xlsx,.xls" required class="d-none">
                            <div id="fileName" class="mt-2 fw-semibold text-primary" style="display: none;"></div>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger py-2 mb-3">
                        @foreach($errors->all() as $error)
                        <div><i class="bi bi-x-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                        <i class="bi bi-upload me-1"></i>Upload & Proses
                    </button>

                    <div id="progressSection" style="display: none;" class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-semibold">Memproses...</small>
                            <small id="progressText">0%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========= TAB 2: INPUT KOLEKTIF ========= --}}
    <div class="tab-pane fade {{ request('jenis_potongan_id') ? 'show active' : '' }}" id="collectiveTab" role="tabpanel">
        {{-- Step 1: Pilih Jenis Potongan & Periode --}}
        <div class="card card-custom mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-1-circle me-2"></i>Pilih Jenis Potongan & Periode</h6>
            </div>
            <div class="card-body">
                <form id="kolektifFilterForm" method="GET" action="{{ route('admin.import.form') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Potongan</label>
                            <select class="form-select" name="jenis_potongan_id" onchange="this.form.submit()" required>
                                <option value="">-- Pilih Jenis Potongan --</option>
                                @foreach($jenisPotonganAll as $jp)
                                    <option value="{{ $jp->id }}" {{ request('jenis_potongan_id') == $jp->id ? 'selected' : '' }}>
                                        {{ $jp->nama_potongan }} ({{ $jp->kode_potongan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select class="form-select" name="bulan_kolektif">
                                @foreach($bulanOptions as $num => $name)
                                    <option value="{{ $num }}" {{ (request('bulan_kolektif', date('n')) == $num) ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Tahun</label>
                            <input type="number" class="form-control" name="tahun_kolektif" value="{{ request('tahun_kolektif', date('Y')) }}" min="2020" max="2099">
                        </div>
                        <div class="col-md-3">
                            @if(request('jenis_potongan_id'))
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-arrow-repeat me-1"></i> Refresh
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedPotongan)
            {{-- Step 2: Input Nominal --}}
            <div class="card card-custom">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-2-circle me-2"></i>Input: <strong>{{ $selectedPotongan->nama_potongan }}</strong></h6>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light">Set Semua Rp</span>
                            <input type="number" id="setAllNominal" class="form-control" placeholder="Nominal...">
                            <button class="btn btn-outline-secondary" type="button" onclick="applySetAll()">Terapkan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('admin.import.collective') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jenis_potongan_id" value="{{ $selectedPotongan->id }}">
                        <input type="hidden" name="bulan" value="{{ request('bulan_kolektif', date('n')) }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun_kolektif', date('Y')) }}">

                        <div class="table-responsive">
                            <table class="table table-hover table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th style="width: 150px;">NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th style="width: 250px;">Nominal Potongan (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($karyawanKolektif as $i => $k)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td><span class="badge bg-light text-dark fw-semibold">{{ $k->kode_karyawan }}</span></td>
                                            <td class="fw-medium">{{ $k->nama }}</td>
                                            <td>
                                                <input type="hidden" name="potongan[{{ $i }}][karyawan_id]" value="{{ $k->id }}">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" name="potongan[{{ $i }}][jumlah]"
                                                           class="form-control nominal-input"
                                                           placeholder="Masukkan nominal..."
                                                           min="0">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-person-exclamation fs-3 d-block mb-2"></i>
                                                Belum ada karyawan yang terdaftar untuk jenis potongan ini.<br>
                                                Silakan atur di menu <strong>Mapping Potongan</strong>.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(count($karyawanKolektif) > 0)
                            <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle me-1"></i> Data yang kosong atau bernilai 0 tidak akan disimpan.
                                </div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Simpan Semua Potongan
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-info border-0 shadow-sm">
                <i class="bi bi-info-circle-fill me-2"></i> Silakan pilih <strong>Jenis Potongan</strong> di atas untuk memulai input data kolektif.
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // ===== Import Excel: Drag & Drop =====
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#4A90D9';
        dropZone.style.background = 'rgba(74,144,217,0.05)';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '';
        dropZone.style.background = '';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '';
        dropZone.style.background = '';
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showFileName(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            showFileName(fileInput.files[0]);
        }
    });

    function showFileName(file) {
        fileName.style.display = 'block';
        fileName.innerHTML = '<i class="bi bi-file-earmark-excel me-1"></i>' + file.name +
            ' <span class="text-muted">(' + (file.size / 1024 / 1024).toFixed(2) + ' MB)</span>';
    }

    document.getElementById('importForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';
        document.getElementById('progressSection').style.display = 'block';

        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = Math.round(progress) + '%';
        }, 500);
    });

    // ===== Input Kolektif: Set All =====
    function applySetAll() {
        const value = document.getElementById('setAllNominal').value;
        if (value === '') return;
        document.querySelectorAll('.nominal-input').forEach(input => {
            input.value = value;
        });
    }

    // ===== Modal Departemen: Search =====
    document.querySelectorAll('.dept-karyawan-search').forEach(function(input) {
        input.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();
            const tableId = this.getAttribute('data-target');
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            const modalId = tableId.replace('deptTable', '');
            const noResult = document.getElementById('deptNoResult' + modalId);
            const countSpan = document.querySelector('.dept-shown-count' + modalId);
            let visibleCount = 0;

            rows.forEach(function(row) {
                const nik = row.cells[1] ? row.cells[1].textContent.toLowerCase() : '';
                const nama = row.cells[2] ? row.cells[2].textContent.toLowerCase() : '';
                if (nik.includes(keyword) || nama.includes(keyword)) {
                    row.style.display = '';
                    visibleCount++;
                    row.cells[0].textContent = visibleCount;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noResult) noResult.classList.toggle('d-none', visibleCount > 0);
            if (countSpan) countSpan.textContent = visibleCount;
        });
    });

    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            const searchInput = this.querySelector('.dept-karyawan-search');
            if (searchInput) {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            }
        });
    });
</script>
@endpush
@endsection
