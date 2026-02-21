@extends('layouts.admin')
@section('title', 'Import Excel')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-excel-fill me-2"></i>Import Data Potongan dari Excel</h4>
</div>

<!-- Info Box -->
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

<!-- Upload Form -->
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

            <!-- Drag & Drop Zone -->
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

            <!-- Progress (shown during upload) -->
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

@push('scripts')
<script>
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

        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressText').textContent = Math.round(progress) + '%';
        }, 500);
    });
</script>
@endpush
@endsection
