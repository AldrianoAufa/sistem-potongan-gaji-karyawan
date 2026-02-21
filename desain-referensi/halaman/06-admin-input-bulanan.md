# 💰 Admin — Input Potongan Bulanan

## Layout
- Sidebar kiri + konten utama

## Wireframe — Halaman Index

```
┌──────┬──────────────────────────────────────┐
│ SIDE │  Input Potongan Bulanan              │
│ BAR  │──────────────────────────────────────│
│      │  [+ Tambah]  [📤 Import Excel]       │
│      │                                      │
│      │  Filter: Bulan [▼ Jan] Tahun [▼ 2026]│
│      │  🔍 [Cari nama/kode anggota...]      │
│      │                                      │
│      │  ┌───┬──────┬──────┬──────┬────┬───┐ │
│      │  │No │Kode  │Nama  │Jenis │Jml │Aks│ │
│      │  ├───┼──────┼──────┼──────┼────┼───┤ │
│      │  │1  │C001  │Ahmad │KOPER │500K│✏🗑│ │
│      │  │2  │C001  │Ahmad │BPJS  │200K│✏🗑│ │
│      │  │3  │C002  │Budi  │PINJ.P│1.2M│✏🗑│ │
│      │  └───┴──────┴──────┴──────┴────┴───┘ │
│      │                                      │
│      │  Total Potongan: Rp 1.900.000        │
│      │  ◀ 1  2  3  4  5 ▶                   │
└──────┴──────────────────────────────────────┘
```

## Wireframe — Form Input Manual

```
┌─────────────────────────────────┐
│  ✕  Tambah Potongan Bulanan     │
│─────────────────────────────────│
│                                 │
│  Anggota *                      │
│  ┌─────────────────────────┐    │
│  │ 🔍 Cari anggota...      │    │  ← searchable select
│  └─────────────────────────┘    │
│                                 │
│  Jenis Potongan *               │
│  ┌─────────────────────────┐    │
│  │ ▼ Pilih Jenis Potongan  │    │
│  └─────────────────────────┘    │
│                                 │
│  Bulan *          Tahun *       │
│  ┌──────────┐    ┌──────────┐   │
│  │ ▼ Januari│    │ ▼ 2026   │   │
│  └──────────┘    └──────────┘   │
│                                 │
│  Jumlah Potongan (Rp) *        │
│  ┌─────────────────────────┐    │
│  │ 0                       │    │  ← number input
│  └─────────────────────────┘    │
│                                 │
│  ── Detail Pinjaman (opsional)──│
│                                 │
│  Pinjaman    Saldo Awal         │
│  ┌────────┐  ┌──────────┐       │
│  │ 0      │  │ 0        │       │
│  └────────┘  └──────────┘       │
│                                 │
│  Bulan Ke    Kali Angsuran      │
│  ┌────────┐  ┌──────────┐       │
│  │ 0      │  │ 0        │       │
│  └────────┘  └──────────┘       │
│                                 │
│  Pokok       Sisa Saldo         │
│  ┌────────┐  ┌──────────┐       │
│  │ 0      │  │ 0        │       │
│  └────────┘  └──────────┘       │
│                                 │
│  [Batal]          [💾 Simpan]   │
└─────────────────────────────────┘
```

## Komponen
- **Filter:** dropdown bulan + tahun, auto-refresh tabel
- **Search:** autocomplete atau filter oleh nama/kode
- **Tabel:** menampilkan total di bawah
- **Form:** 
  - Anggota: searchable select (select2 style)
  - Detail pinjaman: collapsible section, hanya muncul jika jenis potongan = pinjaman
  - Validasi: jumlah harus > 0, bulan/tahun required
- **Tombol Import:** link ke halaman import Excel
