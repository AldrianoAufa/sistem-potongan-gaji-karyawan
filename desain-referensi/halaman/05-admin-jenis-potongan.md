# 📋 Admin — Kelola Jenis Potongan

## Layout
- Sidebar kiri + konten utama

## Wireframe

```
┌──────┬──────────────────────────────────────┐
│ SIDE │  Kelola Jenis Potongan               │
│ BAR  │──────────────────────────────────────│
│      │  [+ Tambah Jenis Potongan]           │
│      │                                      │
│      │  ┌────┬──────┬──────────────┬──────┐ │
│      │  │No  │Kode  │Nama Potongan │Aksi  │ │
│      │  ├────┼──────┼──────────────┼──────┤ │
│      │  │1   │KOPER │Koperasi      │✏️ 🗑️ │ │
│      │  │2   │PINJ.P│Pinj. Panjang │✏️ 🗑️ │ │
│      │  │3   │PINJ.D│Pinj. Pendek  │✏️ 🗑️ │ │
│      │  │4   │BPJS  │BPJS Kes.     │✏️ 🗑️ │ │
│      │  └────┴──────┴──────────────┴──────┘ │
│      │                                      │
│      │  ◀ 1 ▶                               │
└──────┴──────────────────────────────────────┘
```

## Form Tambah/Edit (Modal)

```
┌─────────────────────────────────┐
│  ✕  Tambah Jenis Potongan       │
│─────────────────────────────────│
│                                 │
│  Kode Potongan *                │
│  ┌─────────────────────────┐    │
│  │ KODE                    │    │
│  └─────────────────────────┘    │
│                                 │
│  Nama Potongan *                │
│  ┌─────────────────────────┐    │
│  │ Nama jenis potongan     │    │
│  └─────────────────────────┘    │
│                                 │
│  [Batal]          [💾 Simpan]   │
└─────────────────────────────────┘
```

## Komponen
- 2 field: kode_potongan (unique), nama_potongan
- CRUD via modal
- Validasi: keduanya required, kode_potongan unique
- Hapus: konfirmasi, cek apakah ada input_bulanan terkait
