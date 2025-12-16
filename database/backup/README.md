# Database Backup Files

Folder ini berisi file-file backup database untuk deployment ke production.

## 📁 Daftar File

| File | Ukuran | Deskripsi |
|------|--------|-----------|
| `backup_2025_12_16.sql` | ~70 KB | Full database dump (struktur + data) |
| `master_data_seed.sql` | ~5 KB | Data master minimal untuk fresh install |
| `DEPLOYMENT_GUIDE.md` | ~5 KB | Panduan lengkap deployment |

## 🚀 Cara Penggunaan

### Opsi 1: Import Full Backup (Ada Data Existing)

```bash
mysql -u username -p database_name < backup_2025_12_16.sql
```

### Opsi 2: Fresh Install (Database Kosong)

```bash
# Jalankan migration
php artisan migrate --force

# Import data master
mysql -u username -p database_name < master_data_seed.sql

# ATAU gunakan seeder
php artisan db:seed --class=ProductionSeeder --force
```

## ⚠️ Catatan Penting

1. Backup ini dibuat tanggal **16 Desember 2025**
2. Password default admin: `admin@osce.test` / `password`
3. Segera ubah password setelah deployment!
4. Pastikan PHP >= 8.1 dan MySQL/MariaDB terpasang

## 📊 Perubahan Database (BAB VII)

- **global_ratings**: 4 tingkat (1=Tidak Lulus, 2=Borderline, 3=Lulus, 4=Superior)
- **nilai**: Skor komponen 0-3, tambah kolom `catatan`
- Formula: `Nilai Aktual = Σ(skor × bobot)`
- Nilai Acuan dihitung via Linear Regression
