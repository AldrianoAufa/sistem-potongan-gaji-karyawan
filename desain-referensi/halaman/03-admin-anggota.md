# 👥 Admin — Kelola Anggota

## Layout
- Sidebar kiri + konten utama (sama seperti dashboard)

## Wireframe — Halaman Index

```
┌──────┬──────────────────────────────────────┐
│ SIDE │  Kelola Anggota                      │
│ BAR  │──────────────────────────────────────│
│      │  [+ Tambah Anggota]    🔍[Search...] │
│      │                                      │
│      │  ┌────┬──────┬──────┬──────┬───────┐ │
│      │  │No  │Kode  │Nama  │Jaba- │Aksi   │ │
│      │  │    │      │      │tan   │       │ │
│      │  ├────┼──────┼──────┼──────┼───────┤ │
│      │  │1   │C001  │Ahmad │Staff │✏️ 🗑️  │ │
│      │  │2   │C002  │Budi  │SPV   │✏️ 🗑️  │ │
│      │  │3   │C003  │Citra │Staff │✏️ 🗑️  │ │
│      │  └────┴──────┴──────┴──────┴───────┘ │
│      │                                      │
│      │  ◀ 1  2  3  4  5 ▶                   │
└──────┴──────────────────────────────────────┘
```

## Wireframe — Form Tambah/Edit (Modal atau Halaman Baru)

```
┌─────────────────────────────────┐
│  ✕  Tambah Anggota              │
│─────────────────────────────────│
│                                 │
│  Kode Anggota *                 │
│  ┌─────────────────────────┐    │
│  │ C004                    │    │
│  └─────────────────────────┘    │
│                                 │
│  Nama Anggota *                 │
│  ┌─────────────────────────┐    │
│  │ Nama Lengkap            │    │
│  └─────────────────────────┘    │
│                                 │
│  Jabatan *                      │
│  ┌─────────────────────────┐    │
│  │ ▼ Pilih Jabatan         │    │
│  └─────────────────────────┘    │
│                                 │
│  [Batal]          [💾 Simpan]   │
└─────────────────────────────────┘
```

## Komponen
- **Tombol Tambah:** warna Primary, ikon +
- **Tabel:** striped, hover, responsive
- **Search:** real-time filter atau server-side search
- **Aksi Edit:** tombol kecil warna Warning (✏️)
- **Aksi Hapus:** tombol kecil warna Danger (🗑️), konfirmasi via modal
- **Form:** validasi client-side + server-side, required fields ditandai *
- **Dropdown Jabatan:** select2 atau select biasa, data dari tabel jabatan
- **Pagination:** 15 item per halaman

## Responsif
- Tabel: horizontal scroll di mobile
- Form modal: full-screen di mobile
