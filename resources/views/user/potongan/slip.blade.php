@extends('layouts.user')
@section('title', 'Slip Potongan Gaji - ' . $namaBulan . ' ' . $tahun)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3 no-print">
    <div>
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Slip Bukti Potongan Gaji</h5>
        <small class="text-muted">{{ $namaBulan }} {{ $tahun }}</small>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Cetak / Print
        </button>
        <a href="{{ route('user.potongan.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

{{-- Slip Container --}}
<div class="slip-wrapper">
    <div class="slip-paper" id="slipCetak">

        {{-- Header Organisasi --}}
        <div class="slip-header">
            <div class="slip-org">PRIMKOPKAR "PRIMA" BATANG</div>
            <div class="slip-title">BUKTI POTONGAN GAJI</div>
            <div class="slip-period">BULAN {{ strtoupper($namaBulan) }} {{ $tahun }}</div>
        </div>

        <div class="slip-divider-thick"></div>

        {{-- Info Anggota --}}
        <div class="slip-info-grid">
            <div class="slip-info-row">
                <span class="slip-info-label">Anggota</span>
                <span class="slip-info-sep">:</span>
                <span class="slip-info-value">
                    {{ $karyawan->kode_karyawan }}/{{ $karyawan->nama }}
                </span>
            </div>
            <div class="slip-info-row">
                <span class="slip-info-label">Bagian</span>
                <span class="slip-info-sep">:</span>
                <span class="slip-info-value">
                    {{ $karyawan->departemen->nama_departemen ?? '-' }}
                    &nbsp;&nbsp;
                    {{ $karyawan->jabatan->nama_jabatan ?? '' }}
                </span>
            </div>
        </div>

        <div class="slip-divider-thick"></div>

        {{-- Tabel Potongan --}}
        <table class="slip-table">
            <thead>
                <tr>
                    <th class="col-jenis">Jenis Potongan</th>
                    <th class="col-tgl">T.Pinjam</th>
                    <th class="col-num text-right">Angs.Pokok</th>
                    <th class="col-num text-right">Jasa</th>
                    <th class="col-ke text-center">Ke/dari</th>
                    <th class="col-num text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($potonganList as $item)
                @php
                    $r = $item->data_rinci ?? [];
                    // Tanggal pinjaman dari data_rinci jika ada
                    $tPinjam = '';
                    if (!empty($r['TGL_PINJAM'])) {
                        $tPinjam = $r['TGL_PINJAM'];
                    } elseif (!empty($r['TANGGAL'])) {
                        $tPinjam = $r['TANGGAL'];
                    }

                    $angsPkok = $r['PKOK'] ?? (empty($r) ? $item->jumlah_potongan : 0);
                    $jasa     = $r['RPBG'] ?? 0;
                    $bulanKe  = isset($r['BULN']) ? $r['BULN'] : '';
                    $totalKali = isset($r['KALI']) ? $r['KALI'] : '';
                    $saldo    = $r['SALD'] ?? 0;
                @endphp
                <tr>
                    <td class="col-jenis">{{ $no++ }}.{{ $item->jenisPotongan->nama_potongan }}</td>
                    <td class="col-tgl">{{ $tPinjam }}</td>
                    <td class="col-num text-right">{{ $angsPkok > 0 ? number_format($angsPkok, 0, ',', '.') : '' }}</td>
                    <td class="col-num text-right">{{ $jasa > 0 ? number_format($jasa, 0, ',', '.') : '0' }}</td>
                    <td class="col-ke text-center">
                        @if($bulanKe !== '' && $totalKali !== '')
                            {{ $bulanKe }}/ {{ $totalKali }}
                        @else
                            /
                        @endif
                    </td>
                    <td class="col-num text-right">{{ $saldo > 0 ? number_format($saldo, 0, ',', '.') : '0' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="slip-total-row">
                    <td colspan="2"></td>
                    <td class="col-num text-right">{{ number_format($totalPokok, 0, ',', '.') }}</td>
                    <td class="col-num text-right">{{ number_format($totalJasa, 0, ',', '.') }}</td>
                    <td></td>
                    <td class="col-num text-right">
                        @php
                            $totalSaldo = $potonganList->sum(fn($p) => $p->data_rinci['SALD'] ?? 0);
                        @endphp
                        {{ number_format($totalSaldo, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="slip-divider-thin"></div>

        {{-- Total Potongan --}}
        <div class="slip-grand-total">
            <span class="slip-grand-label">Total Potongan (Pokok + Jasa )</span>
            <span class="slip-grand-eq">=</span>
            <span class="slip-grand-value">{{ number_format($totalPotongan, 0, ',', '.') }}</span>
        </div>

        <div class="slip-divider-thin"></div>

        {{-- Terbilang --}}
        <div class="slip-terbilang">
            <span class="slip-terb-label">Terbilang :</span>
            <span class="slip-terb-value">{{ $terbilang }}</span>
        </div>

        <div class="slip-divider-thin"></div>

        {{-- Footer --}}
        <div class="slip-footer">
            <div class="slip-catatan">
                <span>Cat.DK:</span>
            </div>
            <div class="slip-ttd">
                <div>Batang, {{ $namaBulan }} {{ $tahun }}</div>
                <div>Petugas</div>
                <div class="slip-ttd-space"></div>
                <div class="slip-ttd-line">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
            </div>
        </div>

    </div>{{-- end slip-paper --}}
</div>{{-- end slip-wrapper --}}

@push('styles')
<style>
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

/* === PRINT STYLES === */
@media print {
    /* Hide everything outside .slip-paper */
    body * { visibility: hidden; }
    #slipCetak, #slipCetak * { visibility: visible; }
    #slipCetak {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
        border: none;
        box-shadow: none;
    }
    .no-print { display: none !important; }
    .slip-paper {
        border: none;
        box-shadow: none;
        font-size: 10px;
    }
}
</style>
@endpush
@endsection
