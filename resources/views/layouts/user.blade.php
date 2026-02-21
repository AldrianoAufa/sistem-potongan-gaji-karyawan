@extends('layouts.app')

@section('body')
<!-- Main Content (no sidebar for user) -->
<main class="container-fluid" style="padding: 1.5rem; max-width: 1200px; margin: 0 auto;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-auto-dismiss" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>
@endsection

@push('styles')
<style>
    .main-content {
        margin-left: 0 !important;
    }
</style>
@endpush
