# 🔐 Halaman Login

## Layout
- **Tipe:** Full-page centered, tanpa sidebar/navbar
- **Background:** Gradient dari `#1E3A5F` ke `#4A90D9`

## Wireframe

```
┌──────────────────────────────────────────────┐
│          (Background Gradient Biru)          │
│                                              │
│       ┌─────────────────────────────┐        │
│       │     🏭 Logo PT Primatex     │        │
│       │   Sistem Potongan Gaji      │        │
│       │                             │        │
│       │   ┌─────────────────────┐   │        │
│       │   │ 👤 Username         │   │        │
│       │   └─────────────────────┘   │        │
│       │                             │        │
│       │   ┌─────────────────────┐   │        │
│       │   │ 🔒 Password         │   │        │
│       │   └─────────────────────┘   │        │
│       │                             │        │
│       │   [████ MASUK ████████]     │        │
│       │                             │        │
│       └─────────────────────────────┘        │
│                                              │
│       © 2026 PT Primatex Indonesia           │
└──────────────────────────────────────────────┘
```

## Komponen
- **Card login:** background putih, shadow-lg, border-radius 12px, max-width 420px
- **Logo/Ikon:** Di atas form, center
- **Input Username:** icon user di kiri, placeholder "Masukkan username"
- **Input Password:** icon lock di kiri, placeholder "Masukkan password", toggle show/hide
- **Tombol Masuk:** full-width, warna Primary, rounded
- **Error message:** alert merah di atas form jika gagal login
- **Footer:** teks copyright di bawah card, warna putih semi-transparan

## Responsif
- Card tetap centered di semua ukuran layar
- Padding card mengecil di mobile
