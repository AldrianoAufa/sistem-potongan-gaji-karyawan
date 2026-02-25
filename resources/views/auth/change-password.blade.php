@extends($layout)
@section('title', 'Ganti Password')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-key-fill me-2"></i>Ganti Password</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Ganti Password</li>
        </ol>
    </nav>
</div>

<div class="card card-custom" style="max-width: 500px;">
    <div class="card-header">
        <i class="bi bi-shield-lock me-2"></i>Ubah Password Anda
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.change') }}">
            @csrf

            <div class="mb-3">
                <label for="current_password" class="form-label fw-semibold" style="font-size: 0.85rem;">
                    Password Lama <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                           id="current_password" name="current_password" placeholder="Masukkan password lama">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold" style="font-size: 0.85rem;">
                    Password Baru <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" placeholder="Minimal 6 karakter">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold" style="font-size: 0.85rem;">
                    Konfirmasi Password Baru <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                    <input type="password" class="form-control"
                           id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Simpan Password
                </button>
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
