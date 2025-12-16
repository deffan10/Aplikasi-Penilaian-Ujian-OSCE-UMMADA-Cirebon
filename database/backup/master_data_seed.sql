-- ===========================================================================
-- OSCE Assessment Application - Database Schema Only
-- Version: 1.0.0
-- Date: 2025-12-16
-- Description: Schema for fresh production install
-- ===========================================================================

-- ===========================================================================
-- Global Ratings (4 Tingkat - BAB VII)
-- ===========================================================================
INSERT INTO `global_ratings` (`id`, `kode`, `nilai`, `label`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'TIDAK_LULUS', 1, 'Tidak Lulus', 'Tidak berhasil melakukan sebagian besar langkah dengan benar', NOW(), NOW()),
(2, 'BORDERLINE', 2, 'Borderline (KKM)', 'Berada di ambang batas kelulusan', NOW(), NOW()),
(3, 'LULUS', 3, 'Lulus', 'Berhasil melakukan sebagian besar langkah dengan benar', NOW(), NOW()),
(4, 'SUPERIOR', 4, 'Superior', 'Berhasil melakukan seluruh langkah dengan sangat baik', NOW(), NOW());

-- ===========================================================================
-- Default Admin User (password: password)
-- ===========================================================================
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@osce.test', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());

-- ===========================================================================
-- Sample Stasi Data
-- ===========================================================================
INSERT INTO `stasi` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Stasi 1 - Komunikasi Pasien', 'Menilai kemampuan komunikasi dengan pasien', NOW(), NOW()),
(2, 'Stasi 2 - Dispensing Obat', 'Menilai kemampuan dispensing dan penyerahan obat', NOW(), NOW()),
(3, 'Stasi 3 - Konseling Obat', 'Menilai kemampuan konseling penggunaan obat', NOW(), NOW()),
(4, 'Stasi 4 - Compounding', 'Menilai kemampuan peracikan sediaan farmasi', NOW(), NOW()),
(5, 'Stasi 5 - Skrining Resep', 'Menilai kemampuan skrining dan validasi resep', NOW(), NOW()),
(6, 'Stasi 6 - Monitoring Efek Samping', 'Menilai kemampuan monitoring efek samping obat', NOW(), NOW());

-- ===========================================================================
-- Sample Komponen Stasi Data (Bobot total = 100 per stasi)
-- Skor komponen: 0-3
-- ===========================================================================
INSERT INTO `komponen_stasi` (`stasi_id`, `nama`, `bobot`, `urutan`, `created_at`, `updated_at`) VALUES
-- Stasi 1
(1, 'Persiapan dan Perkenalan', 15, 1, NOW(), NOW()),
(1, 'Pengumpulan Informasi', 20, 2, NOW(), NOW()),
(1, 'Pelaksanaan Prosedur', 30, 3, NOW(), NOW()),
(1, 'Komunikasi dan Edukasi', 20, 4, NOW(), NOW()),
(1, 'Dokumentasi dan Penutup', 15, 5, NOW(), NOW()),
-- Stasi 2
(2, 'Persiapan dan Perkenalan', 15, 1, NOW(), NOW()),
(2, 'Pengumpulan Informasi', 20, 2, NOW(), NOW()),
(2, 'Pelaksanaan Prosedur', 30, 3, NOW(), NOW()),
(2, 'Komunikasi dan Edukasi', 20, 4, NOW(), NOW()),
(2, 'Dokumentasi dan Penutup', 15, 5, NOW(), NOW()),
-- Stasi 3
(3, 'Persiapan dan Perkenalan', 15, 1, NOW(), NOW()),
(3, 'Pengumpulan Informasi', 20, 2, NOW(), NOW()),
(3, 'Pelaksanaan Prosedur', 30, 3, NOW(), NOW()),
(3, 'Komunikasi dan Edukasi', 20, 4, NOW(), NOW()),
(3, 'Dokumentasi dan Penutup', 15, 5, NOW(), NOW()),
-- Stasi 4
(4, 'Persiapan dan Perkenalan', 15, 1, NOW(), NOW()),
(4, 'Pengumpulan Informasi', 20, 2, NOW(), NOW()),
(4, 'Pelaksanaan Prosedur', 30, 3, NOW(), NOW()),
(4, 'Komunikasi dan Edukasi', 20, 4, NOW(), NOW()),
(4, 'Dokumentasi dan Penutup', 15, 5, NOW(), NOW()),
-- Stasi 5
(5, 'Persiapan dan Perkenalan', 15, 1, NOW(), NOW()),
(5, 'Pengumpulan Informasi', 20, 2, NOW(), NOW()),
(5, 'Pelaksanaan Prosedur', 30, 3, NOW(), NOW()),
(5, 'Komunikasi dan Edukasi', 20, 4, NOW(), NOW()),
(5, 'Dokumentasi dan Penutup', 15, 5, NOW(), NOW()),
-- Stasi 6
(6, 'Persiapan dan Perkenalan', 15, 1, NOW(), NOW()),
(6, 'Pengumpulan Informasi', 20, 2, NOW(), NOW()),
(6, 'Pelaksanaan Prosedur', 30, 3, NOW(), NOW()),
(6, 'Komunikasi dan Edukasi', 20, 4, NOW(), NOW()),
(6, 'Dokumentasi dan Penutup', 15, 5, NOW(), NOW());

-- ===========================================================================
-- Sample Kelas Data
-- ===========================================================================
INSERT INTO `kelas` (`id`, `kode`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'D3-3A', 'D3 Farmasi Semester 3 Kelas A', NOW(), NOW()),
(2, 'D3-3B', 'D3 Farmasi Semester 3 Kelas B', NOW(), NOW()),
(3, 'S1-5A', 'S1 Farmasi Semester 5 Kelas A', NOW(), NOW()),
(4, 'S1-5B', 'S1 Farmasi Semester 5 Kelas B', NOW(), NOW());

-- ===========================================================================
-- End of File
-- ===========================================================================
