# 📑 Admin — Laporan

## Layout
- Sidebar kiri + konten utama

## Wireframe

```
┌──────┬──────────────────────────────────────┐
│ SIDE │  Laporan Potongan Gaji               │
│ BAR  │──────────────────────────────────────│
│      │                                      │
│      │  ┌───────────────────────────────────┐│
│      │  │ Filter Laporan                    ││
│      │  │                                   ││
│      │  │ Bulan [▼ Semua]  Tahun [▼ 2026]   ││
│      │  │ Jenis [▼ Semua]  karyawan [🔍...]  ││
│      │  │                                   ││
│      │  │ [🔍 Tampilkan]  [📥 Export Excel]  ││
│      │  └───────────────────────────────────┘│
│      │                                      │
│      │  Rekapitulasi: Januari 2026          │
│      │  Total: Rp 125.450.000              │
│      │                                      │
│      │  ┌───┬──────┬──────┬──────┬────────┐ │
│      │  │No │Kode  │Nama  │Jenis │Jumlah  │ │
│      │  ├───┼──────┼──────┼──────┼────────┤ │
│      │  │1  │C001  │Ahmad │KOPER │500.000 │ │
│      │  │2  │C001  │Ahmad │BPJS  │200.000 │ │
│      │  │3  │C002  │Budi  │PINJ.P│1.200K  │ │
│      │  │...│...   │...   │...   │...     │ │
│      │  └───┴──────┴──────┴──────┴────────┘ │
│      │                                      │
│      │  ◀ 1  2  3 ... 10 ▶                  │
│      │                                      │
│      │  ┌───────────────────────────────────┐│
│      │  │ 📊 Ringkasan per Jenis Potongan   ││
│      │  │                                   ││
│      │  │ KOPER    : Rp  45.000.000 (36%)   ││
│      │  │ PINJ.P   : Rp  35.000.000 (28%)   ││
│      │  │ BPJS     : Rp  25.000.000 (20%)   ││
│      │  │ PINJ.D   : Rp  20.450.000 (16%)   ││
│      │  └───────────────────────────────────┘│
└──────┴──────────────────────────────────────┘
```

## Komponen
- **Filter Bar:** card dengan dropdowns + tombol tampilkan & export
- **Summary:** total rupiah, jumlah karyawan terfilter
- **Tabel Detail:** semua data potongan sesuai filter, paginated
- **Ringkasan per Jenis:** breakdown total per jenis potongan, dengan persentase
- **Export Excel:** download file Excel sesuai filter aktif
- **Cetak (opsional):** tombol print-friendly view

## Responsif
- Filter: stack vertical di mobile
- Tabel: horizontal scroll di mobile
- Ringkasan: tetap readable di mobile
