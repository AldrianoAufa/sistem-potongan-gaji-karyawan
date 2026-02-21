@extends('layouts.admin')
@section('title', 'Edit Anggota')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2"></i>Edit Anggota</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.anggota.index') }}">Anggota</a></li>
            <li class="breadcrumb-item active">Edit — {{ $anggota->nama }}</li>
        </ol>
    </nav>
</div>

<div class="card card-custom" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.anggota.update', $anggota) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Anggota <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('kode_anggota') is-invalid @enderror"
                       name="kode_anggota" value="{{ old('kode_anggota', $anggota->kode_anggota) }}" required>
                @error('kode_anggota')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Anggota <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                       name="nama" value="{{ old('nama', $anggota->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                <select class="form-select @error('jabatan_id') is-invalid @enderror" name="jabatan_id" required>
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatan as $j)
                        <option value="{{ $j->id }}" {{ old('jabatan_id', $anggota->jabatan_id) == $j->id ? 'selected' : '' }}>
                            {{ $j->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
                @error('jabatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
