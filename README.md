# Aplikasi Ujian OSCE - Farmasi

Aplikasi web untuk mengelola ujian OSCE (Objective Structured Clinical Examination) Program Studi Farmasi.

## 🌟 Fitur Utama

### Role Admin
- ✅ Kelola Stasi (Station) dan Komponen Penilaian
- ✅ Kelola Kelas dan Mahasiswa (dengan import Excel)
- ✅ Kelola Penguji dan Penugasan Stasi
- ✅ Kelola Jadwal Ujian
- ✅ **Kelola Gelombang** - Pembagian sesi ujian dengan validasi waktu
- ✅ Rekap Nilai per Jadwal dan per Gelombang
- ✅ Export PDF (dengan kop surat customizable) dan Excel
- ✅ Pengaturan Sistem (logo, institusi, alamat, koordinator)
- ✅ Arsip Jadwal dan Kelas

### Role Penguji
- ✅ Dashboard dengan salam dinamis (Pagi/Siang/Sore/Malam)
- ✅ Jam server realtime
- ✅ Input nilai per komponen dengan bobot (skala 0-4)
- ✅ Global Rating (Fail/Borderline/Pass/Honours)
- ✅ **Validasi Waktu** - Penilaian hanya bisa dilakukan dalam waktu gelombang
- ✅ Catatan penilaian
- ✅ Responsive design untuk mobile

## 💻 Teknologi

- **Backend:** Laravel 10, PHP 8.1+
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Database:** MySQL/MariaDB
- **Export:** barryvdh/laravel-dompdf, maatwebsite/excel

## 📦 Instalasi

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/MariaDB

### Steps

1. Clone/download project

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy `.env.example` ke `.env` dan sesuaikan konfigurasi database:
```env
DB_DATABASE=aplikasi_ujian
DB_USERNAME=root
DB_PASSWORD=
```

4. Generate key:
```bash
php artisan key:generate
```

5. Jalankan migration dan seeding:
```bash
php artisan migrate --seed
```

6. Buat storage link:
```bash
php artisan storage:link
```

7. Build assets:
```bash
npm run build
```

8. Jalankan server:
```bash
php artisan serve
```

## 🔐 Akun Default

| Role | Username/Email | Password |
|------|----------------|----------|
| Admin | admin / admin@osce.test | password |
| Penguji | penguji1 / penguji1@osce.test | password |

> 💡 Login bisa menggunakan **username** atau **email**

## 📊 Struktur Database

### Tabel Utama
| Tabel | Deskripsi |
|-------|-----------|
| `users` | Pengguna (admin/penguji) |
| `kelas` | Data kelas mahasiswa |
| `mahasiswa` | Data mahasiswa |
| `stasi` | Station ujian OSCE |
| `komponen_stasi` | Komponen penilaian (dengan bobot) |
| `jadwal` | Jadwal ujian |
| `gelombang` | Gelombang/sesi dalam jadwal |
| `gelombang_mahasiswa` | Peserta per gelombang |
| `gelombang_penguji` | Penguji per stasi per gelombang |
| `nilai` | Nilai mahasiswa |
| `nilai_detail` | Detail nilai per komponen |
| `global_ratings` | Skala global rating |
| `settings` | Pengaturan sistem |
| `log_penilaian` | Audit trail perubahan nilai |

📖 Dokumentasi lengkap: [docs/DATABASE-STRUKTUR.md](docs/DATABASE-STRUKTUR.md)

## 📝 Alur Penggunaan

### Alur Admin
1. **Setup Data Master**
   - Buat Kelas dan import Mahasiswa
   - Buat Stasi dan Komponen Penilaian
   - Tambah Penguji

2. **Setup Jadwal Ujian**
   - Buat Jadwal baru
   - Buat Gelombang (dengan waktu mulai & selesai)
   - Tambahkan Peserta ke Gelombang
   - Tugaskan Penguji per Stasi per Gelombang

3. **Monitoring**
   - Lihat progress penilaian
   - Export rekap nilai ke Excel/PDF

### Alur Penguji
1. Login ke sistem
2. Pilih Jadwal aktif
3. Pilih Stasi yang ditugaskan
4. Pilih Mahasiswa untuk dinilai
5. Input nilai komponen (0-4)
6. Pilih Global Rating
7. Tentukan kelulusan stasi
8. Simpan

> ⚠️ **Penting:** Penilaian hanya bisa dilakukan saat waktu server berada dalam rentang waktu gelombang aktif.

## 📱 Akses Mobile

Aplikasi mendukung akses dari smartphone:
- Responsive design
- Jam server terlihat di navbar
- Menu hamburger untuk navigasi

## 📁 Format Import Excel

### Mahasiswa
| nim | nama |
|-----|------|
| 2024001 | Ahmad Rizki |
| 2024002 | Budi Santoso |

## 📚 Dokumentasi

| Dokumen | Deskripsi |
|---------|-----------|
| [PANDUAN-PENGGUNA.md](docs/PANDUAN-PENGGUNA.md) | Tutorial lengkap untuk Admin & Penguji |
| [DATABASE-STRUKTUR.md](docs/DATABASE-STRUKTUR.md) | Dokumentasi struktur database |
| [database-schema.sql](docs/database-schema.sql) | SQL dump struktur database |
| [CHANGELOG-2025-12-17.md](docs/CHANGELOG-2025-12-17.md) | Log perubahan terbaru |

## 🔧 Maintenance

### Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Optimize untuk Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📄 License

MIT

## 👨‍💻 Author

Developed for Pharmacy OSCE Assessment

---

*Last updated: December 17, 2025*
