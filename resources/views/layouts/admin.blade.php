@extends('layouts.app')

@section('body')
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h6>Sistem Potongan Gaji</h6>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        {{-- Grup 1: Karyawan --}}
        <div class="sidebar-heading">Karyawan</div>

        <a class="nav-link {{ request()->routeIs('admin.karyawan.index') || request()->routeIs('admin.karyawan.create') || request()->routeIs('admin.karyawan.edit') ? 'active' : '' }}"
           href="{{ route('admin.karyawan.index') }}">
            <i class="bi bi-person-lines-fill"></i> Daftar Karyawan
        </a>
        <a class="nav-link {{ request()->routeIs('admin.karyawan.mapping') ? 'active' : '' }}"
           href="{{ route('admin.karyawan.mapping') }}">
            <i class="bi bi-clipboard-check"></i> Mapping Potongan
        </a>
        <a class="nav-link {{ request()->routeIs('admin.departemen.*') ? 'active' : '' }}"
           href="{{ route('admin.departemen.index') }}">
            <i class="bi bi-diagram-3-fill"></i> Departemen
        </a>
        <a class="nav-link {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}"
           href="{{ route('admin.jabatan.index') }}">
            <i class="bi bi-building-fill"></i> Jabatan
        </a>

        {{-- Grup 2: Potongan Gaji --}}
        <div class="sidebar-heading">Potongan Gaji</div>

        <a class="nav-link {{ request()->routeIs('admin.jenis-potongan.*') ? 'active' : '' }}"
           href="{{ route('admin.jenis-potongan.index') }}">
            <i class="bi bi-clipboard2-data-fill"></i> Jenis Potongan
        </a>
        <a class="nav-link {{ request()->routeIs('admin.input-bulanan.*') ? 'active' : '' }}"
           href="{{ route('admin.input-bulanan.index') }}">
            <i class="bi bi-cash-coin"></i> Input Bulanan
        </a>
        <a class="nav-link {{ request()->routeIs('admin.import.*') ? 'active' : '' }}"
           href="{{ route('admin.import.form') }}">
            <i class="bi bi-file-earmark-excel-fill"></i> Input Potongan
        </a>

        {{-- Grup 3: Laporan --}}
        <div class="sidebar-heading">Laporan</div>

        <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
           href="{{ route('admin.laporan.index') }}">
            <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
        </a>
    </nav>
</aside>

<!-- Main Content -->
<main class="main-content">
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
