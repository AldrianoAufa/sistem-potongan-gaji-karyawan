@extends('layouts.admin')
@section('title', 'Import Data karyawan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-people-fill me-2"></i>Import Data karyawan dari Excel</h4>
</div>

<!-- Info Box: Required Columns -->
<div class="card card-custom mb-3" style="border-left: 4px solid #28A745;">
    <div class="card-body">
        <h6 class="text-success mb-2"><i class="bi bi-check-circle-fill me-1"></i>Kolom Wajib</h6>
        <div class="d-flex flex-wrap gap-1 mb-2">
            @foreach(['KODE','NAMA','JABATAN','DEPARTEMEN'] as $col)
            <span class="badge bg-success" style="font-size: 0.8rem;">{{ $col }}</span>
            @endforeach
        </div>
        <small class="text-muted">
            <strong>KODE</strong> = NIK (unik) &nbsp;|&nbsp;
            <strong>NAMA</strong> = Nama lengkap &nbsp;|&nbsp;
            <strong>JABATAN</strong> = Nama jabatan &nbsp;|&nbsp;
            <strong>DEPARTEMEN</strong> = Kode departemen
        </small>
    </div>
</div>


<!-- Behavior Note -->
<div class="alert alert-warning py-2 mb-4" style="font-size: 0.85rem;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Catatan:</strong> Jika NIK sudah ada di database, data karyawan tersebut akan <strong>diperbarui</strong> (update), bukan diduplikasi.
    Jabatan yang belum ada akan <strong>otomatis dibuat</strong>.
</div>

<!-- Upload Form -->
<div class="card card-custom" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.import-karyawan.process') }}" enctype="multipart/form-data" id="importForm">
            @csrf

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
                    <div id="fileName" class="mt-2 fw-semibold text-success" style="display: none;"></div>
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

            <!-- Progress -->
            <div id="progressSection" style="display: none;" class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="fw-semibold">Memproses...</small>
                    <small id="progressText">0%</small>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="progressBar" style="width: 0%"></div>
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
        dropZone.style.borderColor = '#28A745';
        dropZone.style.background = 'rgba(40,167,69,0.05)';
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
        if (fileInput.files.length) showFileName(fileInput.files[0]);
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
</script>
@endpush
@endsection
