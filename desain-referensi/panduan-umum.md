# 🎨 Panduan Umum Desain

## Brand & Identitas
- **Nama Aplikasi:** Sistem Potongan Gaji — PT Primatex Indonesia
- **Tagline:** "Kelola Potongan Gaji dengan Mudah dan Transparan"

## Palet Warna

| Peran | Warna | Hex Code |
|---|---|---|
| Primary | Biru Tua | `#1E3A5F` |
| Primary Light | Biru Muda | `#4A90D9` |
| Secondary | Abu-abu | `#6C757D` |
| Accent | Hijau | `#28A745` |
| Danger | Merah | `#DC3545` |
| Warning | Kuning | `#FFC107` |
| Background | Putih/Abu Terang | `#F8F9FA` |
| Sidebar | Biru Gelap | `#1A2332` |
| Text Primary | Hitam | `#212529` |
| Text Muted | Abu | `#6C757D` |

## Tipografi
- **Font Utama:** Inter (Google Fonts)
- **Heading:** Bold, ukuran 24-32px
- **Sub-heading:** Semi-bold, 18-20px
- **Body text:** Regular, 14-16px
- **Small/Caption:** 12px

## Framework CSS
- **Bootstrap 5** — gunakan komponen standar Bootstrap
- Semua halaman harus **responsif** (desktop, tablet, mobile)

## Komponen Umum

### Navbar
- Posisi: fixed top
- Background: Primary (`#1E3A5F`)
- Logo + Nama Aplikasi di kiri
- Nama User + Dropdown (Profil, Logout) di kanan

### Sidebar (Admin)
- Posisi: fixed left, collapsible di mobile
- Background: Sidebar (`#1A2332`)  
- Item menu dengan ikon (gunakan Bootstrap Icons)
- Menu items:
  - 📊 Dashboard
  - 👥 karyawan
  - 🏢 Jabatan
  - 📋 Jenis Potongan
  - 💰 Input Bulanan
  - 📤 Import Excel
  - 📑 Laporan

### Card Statistik (Dashboard)
- Shadow ringan, border-radius 8px
- Ikon besar di kiri, angka besar + label di kanan
- Warna berbeda per card (Primary, Success, Warning, Info)

### Tabel Data
- Striped rows
- Hover effect
- Pagination di bawah
- Search box di atas kanan
- Tombol aksi: Edit (warning), Hapus (danger), Detail (info)

### Form
- Label di atas input
- Validasi visual (border merah + pesan error di bawah input)
- Tombol submit: Primary, full-width di mobile

### Modal
- Untuk konfirmasi hapus data
- Center screen, backdrop gelap

### Alert/Notifikasi
- Success: hijau, dengan ikon ✓
- Error: merah, dengan ikon ✕
- Warning: kuning
- Auto-dismiss setelah 5 detik

## Ikon
- Gunakan **Bootstrap Icons** (https://icons.getbootstrap.com/)
- Ukuran: 20-24px untuk menu, 40-48px untuk card statistik

## Responsive Breakpoints
| Ukuran | Breakpoint |
|---|---|
| Mobile | < 768px |
| Tablet | 768px - 1024px |
| Desktop | > 1024px |

- Sidebar collapse di mobile → hamburger menu
- Tabel horizontal scroll di mobile
- Card statistik stack vertical di mobile
