# 📊 Dokumentasi Database - Aplikasi Ujian OSCE

Dokumen ini berisi penjelasan struktur database aplikasi.

---

## 📑 Daftar Tabel

| No | Nama Tabel | Deskripsi |
|----|------------|-----------|
| 1 | users | Data pengguna (admin & penguji) |
| 2 | kelas | Data kelas mahasiswa |
| 3 | mahasiswa | Data mahasiswa |
| 4 | stasi | Data stasi/station ujian OSCE |
| 5 | komponen_stasi | Komponen penilaian per stasi |
| 6 | global_ratings | Skala global rating |
| 7 | jadwal | Jadwal ujian OSCE |
| 8 | gelombang | Gelombang/sesi dalam jadwal |
| 9 | gelombang_mahasiswa | Peserta per gelombang |
| 10 | gelombang_penguji | Penguji per gelombang per stasi |
| 11 | penguji_stasi | Mapping penguji ke stasi |
| 12 | nilai | Nilai mahasiswa per stasi |
| 13 | nilai_detail | Detail nilai per komponen |
| 14 | nilai_acuan_stasi | Nilai acuan borderline |
| 15 | log_penilaian | Log perubahan nilai |
| 16 | settings | Pengaturan sistem |

---

## 🔗 Entity Relationship Diagram (Text)

```
┌─────────────┐     ┌─────────────┐     ┌─────────────────┐
│   kelas     │────<│  mahasiswa  │────<│gelombang_mhs    │
└─────────────┘     └─────────────┘     └─────────────────┘
                           │                    │
                           │                    │
                           ▼                    ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────────┐
│   users     │────<│   nilai     │>────│   gelombang     │
│(admin/penguji)    └─────────────┘     └─────────────────┘
└─────────────┘            │                    │
      │                    │                    │
      │                    ▼                    ▼
      │            ┌─────────────┐     ┌─────────────────┐
      └───────────<│ nilai_detail│     │    jadwal       │
                   └─────────────┘     └─────────────────┘
                          │                    │
                          ▼                    ▼
                   ┌─────────────┐     ┌─────────────────┐
                   │komponen_stasi│────│     stasi       │
                   └─────────────┘     └─────────────────┘
```

---

## 📋 Detail Struktur Tabel

### 1. users
Data pengguna sistem (admin dan penguji).

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| name | VARCHAR(255) | NO | Nama lengkap |
| username | VARCHAR(255) | YES | Username untuk login (unique) |
| email | VARCHAR(255) | YES | Email (unique) |
| password | VARCHAR(255) | NO | Password (hashed) |
| role | ENUM('admin','penguji') | NO | Role pengguna |
| remember_token | VARCHAR(100) | YES | Token remember me |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Index:**
- PRIMARY KEY (id)
- UNIQUE (email)
- UNIQUE (username)

---

### 2. kelas
Data kelas mahasiswa.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| kode | VARCHAR(255) | NO | Kode kelas (unique) |
| nama | VARCHAR(255) | YES | Nama kelas |
| tahun_akademik | VARCHAR(9) | YES | Tahun akademik (contoh: 2024/2025) |
| semester | ENUM('ganjil','genap') | YES | Semester |
| is_arsip | TINYINT(1) | NO | Status arsip (0=aktif, 1=diarsipkan) |
| diarsipkan_pada | TIMESTAMP | YES | Waktu diarsipkan |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Index:**
- PRIMARY KEY (id)
- UNIQUE (kode)

---

### 3. mahasiswa
Data mahasiswa peserta ujian.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| nim | VARCHAR(255) | NO | NIM mahasiswa (unique) |
| nama | VARCHAR(255) | NO | Nama mahasiswa |
| kelas_id | BIGINT | NO | Foreign key ke kelas |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- kelas_id → kelas(id) ON DELETE CASCADE

---

### 4. stasi
Data stasi/station ujian OSCE.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| nama | VARCHAR(255) | NO | Nama stasi |
| deskripsi | TEXT | YES | Deskripsi stasi |
| aktif | TINYINT(1) | NO | Status aktif (1=aktif) |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

---

### 5. komponen_stasi
Komponen penilaian per stasi.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| stasi_id | BIGINT | NO | Foreign key ke stasi |
| nama | VARCHAR(255) | NO | Nama komponen |
| bobot | TINYINT | NO | Bobot dalam persen |
| urutan | SMALLINT | NO | Urutan tampilan |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- stasi_id → stasi(id) ON DELETE CASCADE

---

### 6. global_ratings
Skala global rating untuk penilaian keseluruhan.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| kode | VARCHAR(255) | NO | Kode rating (unique) |
| nilai | TINYINT | NO | Nilai numerik |
| label | VARCHAR(255) | NO | Label (Fail/Borderline/Pass/Honours) |
| deskripsi | TEXT | YES | Deskripsi |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Data Default:**
| kode | nilai | label |
|------|-------|-------|
| F | 1 | Fail |
| B | 2 | Borderline |
| P | 3 | Pass |
| H | 4 | Honours |

---

### 7. jadwal
Jadwal ujian OSCE.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| nama | VARCHAR(255) | NO | Nama jadwal |
| mulai | DATETIME | NO | Tanggal & waktu mulai |
| selesai | DATETIME | NO | Tanggal & waktu selesai |
| keterangan | TEXT | YES | Keterangan tambahan |
| tahun_akademik | VARCHAR(9) | YES | Tahun akademik |
| semester | ENUM('ganjil','genap') | YES | Semester |
| is_arsip | TINYINT(1) | NO | Status arsip |
| diarsipkan_pada | TIMESTAMP | YES | Waktu diarsipkan |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

---

### 8. gelombang
Gelombang/sesi dalam satu jadwal ujian.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| jadwal_id | BIGINT | NO | Foreign key ke jadwal |
| nama | VARCHAR(255) | NO | Nama gelombang |
| waktu_mulai | TIME | YES | Waktu mulai (HH:MM:SS) |
| waktu_selesai | TIME | YES | Waktu selesai (HH:MM:SS) |
| urutan | INT | NO | Urutan gelombang |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- jadwal_id → jadwal(id) ON DELETE CASCADE

**Catatan Penting:**
- `waktu_mulai` dan `waktu_selesai` digunakan untuk validasi waktu penilaian
- Penguji hanya bisa input nilai saat waktu server berada dalam rentang ini

---

### 9. gelombang_mahasiswa
Peserta (mahasiswa) dalam gelombang.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| gelombang_id | BIGINT | NO | Foreign key ke gelombang |
| mahasiswa_id | BIGINT | NO | Foreign key ke mahasiswa |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- gelombang_id → gelombang(id) ON DELETE CASCADE
- mahasiswa_id → mahasiswa(id) ON DELETE CASCADE

**Index:**
- UNIQUE (gelombang_id, mahasiswa_id)

---

### 10. gelombang_penguji
Penugasan penguji per stasi per gelombang.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| gelombang_id | BIGINT | NO | Foreign key ke gelombang |
| stasi_id | BIGINT | NO | Foreign key ke stasi |
| penguji_id | BIGINT | NO | Foreign key ke users |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- gelombang_id → gelombang(id) ON DELETE CASCADE
- stasi_id → stasi(id) ON DELETE CASCADE
- penguji_id → users(id) ON DELETE CASCADE

**Index:**
- UNIQUE (gelombang_id, stasi_id) - Satu stasi hanya satu penguji per gelombang

---

### 11. penguji_stasi
Mapping penguji ke stasi (untuk referensi).

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| user_id | BIGINT | NO | Foreign key ke users |
| stasi_id | BIGINT | NO | Foreign key ke stasi |
| aktif | TINYINT(1) | NO | Status aktif |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- user_id → users(id) ON DELETE CASCADE
- stasi_id → stasi(id) ON DELETE CASCADE

---

### 12. nilai
Nilai mahasiswa per stasi.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| jadwal_id | BIGINT | NO | Foreign key ke jadwal |
| gelombang_id | BIGINT | YES | Foreign key ke gelombang |
| stasi_id | BIGINT | NO | Foreign key ke stasi |
| mahasiswa_id | BIGINT | NO | Foreign key ke mahasiswa |
| penguji_id | BIGINT | NO | Foreign key ke users |
| global_rating_id | BIGINT | YES | Foreign key ke global_ratings |
| total_nilai | DECIMAL(5,2) | NO | Total nilai (0-100) |
| nilai_aktual | DECIMAL(8,2) | NO | Nilai aktual sebelum konversi |
| lulus_stasi | TINYINT(1) | YES | Status kelulusan stasi |
| catatan | TEXT | YES | Catatan dari penguji |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- jadwal_id → jadwal(id) ON DELETE CASCADE
- gelombang_id → gelombang(id) ON DELETE SET NULL
- stasi_id → stasi(id) ON DELETE CASCADE
- mahasiswa_id → mahasiswa(id) ON DELETE CASCADE
- penguji_id → users(id) ON DELETE CASCADE
- global_rating_id → global_ratings(id) ON DELETE SET NULL

**Index:**
- UNIQUE (jadwal_id, stasi_id, mahasiswa_id, penguji_id)

---

### 13. nilai_detail
Detail nilai per komponen.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| nilai_id | BIGINT | NO | Foreign key ke nilai |
| komponen_stasi_id | BIGINT | NO | Foreign key ke komponen_stasi |
| skor | DECIMAL(5,2) | NO | Skor komponen (0-4) |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- nilai_id → nilai(id) ON DELETE CASCADE
- komponen_stasi_id → komponen_stasi(id) ON DELETE CASCADE

**Index:**
- UNIQUE (nilai_id, komponen_stasi_id)

---

### 14. nilai_acuan_stasi
Nilai acuan borderline untuk perhitungan.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| jadwal_id | BIGINT | NO | Foreign key ke jadwal |
| stasi_id | BIGINT | NO | Foreign key ke stasi |
| nilai_acuan | DECIMAL(8,2) | NO | Nilai batas borderline |
| intercept | DECIMAL(10,4) | YES | Intercept regresi |
| slope | DECIMAL(10,4) | YES | Slope regresi |
| sample_count | INT | NO | Jumlah sampel |
| calculated_at | TIMESTAMP | YES | Waktu kalkulasi |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

---

### 15. log_penilaian
Log perubahan nilai untuk audit trail.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| nilai_id | BIGINT | NO | Foreign key ke nilai |
| penguji_id | BIGINT | NO | Foreign key ke users |
| catatan | TEXT | YES | Catatan perubahan |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

**Relasi:**
- nilai_id → nilai(id) ON DELETE CASCADE
- penguji_id → users(id) ON DELETE CASCADE

---

### 16. settings
Pengaturan sistem.

| Kolom | Tipe | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | BIGINT | NO | Primary key |
| key | VARCHAR(255) | NO | Key pengaturan (unique) |
| value | TEXT | YES | Nilai pengaturan |
| created_at | TIMESTAMP | YES | Waktu dibuat |
| updated_at | TIMESTAMP | YES | Waktu diupdate |

---

## 🔄 Alur Data Penilaian

```
1. Admin membuat JADWAL
       ↓
2. Admin menambahkan GELOMBANG ke jadwal
       ↓
3. Admin menambahkan MAHASISWA ke gelombang (gelombang_mahasiswa)
       ↓
4. Admin menambahkan PENGUJI ke gelombang per stasi (gelombang_penguji)
       ↓
5. Penguji login dan memilih jadwal
       ↓
6. Sistem menampilkan stasi yang ditugaskan (dari gelombang_penguji)
       ↓
7. Penguji memilih stasi, melihat daftar mahasiswa (dari gelombang_mahasiswa)
       ↓
8. Penguji input NILAI untuk setiap komponen (nilai_detail)
       ↓
9. Sistem menyimpan total nilai dan global rating (nilai)
       ↓
10. Sistem mencatat log perubahan (log_penilaian)
```

---

## 📝 Catatan Migrasi

Urutan migrasi yang benar untuk setup database baru:

```
1. 2014_10_12_000000_create_users_table
2. 2025_01_01_000100_create_kelas_table
3. 2025_01_01_000200_create_mahasiswa_table
4. 2025_01_01_000300_create_stasi_table
5. 2025_01_01_000400_create_komponen_stasi_table
6. 2025_01_01_000500_create_penguji_stasi_table
7. 2025_01_01_000600_create_jadwal_table
8. 2025_01_01_000700_create_jadwal_mahasiswa_table
9. 2025_01_01_000800_create_global_ratings_table
10. 2025_01_01_000900_create_nilai_table
11. 2025_01_01_001000_create_nilai_detail_table
12. 2025_01_01_001100_create_log_penilaian_table
13. 2025_01_01_001200_create_settings_table
14. 2025_12_16_064555_add_arsip_columns
15. 2025_12_16_150000_update_osce_scoring_system
16. 2025_12_16_160000_add_catatan_to_nilai_table
17. 2025_12_17_010000_create_gelombang_tables
18. 2025_12_17_120000_add_username_to_users_table
```

---

*Dokumentasi ini diperbarui pada: 17 Desember 2025*
