# 📊 Admin — Dashboard

## Layout
- **Tipe:** Sidebar kiri + konten utama
- **Navbar:** fixed top, nama admin + dropdown logout
- **Sidebar:** Menu navigasi admin (lihat panduan-umum.md)

## Wireframe

```
┌──────┬──────────────────────────────────────┐
│      │  [Navbar: Logo | Sistem Potongan]  👤│
│ SIDE │──────────────────────────────────────│
│ BAR  │                                      │
│      │  Selamat Datang, Admin!               │
│ 📊   │                                      │
│ 👥   │  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐│
│ 🏢   │  │ 👥   │ │ 💰   │ │ 📋   │ │ 🏢   ││
│ 📋   │  │Total │ │Total │ │Jenis │ │Jaba- ││
│ 💰   │  │Anggo-│ │Potong│ │Potong│ │tan   ││
│ 📤   │  │ta    │ │Bulan │ │an    │ │      ││
│ 📑   │  │ 1250 │ │ 45jt │ │  12  │ │  8   ││
│      │  └──────┘ └──────┘ └──────┘ └──────┘│
│      │                                      │
│      │  ┌───────────────────────────────────┐│
│      │  │  📊 Grafik Potongan 6 Bulan       ││
│      │  │  Terakhir (Bar Chart)             ││
│      │  │                                   ││
│      │  │  ████                             ││
│      │  │  ████ ████                        ││
│      │  │  ████ ████ ████ ████ ████ ████    ││
│      │  │  Sep  Okt  Nov  Des  Jan  Feb     ││
│      │  └───────────────────────────────────┘│
│      │                                      │
│      │  ┌───────────────────────────────────┐│
│      │  │  📋 Potongan Terbaru              ││
│      │  │  ┌────┬──────┬──────┬──────────┐  ││
│      │  │  │No  │Nama  │Jenis │Jumlah    │  ││
│      │  │  ├────┼──────┼──────┼──────────┤  ││
│      │  │  │1   │Ahmad │KOPER │500.000   │  ││
│      │  │  │2   │Budi  │PINJ  │1.200.000 │  ││
│      │  │  └────┴──────┴──────┴──────────┘  ││
│      │  └───────────────────────────────────┘│
└──────┴──────────────────────────────────────┘
```

## Komponen

### Card Statistik (4 buah, horizontal)
| Card | Ikon | Label | Warna |
|---|---|---|---|
| 1 | 👥 | Total Anggota | Primary |
| 2 | 💰 | Total Potongan Bulan Ini | Success |
| 3 | 📋 | Jenis Potongan | Info |
| 4 | 🏢 | Total Jabatan | Warning |

### Grafik Potongan
- Bar chart 6 bulan terakhir
- Library: Chart.js (sederhana, via CDN)
- Sumbu X: Bulan, Sumbu Y: Total rupiah

### Tabel Potongan Terbaru
- 5-10 data terbaru
- Kolom: No, Nama Anggota, Jenis Potongan, Jumlah, Bulan/Tahun
- Link "Lihat Semua" ke halaman laporan

## Responsif
- Card statistik: 4 kolom → 2 kolom (tablet) → 1 kolom (mobile)
- Grafik: full-width, height menyesuaikan
- Tabel: horizontal scroll di mobile
