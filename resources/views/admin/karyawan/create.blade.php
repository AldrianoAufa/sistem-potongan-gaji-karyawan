@extends('layouts.admin')
@section('title', 'Tambah karyawan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person-plus-fill me-2"></i>Tambah karyawan</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.karyawan.index') }}">karyawan</a></li>
            <li class="breadcrumb-item active">Tambah</li>
        </ol>
    </nav>
</div>

<div class="card card-custom" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.karyawan.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('kode_karyawan') is-invalid @enderror"
                       name="kode_karyawan" value="{{ old('kode_karyawan') }}" placeholder="Contoh: C006" required>
                @error('kode_karyawan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama karyawan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                       name="nama" value="{{ old('nama') }}" placeholder="Nama lengkap" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                <select class="form-select @error('jabatan_id') is-invalid @enderror" name="jabatan_id" required>
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatan as $j)
                        <option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
                @error('jabatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Departemen <span class="text-danger">*</span></label>
                <select class="form-select @error('departemen_id') is-invalid @enderror" name="departemen_id" required>
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemen as $d)
                        <option value="{{ $d->id }}" {{ old('departemen_id') == $d->id ? 'selected' : '' }}>
                            [{{ $d->kode_departemen }}] {{ $d->nama_departemen }}
                        </option>
                    @endforeach
                </select>
                @error('departemen_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr>

            <div id="akunFields" style="display: {{ old('buat_akun') ? 'block' : 'none' }};">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('username') }}" placeholder="Username untuk login">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" placeholder="Minimal 6 karakter">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('buatAkun').addEventListener('change', function() {
        document.getElementById('akunFields').style.display = this.checked ? 'block' : 'none';
    });
</script>
@endpush
@endsection
