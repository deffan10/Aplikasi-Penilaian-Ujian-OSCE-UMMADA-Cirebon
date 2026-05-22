# Aplikasi Ujian OSCE - Farmasi

Aplikasi web untuk mengelola ujian OSCE (Objective Structured Clinical Examination) Program Studi Farmasi.

## 🌟 Fitur Utama

### Role Admin
- ✅ Kelola Stasi (Station) dan Komponen Penilaian
- ✅ Kelola Kelas dan Mahasiswa (dengan import Excel)
- ✅ Kelola Penguji dan Penugasan Stasi (dengan import CSV)
- ✅ Kelola Jadwal Ujian
- ✅ **Kelola Gelombang** - Pembagian sesi ujian dengan validasi waktu
- ✅ Rekap Nilai per Jadwal dan per Gelombang
- ✅ Export PDF (dengan kop surat customizable) dan Excel
- ✅ Pengaturan Sistem (logo, institusi, alamat, koordinator)
- ✅ Arsip Jadwal dan Kelas
- ✅ **Print Label Penguji** - Layout A4, 10 label/halaman, filter per jadwal
- ✅ **Print Kartu Peserta** - Layout A4 portrait, 4 kartu/halaman (90x130mm), filter per kelas & jadwal
- ✅ **Upload Foto Mahasiswa** - Bulk via ZIP (auto-match NIM) + individual, auto compress
- ✅ **Pengaturan Kop Label & Kartu** - Header editable, logo kiri/kanan terpisah
- ✅ **Opsi Cetak Fleksibel** - Checkbox opsional untuk field yang ditampilkan saat cetak

### Role Penguji
- ✅ Dashboard dengan salam dinamis (Pagi/Siang/Sore/Malam)
- ✅ Jam server realtime
- ✅ Input nilai per komponen dengan bobot (skala 0-4)
- ✅ Global Rating (Fail/Borderline/Pass/Honours)
- ✅ **Validasi Waktu** - Penilaian hanya bisa dilakukan dalam waktu gelombang
- ✅ Catatan penilaian
- ✅ Responsive design untuk mobile

## 🖨️ Fitur Cetak

### Print Label Penguji
- Akses dari `/admin/penguji` → tombol "Print Label"
- Modal pilih jadwal + checkbox field opsional (Stasi, Gelombang, Waktu)
- Layout: A4 portrait, 2 kolom × 5 baris = **10 label per halaman**
- Data: Nama, Username, Password, Stasi, Gelombang, Tanggal+Jam
- Header/kop editable dari Pengaturan

### Print Kartu Peserta
- Akses dari `/admin/kelas` → tombol "Cetak Kartu" per kelas
- Modal pilih jadwal + checkbox field opsional (Nama Jadwal, Gelombang, Waktu)
- Layout: A4 portrait, 2 kolom × 2 baris = **4 kartu per halaman** (90mm × 130mm)
- Data: Foto, Nama (26px bold), NIM, Kelas, Jadwal, Gelombang, Waktu + WIB
- Kop editable dengan logo kiri dan kanan terpisah
- Outline tiap kartu untuk garis potong

### Upload Foto Mahasiswa
- Akses dari `/admin/mahasiswa` → tombol "Upload Foto"
- **Bulk upload** via file ZIP (nama file = NIM, contoh: `2201001.jpg`)
- **Individual upload** di form Edit Mahasiswa
- Auto compress: resize max 400px width + JPEG quality 70%
- Format: JPG, JPEG, PNG

## 💻 Teknologi

- **Backend:** Laravel 10, PHP 8.1+
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Database:** MySQL/MariaDB
- **Export:** barryvdh/laravel-dompdf, maatwebsite/excel

## 📦 Instalasi

### Prerequisites
- PHP 8.1+ (dengan ext-gd untuk kompresi foto)
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
| `users` | Pengguna (admin/penguji) dengan field `plain_password` untuk cetak label |
| `kelas` | Data kelas mahasiswa |
| `mahasiswa` | Data mahasiswa dengan field `foto` |
| `stasi` | Station ujian OSCE |
| `komponen_stasi` | Komponen penilaian (dengan bobot) |
| `jadwal` | Jadwal ujian |
| `gelombang` | Gelombang/sesi dalam jadwal |
| `gelombang_mahasiswa` | Peserta per gelombang |
| `gelombang_penguji` | Penguji per stasi per gelombang |
| `nilai` | Nilai mahasiswa |
| `nilai_detail` | Detail nilai per komponen |
| `global_ratings` | Skala global rating |
| `settings` | Pengaturan sistem (kop, logo, penandatangan, dll) |
| `log_penilaian` | Audit trail perubahan nilai |

## 📝 Alur Penggunaan

### Alur Admin
1. **Setup Data Master**
   - Buat Kelas dan import Mahasiswa
   - Upload foto mahasiswa (ZIP atau individual)
   - Buat Stasi dan Komponen Penilaian
   - Tambah Penguji (manual atau import CSV)

2. **Setup Jadwal Ujian**
   - Buat Jadwal baru
   - Buat Gelombang (nama, urutan, waktu)
   - Tambahkan Peserta ke Gelombang
   - Tugaskan Penguji per Stasi per Gelombang

3. **Cetak Label & Kartu**
   - Print Label Penguji (username + password + stasi)
   - Print Kartu Peserta per Kelas (dengan foto)

4. **Monitoring**
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

## ⚙️ Pengaturan Admin

Akses dari `/admin/settings`:

| Setting | Deskripsi |
|---------|-----------|
| Kop Surat | Gambar kop untuk dokumen PDF |
| Header Label Print | 3 baris teks + logo untuk label penguji |
| Kop Kartu Peserta | 3 baris teks + logo kiri & kanan (terpisah) |
| Penandatangan | Jabatan, nama, NIP untuk dokumen resmi |

## 📱 Akses Mobile

Aplikasi mendukung akses dari smartphone:
- Responsive design
- Jam server terlihat di navbar
- Menu hamburger untuk navigasi

## 📁 Format Import

### Mahasiswa (Excel)
| nim | nama |
|-----|------|
| 2024001 | Ahmad Rizki |
| 2024002 | Budi Santoso |

### Penguji (CSV)
| Nama | Username | Password |
|------|----------|----------|
| Dr. Budi Santoso | budi.santoso | password123 |

### Foto Mahasiswa (ZIP)
- Nama file = NIM (contoh: `2024001.jpg`, `2024002.png`)
- Format: JPG/PNG
- Auto compress saat upload

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

### Fix Permission Storage
```bash
sudo chmod -R 775 storage
sudo chown -R www-data:www-data storage
```

## 📄 License

MIT

## 👨‍💻 Author

Developed for Pharmacy OSCE Assessment - UMMADA Cirebon

---

*Last updated: May 22, 2026*
