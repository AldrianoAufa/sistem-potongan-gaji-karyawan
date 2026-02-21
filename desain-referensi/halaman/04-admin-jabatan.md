# 🏢 Admin — Kelola Jabatan

## Layout
- Sidebar kiri + konten utama

## Wireframe

```
┌──────┬──────────────────────────────────────┐
│ SIDE │  Kelola Jabatan                      │
│ BAR  │──────────────────────────────────────│
│      │  [+ Tambah Jabatan]                  │
│      │                                      │
│      │  ┌────┬──────────────────┬───────┐   │
│      │  │No  │Nama Jabatan      │Aksi   │   │
│      │  ├────┼──────────────────┼───────┤   │
│      │  │1   │Staff Produksi    │✏️ 🗑️  │   │
│      │  │2   │Supervisor        │✏️ 🗑️  │   │
│      │  │3   │Manager           │✏️ 🗑️  │   │
│      │  │4   │Kepala Bagian     │✏️ 🗑️  │   │
│      │  └────┴──────────────────┴───────┘   │
│      │                                      │
│      │  ◀ 1  2 ▶                            │
└──────┴──────────────────────────────────────┘
```

## Form Tambah/Edit (Modal)

```
┌─────────────────────────────────┐
│  ✕  Tambah Jabatan              │
│─────────────────────────────────│
│                                 │
│  Nama Jabatan *                 │
│  ┌─────────────────────────┐    │
│  │ Masukkan nama jabatan   │    │
│  └─────────────────────────┘    │
│                                 │
│  [Batal]          [💾 Simpan]   │
└─────────────────────────────────┘
```

## Komponen
- Halaman sederhana karena hanya 1 field (nama_jabatan)
- CRUD via modal untuk efisiensi
- Validasi: nama_jabatan required, unique
- Hapus: konfirmasi modal, cek apakah ada anggota terkait
