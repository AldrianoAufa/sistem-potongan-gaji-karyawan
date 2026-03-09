<div class="slip-paper {{ $isDashboard ?? false ? 'mx-auto' : '' }}" id="{{ $id ?? 'slipCetak' }}">
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
</div>
