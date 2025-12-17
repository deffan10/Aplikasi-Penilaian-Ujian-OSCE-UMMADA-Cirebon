# Changelog - 17 Desember 2025

## Ringkasan Update
Update ini mencakup fitur validasi waktu untuk penguji, perbaikan UI/UX, dan berbagai bug fixes.

---

## 🆕 Fitur Baru

### 1. Validasi Waktu Penilaian untuk Penguji
Penguji tidak bisa input nilai sebelum atau sesudah waktu yang dijadwalkan.

**File yang diubah:**
- `app/Models/Gelombang.php` - Menambahkan method validasi waktu
- `app/Http/Controllers/Penguji/PenilaianController.php` - Menambahkan pengecekan waktu

**Method baru di Gelombang Model:**
```php
hasStarted()      // Cek apakah waktu sudah mulai
hasEnded()        // Cek apakah waktu sudah selesai  
isActive()        // Cek apakah sedang dalam waktu aktif
canInputNilai()   // Return: hasStarted() && !hasEnded()
getStatusWaktu()  // Return array dengan status, label, color, message
```

**Behavior:**
- Jika waktu belum mulai: Tampil "Menunggu Waktu" (kuning), tombol disabled
- Jika waktu sudah selesai: Tampil "Waktu Selesai" (merah), tombol disabled
- Jika sedang aktif: Tombol enabled, penguji bisa input nilai

---

### 2. Menu Kelola Gelombang di Navigation
Menu Jadwal sekarang menjadi dropdown dengan sub-menu.

**File yang diubah:**
- `resources/views/layouts/navigation.blade.php`
- `routes/web.php` - Menambahkan route `admin.gelombang.index`
- `app/Http/Controllers/Admin/GelombangController.php` - Menambahkan method `list()`
- `resources/views/admin/gelombang/list.blade.php` (file baru)

**Struktur Menu:**
```
Jadwal (Dropdown)
├── Jadwal Ujian
└── Kelola Gelombang
```

---

### 3. Greeting Dinamis di Dashboard Penguji
Greeting berubah sesuai waktu hari.

**File:** `resources/views/penguji/dashboard.blade.php`

**Logika:**
- 🌅 Selamat Pagi (05:00 - 10:59)
- ☀️ Selamat Siang (11:00 - 14:59)
- 🌇 Selamat Sore (15:00 - 17:59)
- 🌙 Selamat Malam (18:00 - 04:59)

---

### 4. Jam Server di Dashboard Penguji
Menampilkan jam realtime dan tanggal di welcome card dashboard penguji.

---

### 5. Jam Server di Mobile View (Luar Hamburger)
Jam server sekarang tampil di sebelah hamburger button, tidak perlu buka menu.

**File:** `resources/views/layouts/navigation.blade.php`

---

## 🐛 Bug Fixes

### 1. Fix Error `is_archived` Column Not Found
**Masalah:** Query menggunakan `is_archived` padahal kolom sebenarnya `is_arsip`  
**Solusi:** Mengubah `is_archived` → `is_arsip` di `GelombangController.php`

---

### 2. Fix Progress Count Bug (2/1 Issue)
**Masalah:** Progress menunjukkan nilai yang salah (misal 2/1) ketika mahasiswa dipindah gelombang  
**Solusi:** Menambahkan filter `whereIn('mahasiswa_id', $mahasiswaIds)` untuk hanya menghitung nilai mahasiswa yang masih aktif di gelombang

**File:** `app/Http/Controllers/Penguji/PenilaianController.php`
- Method `index()` - progress di dashboard
- Method `selectStasi()` - progress per stasi

---

### 3. Fix Rekap Per Jadwal Empty Table
**Masalah:** Tabel rekap kosong karena mengambil data dari `jadwal_mahasiswa` yang tidak digunakan  
**Solusi:** Mengubah query untuk mengambil peserta dari `gelombang_mahasiswa`

**File yang diubah:**
- `app/Http/Controllers/Admin/RekapController.php` - Method `perJadwal()` dan `exportPdfJadwal()`
- `app/Exports/RekapPerJadwalExport.php`

---

### 4. Fix Mobile Navigation Missing Menu Items
**Masalah:** Menu penguji tidak lengkap di mobile view  
**Solusi:** Menambahkan menu yang hilang (Penilaian, Daftar Stasi)

---

### 5. Fix Login Page Not Centered on Mobile
**Masalah:** Form login tidak di tengah pada mobile  
**Solusi:** Mengubah `sm:justify-center` → `justify-center`

**File:** `resources/views/layouts/guest.blade.php`

---

## 🎨 UI/UX Improvements

### 1. Navigation Bar Spacing & Font
- Menambah spacing antar menu: `space-x-6` → `space-x-4` dengan `px-3`
- Font weight: `font-medium` → `font-semibold`

**File:** `resources/views/components/nav-link.blade.php`

---

### 2. Time Status UI di Halaman Penguji
Menampilkan status waktu dengan warna yang jelas:
- **Kuning** (#EAB308): Menunggu Waktu
- **Hijau**: Waktu Aktif  
- **Merah** (#DC2626): Waktu Selesai

**File yang diubah:**
- `resources/views/penguji/penilaian/index.blade.php`
- `resources/views/penguji/penilaian/select-stasi.blade.php`
- `resources/views/penguji/penilaian/list.blade.php`

---

### 3. Dashboard Penguji Welcome Card
Redesign welcome card dengan:
- Background putih + border kiri indigo
- Teks yang mudah dibaca (gray-800)
- Jam server berwarna indigo

---

## 📁 File Baru yang Dibuat

1. `resources/views/admin/gelombang/list.blade.php` - Halaman daftar jadwal untuk kelola gelombang

---

## 📝 Catatan Deployment

### Database
Tidak ada migration baru. Pastikan kolom berikut ada:
- Tabel `jadwal`: kolom `is_arsip` (boolean)
- Tabel `gelombang`: kolom `waktu_mulai`, `waktu_selesai`

### Clear Cache
Setelah deploy, jalankan:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Permissions
Pastikan folder berikut writable:
- `storage/`
- `bootstrap/cache/`

---

## 🔧 Konfigurasi

### Logo & Favicon
- **Logo Login:** `resources/views/components/application-logo.blade.php`
- **Favicon:** `public/favicon.ico`

Untuk mengubah logo, edit file `application-logo.blade.php`:
```php
{{-- Ganti SVG dengan gambar --}}
<img src="{{ asset('images/logo.png') }}" {{ $attributes }}>
```

---

## Testing Checklist

### Penguji
- [ ] Login sebagai penguji
- [ ] Cek greeting dinamis sesuai waktu
- [ ] Cek jam server di dashboard
- [ ] Cek jam server di mobile (di luar hamburger)
- [ ] Cek validasi waktu - tidak bisa input sebelum waktu mulai
- [ ] Cek validasi waktu - tidak bisa input setelah waktu selesai
- [ ] Cek progress count setelah mahasiswa dipindah gelombang

### Admin
- [ ] Cek menu dropdown Jadwal (Jadwal Ujian + Kelola Gelombang)
- [ ] Cek halaman Kelola Gelombang tampil dengan benar
- [ ] Cek rekap per jadwal menampilkan data dengan benar
- [ ] Cek export PDF rekap
- [ ] Cek export Excel rekap

### Mobile
- [ ] Cek login page centered
- [ ] Cek jam server visible tanpa buka menu
- [ ] Cek semua menu penguji lengkap

---

*Dokumentasi dibuat: 17 Desember 2025*
