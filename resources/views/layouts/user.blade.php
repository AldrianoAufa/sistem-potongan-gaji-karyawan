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

    /* ===== SLIP PAPER STYLES ===== */
    .slip-wrapper {
        display: flex;
        justify-content: center;
        padding: 20px 0 40px;
    }

    .slip-paper {
        background: #fff;
        width: 420px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        line-height: 1.5;
        padding: 20px 24px 24px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        border-radius: 4px;
        color: #333;
    }

    /* Header */
    .slip-header {
        text-align: center;
        margin-bottom: 8px;
    }
    .slip-org {
        font-size: 11px;
        font-weight: normal;
        letter-spacing: 0.5px;
    }
    .slip-title {
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 2px;
        margin: 4px 0 2px;
    }
    .slip-period {
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    /* Dividers */
    .slip-divider-thick {
        border-top: 2px dashed #555;
        margin: 6px 0;
    }
    .slip-divider-thin {
        border-top: 1px dashed #888;
        margin: 6px 0;
    }

    /* Info Anggota */
    .slip-info-grid {
        margin: 6px 0;
    }
    .slip-info-row {
        display: flex;
        gap: 0;
        margin-bottom: 2px;
    }
    .slip-info-label {
        min-width: 58px;
        font-size: 11px;
    }
    .slip-info-sep {
        margin: 0 4px;
    }
    .slip-info-value {
        font-size: 11px;
        font-weight: bold;
    }

    /* Table */
    .slip-table {
        width: 100%;
        border-collapse: collapse;
        margin: 4px 0;
        font-size: 10.5px;
    }
    .slip-table th {
        font-size: 10px;
        padding: 2px 3px;
        border-bottom: 1px solid #333;
        font-weight: normal;
        white-space: nowrap;
    }
    .slip-table td {
        padding: 2px 3px;
        vertical-align: top;
    }
    .slip-table tfoot td {
        padding-top: 4px;
        border-top: 1px dashed #888;
    }
    .col-jenis  { width: 38%; }
    .col-tgl    { width: 14%; white-space: nowrap; }
    .col-num    { width: 14%; white-space: nowrap; }
    .col-ke     { width: 10%; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    .slip-total-row td {
        font-weight: bold;
    }

    /* Grand Total */
    .slip-grand-total {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin: 4px 0;
        font-size: 12px;
        font-weight: bold;
    }
    .slip-grand-label { flex: 1; font-size: 11px; }
    .slip-grand-eq { font-size: 12px; }
    .slip-grand-value { font-size: 13px; letter-spacing: 1px; }

    /* Terbilang */
    .slip-terbilang {
        font-size: 10.5px;
        margin: 4px 0;
        display: flex;
        gap: 4px;
    }
    .slip-terb-label { white-space: nowrap; font-size: 10.5px; }
    .slip-terb-value { font-style: italic; }

    /* Footer TTD */
    .slip-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-top: 8px;
        font-size: 11px;
    }
    .slip-catatan {
        flex: 1;
    }
    .slip-ttd {
        text-align: center;
        min-width: 130px;
    }
    .slip-ttd-space {
        height: 50px;
    }
    .slip-ttd-line {
        font-size: 11px;
    }

    @media print {
        .no-print { display: none !important; }
        body * { visibility: hidden; }
        .slip-to-print, .slip-to-print * { visibility: visible; }
        .slip-to-print {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
        }
    }
</style>
@endpush
