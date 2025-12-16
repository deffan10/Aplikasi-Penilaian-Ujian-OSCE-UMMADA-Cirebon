# Panduan Deployment ke Production

## Aplikasi Penilaian Ujian OSCE - UMMADA Cirebon
**Versi:** 1.0.0  
**Tanggal:** 16 Desember 2025

---

## 📋 Ringkasan Perubahan (BAB VII - Penetapan Kelulusan)

### Sistem Penilaian Baru:
1. **Skor Komponen:** 0-3 (bukan 0-100)
   - 0 = Tidak Dikerjakan
   - 1 = Perlu Perbaikan
   - 2 = Sesuai Standar
   - 3 = Sempurna

2. **Global Rating:** 4 tingkat (1-4)
   - 1 = Tidak Lulus
   - 2 = Borderline (KKM)
   - 3 = Lulus
   - 4 = Superior

3. **Formula Nilai Aktual:**
   ```
   Nilai Aktual = Σ(skor × bobot)
   ```
   - Nilai tidak dinormalisasi ke 100
   - Contoh: 5 komponen dengan bobot 15,20,30,20,15 dan skor 3 semua = 3×100 = 300

4. **Formula Nilai Acuan (Regression):**
   - Menggunakan metode Borderline Group (Global Rating = 2)
   - Dihitung menggunakan Linear Regression dari data penilaian

---

## 🚀 Langkah-langkah Deployment

### 1. Persiapan Server Production

```bash
# Requirements
PHP >= 8.1
MySQL >= 5.7 atau MariaDB >= 10.3
Composer
Node.js >= 16 (untuk build assets)
```

### 2. Upload Source Code

```bash
# Clone atau upload source code ke server
git clone [repository-url] /path/to/aplikasi-osce
cd /path/to/aplikasi-osce
```

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies dan build assets
npm install
npm run build
```

### 4. Konfigurasi Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit file `.env`:
```env
APP_NAME="Aplikasi Ujian OSCE"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aplikasi_ujian
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 5. Setup Database

#### Opsi A: Menggunakan Backup SQL (Rekomendasi jika ada data existing)

```bash
# Import backup SQL
mysql -u root -p aplikasi_ujian < database/backup/backup_2025_12_16.sql
```

#### Opsi B: Fresh Install (Database baru)

```bash
# Jalankan migration
php artisan migrate --force

# Seed data master
php artisan db:seed --class=ProductionSeeder --force
```

### 6. Optimasi untuk Production

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 7. Setup Web Server

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/aplikasi-osce/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📁 File Backup

| File | Deskripsi |
|------|-----------|
| `database/backup/backup_2025_12_16.sql` | Full database backup termasuk struktur dan data |
| `database/seeders/ProductionSeeder.php` | Seeder untuk fresh install |
| `database/seeders/GlobalRatingSeeder.php` | Seeder Global Rating 4 tingkat |

---

## 🔐 Akses Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@osce.test | password |

> ⚠️ **PENTING:** Segera ubah password setelah login pertama!

---

## 📊 Struktur Database Utama

### Tabel Baru/Dimodifikasi:

1. **global_ratings** - Data Global Rating 4 tingkat
2. **nilai** - Tambah kolom `catatan`
3. **kelas** - Tambah kolom `is_arsip`, `diarsipkan_pada`
4. **jadwal** - Tambah kolom `is_arsip`, `diarsipkan_pada`

### Migration yang perlu dijalankan:
```
2025_12_16_064555_add_arsip_columns_to_kelas_and_jadwal_tables.php
2025_12_16_150000_update_osce_scoring_system.php
2025_12_16_160000_add_catatan_to_nilai_table.php
```

---

## 🛠️ Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Error: View tidak terupdate
```bash
php artisan view:clear
php artisan cache:clear
```

### Error: Permission denied
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 📞 Kontak Support

Jika ada masalah saat deployment, hubungi:
- Developer: [Contact Info]
- Repository: [GitHub URL]

---

*Dokumentasi ini dibuat pada 16 Desember 2025*
