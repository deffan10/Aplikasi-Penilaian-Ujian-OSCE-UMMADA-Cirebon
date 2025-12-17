# 📖 Panduan Pengguna Aplikasi Ujian OSCE

Dokumen ini berisi panduan lengkap untuk menggunakan Aplikasi Ujian OSCE.

---

## 📑 Daftar Isi

1. [Pendahuluan](#pendahuluan)
2. [Login ke Sistem](#login-ke-sistem)
3. [Panduan untuk Admin](#panduan-untuk-admin)
4. [Panduan untuk Penguji](#panduan-untuk-penguji)
5. [FAQ - Pertanyaan yang Sering Diajukan](#faq)

---

## Pendahuluan

Aplikasi Ujian OSCE adalah sistem penilaian digital untuk pelaksanaan ujian Objective Structured Clinical Examination (OSCE). Sistem ini memiliki dua jenis pengguna:

| Role | Deskripsi |
|------|-----------|
| **Admin** | Mengelola data master, jadwal ujian, gelombang, dan melihat rekap nilai |
| **Penguji** | Melakukan input penilaian mahasiswa pada stasi yang ditugaskan |

---

## Login ke Sistem

### Langkah-langkah Login

1. Buka browser dan akses URL aplikasi
2. Pada halaman login, masukkan:
   - **Username** atau **Email**
   - **Password**
3. Klik tombol **"Log in"**

### Tips Login
- Centang "Remember me" jika menggunakan perangkat pribadi
- Jika lupa password, hubungi Admin untuk reset password

---

## Panduan untuk Admin

### 🏠 Dashboard Admin

Setelah login, Anda akan melihat dashboard dengan statistik:
- Jumlah Mahasiswa
- Jumlah Penguji
- Jumlah Stasi
- Jumlah Jadwal Aktif

### 📁 Menu Kelola Data Master

#### 1. Kelola Kelas
**Menu: Kelas**

| Aksi | Cara |
|------|------|
| Tambah Kelas | Klik "Tambah Kelas" → Isi Kode dan Nama → Simpan |
| Edit Kelas | Klik icon Edit pada baris kelas → Ubah data → Simpan |
| Hapus Kelas | Klik icon Hapus → Konfirmasi |
| Arsipkan | Klik "Arsipkan" untuk menyembunyikan kelas lama |

#### 2. Kelola Mahasiswa
**Menu: Mahasiswa**

| Aksi | Cara |
|------|------|
| Tambah Mahasiswa | Klik "Tambah Mahasiswa" → Isi NIM, Nama, Pilih Kelas → Simpan |
| Import Excel | Klik "Import" → Upload file Excel sesuai template |
| Edit | Klik icon Edit → Ubah data → Simpan |
| Hapus | Klik icon Hapus → Konfirmasi |

**Format Import Excel:**
| NIM | Nama | Kelas |
|-----|------|-------|
| 2024001 | Ahmad Rizki | A1 |
| 2024002 | Budi Santoso | A1 |

#### 3. Kelola Penguji
**Menu: Penguji**

| Aksi | Cara |
|------|------|
| Tambah Penguji | Klik "Tambah Penguji" → Isi Username, Nama, Email, Password → Simpan |
| Edit | Klik icon Edit → Ubah data → Simpan |
| Hapus | Klik icon Hapus → Konfirmasi |

> ⚠️ **Penting:** Catat username dan password penguji untuk diberikan ke masing-masing penguji.

#### 4. Kelola Stasi
**Menu: Stasi**

Stasi adalah pos/station dalam ujian OSCE.

| Aksi | Cara |
|------|------|
| Tambah Stasi | Klik "Tambah Stasi" → Isi Kode, Nama, Deskripsi → Simpan |
| Kelola Komponen | Klik "Komponen" → Tambah komponen penilaian dengan bobot |

**Menambah Komponen Penilaian:**
1. Masuk ke halaman Stasi
2. Klik "Komponen" pada stasi yang diinginkan
3. Klik "Tambah Komponen"
4. Isi nama komponen dan bobot (dalam persen)
5. Simpan

> 💡 **Tips:** Total bobot semua komponen dalam satu stasi harus = 100%

---

### 📅 Menu Kelola Jadwal

#### 1. Jadwal Ujian
**Menu: Jadwal → Jadwal Ujian**

**Membuat Jadwal Baru:**
1. Klik "Buat Jadwal Ujian"
2. Isi form:
   - Nama Jadwal (contoh: "OSCE Semester Ganjil 2024")
   - Tanggal & Waktu Mulai
   - Tanggal & Waktu Selesai
   - Tahun Akademik
   - Semester
   - Keterangan (opsional)
3. Simpan

**Mengatur Stasi untuk Jadwal:**
1. Pada baris jadwal, klik "Kelola Stasi"
2. Centang stasi yang akan digunakan
3. Simpan

**Mengatur Penguji per Stasi:**
1. Pada baris jadwal, klik "Penguji"
2. Untuk setiap stasi, pilih penguji yang bertugas
3. Simpan

#### 2. Kelola Gelombang
**Menu: Jadwal → Kelola Gelombang**

Gelombang adalah pembagian waktu ujian dalam satu jadwal.

**Membuat Gelombang:**
1. Pilih jadwal ujian
2. Klik "Tambah Gelombang"
3. Isi form:
   - Nama Gelombang (contoh: "Gelombang 1")
   - Waktu Mulai (contoh: 08:00)
   - Waktu Selesai (contoh: 10:00)
4. Simpan

**Menambah Peserta ke Gelombang:**
1. Pada gelombang, klik "Peserta"
2. Pilih mahasiswa dari daftar
3. Simpan

**Menambah Penguji ke Gelombang:**
1. Pada gelombang, klik "Penguji"
2. Untuk setiap stasi, pilih penguji yang bertugas di gelombang tersebut
3. Simpan

> ⚠️ **Penting:** 
> - Waktu gelombang menentukan kapan penguji BISA melakukan input nilai
> - Penguji hanya bisa input nilai saat waktu server berada dalam rentang waktu gelombang
> - Pastikan waktu server sudah sinkron dengan benar

**Memindahkan Peserta antar Gelombang:**
1. Pada gelombang asal, klik "Peserta"
2. Hapus mahasiswa dari gelombang asal
3. Pada gelombang tujuan, tambahkan mahasiswa tersebut

---

### 📊 Menu Rekap Nilai

**Menu: Rekap Nilai**

**Melihat Rekap:**
1. Pilih jadwal ujian
2. Pilih gelombang (opsional, untuk filter)
3. Sistem akan menampilkan tabel nilai semua peserta

**Kolom yang ditampilkan:**
- NIM
- Nama Mahasiswa
- Nilai per Stasi
- Total Nilai
- Status Kelulusan

**Export ke Excel:**
1. Pada halaman rekap, klik "Export Excel"
2. File akan otomatis terdownload

---

### ⚙️ Menu Settings

**Menu: Settings**

Pengaturan sistem yang tersedia:
- Nama Institusi
- Logo Institusi
- Batas Nilai Kelulusan
- Pengaturan lainnya

---

## Panduan untuk Penguji

### 🏠 Dashboard Penguji

Setelah login, Anda akan melihat:
- **Salam Sapaan** berdasarkan waktu (Selamat Pagi/Siang/Sore/Malam)
- **Jam Server** yang berjalan realtime
- Informasi penugasan Anda

### 📝 Melakukan Penilaian

**Menu: Penilaian**

#### Langkah 1: Pilih Jadwal
1. Sistem akan menampilkan daftar jadwal ujian yang aktif
2. Klik pada jadwal untuk melanjutkan

#### Langkah 2: Pilih Stasi
1. Sistem menampilkan stasi yang ditugaskan kepada Anda
2. Terlihat progress penilaian (contoh: "3/10 mahasiswa dinilai")
3. Klik pada stasi untuk melanjutkan

#### Langkah 3: Pilih Mahasiswa
1. Sistem menampilkan daftar mahasiswa dalam gelombang aktif
2. Status penilaian terlihat:
   - ✅ Sudah dinilai (warna hijau)
   - ⏳ Belum dinilai (warna kuning)
3. Klik pada mahasiswa untuk menilai

#### Langkah 4: Input Nilai
1. Isi nilai untuk setiap komponen (0-4):
   - **0**: Tidak Dilakukan
   - **1**: Dilakukan tapi Salah
   - **2**: Dilakukan dengan Bimbingan
   - **3**: Dilakukan dengan Benar
   - **4**: Dilakukan dengan Sempurna

2. Pilih **Global Rating** (penilaian keseluruhan):
   - Fail
   - Borderline
   - Pass
   - Honours

3. Tentukan **Kelulusan Stasi** (Lulus/Tidak Lulus)

4. Tambahkan **Catatan** jika diperlukan

5. Klik **"Simpan Nilai"**

> 💡 **Tips:** 
> - Anda bisa kembali dan mengedit nilai yang sudah disimpan
> - Semua perubahan tercatat dalam log sistem

### ⏰ Tentang Waktu Penilaian

**Sistem memiliki validasi waktu:**

| Status | Keterangan |
|--------|------------|
| 🟢 **Dapat Menilai** | Waktu server berada dalam rentang gelombang aktif |
| 🔴 **Belum Dimulai** | Waktu gelombang belum tiba, belum bisa menilai |
| 🔴 **Sudah Berakhir** | Waktu gelombang sudah lewat, tidak bisa menilai |

**Jika muncul pesan "Waktu penilaian belum dimulai" atau "sudah berakhir":**
1. Periksa jam di sudut kanan atas (jam server)
2. Pastikan Anda berada di gelombang yang benar
3. Hubungi Admin jika ada masalah

### 📱 Penggunaan di Mobile

Aplikasi dapat diakses melalui smartphone:

1. Buka browser di HP
2. Akses URL aplikasi
3. Klik tombol hamburger (☰) untuk membuka menu
4. Jam server terlihat di sebelah menu hamburger
5. Navigasi sama seperti versi desktop

---

## FAQ

### Umum

**Q: Bagaimana jika lupa password?**
> Hubungi Admin untuk melakukan reset password.

**Q: Apakah bisa mengakses dari HP?**
> Ya, aplikasi responsive dan dapat diakses dari berbagai perangkat.

**Q: Jam di layar berbeda dengan jam di HP saya?**
> Jam yang ditampilkan adalah jam server. Ini yang menjadi acuan untuk validasi waktu penilaian.

### Untuk Penguji

**Q: Kenapa saya tidak bisa menilai mahasiswa?**
> Beberapa kemungkinan:
> 1. Waktu gelombang belum dimulai atau sudah berakhir
> 2. Anda tidak ditugaskan di stasi tersebut untuk gelombang ini
> 3. Mahasiswa tidak terdaftar dalam gelombang aktif
> 
> Hubungi Admin untuk verifikasi.

**Q: Bisa mengedit nilai yang sudah disimpan?**
> Ya, selama masih dalam rentang waktu gelombang yang aktif.

**Q: Bagaimana jika salah input nilai?**
> Kembali ke halaman mahasiswa tersebut dan edit nilainya. Semua perubahan tercatat di log.

**Q: Kenapa progress menunjukkan angka yang tidak sesuai?**
> Progress menghitung jumlah mahasiswa yang sudah dinilai dari total mahasiswa dalam gelombang aktif saat ini.

### Untuk Admin

**Q: Bagaimana memindahkan mahasiswa ke gelombang lain?**
> 1. Hapus mahasiswa dari gelombang asal
> 2. Tambahkan ke gelombang tujuan
> 3. Nilai yang sudah diinput akan tetap tersimpan

**Q: Bagaimana mengarsipkan jadwal lama?**
> Pada halaman Jadwal, klik "Arsipkan" pada jadwal yang ingin diarsipkan. Jadwal akan tersembunyi dari daftar aktif.

**Q: Bagaimana melihat jadwal yang diarsipkan?**
> Pada halaman Jadwal, aktifkan filter "Tampilkan Arsip".

---

## 📞 Bantuan

Jika mengalami kendala teknis, hubungi:
- **Tim IT Support**: [Isi kontak support]
- **Email**: [Isi email support]

---

*Dokumen ini diperbarui pada: 17 Desember 2025*
