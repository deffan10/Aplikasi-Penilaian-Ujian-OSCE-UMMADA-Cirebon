# Aplikasi Ujian OSCE - Farmasi

Aplikasi web untuk mengelola ujian OSCE (Objective Structured Clinical Examination) Program Studi Farmasi.

## Fitur

### Role Admin
- ✅ Kelola Stasi (Station) dan Komponen Penilaian
- ✅ Kelola Kelas dan Mahasiswa (dengan import Excel)
- ✅ Kelola Penguji dan Penugasan Stasi
- ✅ Kelola Jadwal Ujian
- ✅ Rekap Nilai per Jadwal dan per Kelas
- ✅ Export PDF (dengan kop surat customizable) dan Excel
- ✅ Pengaturan Kop Surat (logo, institusi, alamat, koordinator)

### Role Penguji
- ✅ Dashboard dengan daftar stasi yang ditugaskan
- ✅ Input nilai per komponen dengan bobot
- ✅ Global Rating (Lulus/Borderline/Tidak Lulus)
- ✅ Catatan penilaian

## Teknologi

- Laravel 10
- Laravel Breeze (Auth + Blade)
- Tailwind CSS
- MySQL
- barryvdh/laravel-dompdf (Export PDF)
- maatwebsite/excel (Import/Export Excel)

## Instalasi

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL

### Steps

1. Clone/download project

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy `.env.example` ke `.env` dan sesuaikan konfigurasi database:
```
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
php artisan migrate:fresh --seed
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

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@osce.test | password |
| Penguji 1 | penguji1@osce.test | password |
| Penguji 2 | penguji2@osce.test | password |

## Struktur Database

### Tabel Utama
- `users` - User dengan role (admin/penguji)
- `kelas` - Data kelas mahasiswa
- `mahasiswa` - Data mahasiswa
- `stasi` - Station ujian OSCE
- `komponen_stasi` - Komponen penilaian tiap stasi (dengan bobot)
- `penguji_stasi` - Penugasan penguji ke stasi
- `jadwal` - Jadwal ujian
- `jadwal_mahasiswa` - Peserta tiap jadwal
- `global_ratings` - Pilihan global rating (Lulus/Borderline/Tidak Lulus)
- `nilai` - Nilai mahasiswa per stasi per jadwal
- `nilai_detail` - Detail nilai per komponen
- `settings` - Pengaturan kop surat
- `log_penilaian` - Log perubahan nilai

## Perhitungan Nilai

Nilai total dihitung berdasarkan bobot komponen:
```
Nilai Total = Σ (Nilai Komponen × Bobot / 100)
```

Kriteria Kelulusan:
- Rata-rata nilai >= 70
- Tidak ada Global Rating "TIDAK LULUS" di semua stasi

## Penggunaan

### Alur Admin
1. Login sebagai Admin
2. Setup data master: Kelas, Mahasiswa, Stasi, Komponen
3. Tambah Penguji dan tugaskan ke Stasi
4. Buat Jadwal Ujian dan aktifkan
5. Monitor penilaian dan lihat rekap
6. Export ke PDF/Excel

### Alur Penguji
1. Login sebagai Penguji
2. Lihat stasi yang ditugaskan
3. Pilih jadwal aktif
4. Input nilai per mahasiswa
5. Pilih Global Rating
6. Simpan

## Format Import Excel Mahasiswa

| nim | nama |
|-----|------|
| 12345678 | Nama Mahasiswa |

## License

MIT

## Author

Developed for Pharmacy OSCE Assessment
