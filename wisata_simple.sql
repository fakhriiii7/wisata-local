-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Okt 2025 pada 09.14
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wisata_simple`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi`
--

CREATE TABLE `destinasi` (
  `id` int(11) NOT NULL,
  `nama_destinasi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_dewasa` decimal(10,2) NOT NULL,
  `harga_anak` decimal(10,2) NOT NULL,
  `kuota_harian` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi`
--

INSERT INTO `destinasi` (`id`, `nama_destinasi`, `deskripsi`, `harga_dewasa`, `harga_anak`, `kuota_harian`, `gambar`, `created_at`) VALUES
(1, 'Taman Wisata Alam', 'Taman alam dengan berbagai flora dan fauna menarik', 50000.00, 25000.00, 200, NULL, '2025-10-27 09:42:26'),
(2, 'Museum Sejarah', 'Museum yang menyimpan sejarah dan budaya daerah', 35000.00, 15000.00, 150, NULL, '2025-10-27 09:42:26'),
(3, 'Air Terjun Seruni', 'Air terjun dengan pemandangan alam yang menakjubkan', 45000.00, 20000.00, 100, NULL, '2025-10-27 09:42:26'),
(4, 'Gunung Berapi View', 'Pemandangan gunung berapi yang menakjubkan dengan trekking ringan dan spot foto yang instagramable', 75000.00, 35000.00, 150, NULL, '2025-10-27 17:15:35'),
(5, 'Pantai Pasir Putih', 'Pantai dengan pasir putih yang halus, air laut jernih, dan fasilitas water sport lengkap', 60000.00, 30000.00, 250, NULL, '2025-10-27 17:15:35'),
(6, 'Hutan Wisata Alam', 'Hutan konservasi dengan berbagai flora dan fauna langka, cocok untuk edukasi dan healing', 45000.00, 20000.00, 100, NULL, '2025-10-27 17:15:35'),
(7, 'Air Panas Alami', 'Pemandian air panas alami dari sumber geothermal dengan khasiat terapi kesehatan', 55000.00, 25000.00, 120, NULL, '2025-10-27 17:15:35'),
(8, 'Desa Wisata Budaya', 'Pengalaman budaya lokal dengan workshop kerajinan tangan dan kuliner tradisional', 40000.00, 15000.00, 80, NULL, '2025-10-27 17:15:35'),
(9, 'Taman Bunga Indah', 'Taman dengan berbagai jenis bunga tropis yang cantik, perfect untuk prewedding dan fotografi', 35000.00, 15000.00, 200, NULL, '2025-10-27 17:15:35'),
(10, 'Goa Alam Menakjubkan', 'Eksplorasi goa alam dengan stalaktit dan stalagmit yang unik serta sejarah geologinya', 50000.00, 20000.00, 60, NULL, '2025-10-27 17:15:35'),
(11, 'Danau Biru Alami', 'Danau dengan air berwarna biru alami, spot memancing, dan perahu tradisional', 65000.00, 30000.00, 90, NULL, '2025-10-27 17:15:35'),
(12, 'Kebun Buah Organik', 'Kebun buah-buahan organik dengan sistem petik sendiri dan edukasi pertanian', 30000.00, 10000.00, 120, NULL, '2025-10-27 17:15:35'),
(13, 'Museum Seni Kontemporer', 'Museum seni modern dengan koleksi karya seniman lokal dan internasional', 25000.00, 10000.00, 180, NULL, '2025-10-27 17:15:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id` int(11) NOT NULL,
  `kode_booking` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destinasi_id` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `tanggal_berkunjung` date NOT NULL,
  `jumlah_dewasa` int(11) NOT NULL,
  `jumlah_anak` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id`, `kode_booking`, `user_id`, `destinasi_id`, `nama_pemesan`, `email`, `no_telepon`, `tanggal_berkunjung`, `jumlah_dewasa`, `jumlah_anak`, `total_harga`, `status`, `created_at`) VALUES
(1, 'WB20240101FA01', 4, 1, 'Fakhri Azmar', 'fakhri.azmar@email.com', '081298765431', '2024-02-10', 2, 1, 175000.00, 'confirmed', '2024-01-01 02:15:00'),
(2, 'WB20240115FA02', 4, 3, 'Fakhri Azmar', 'fakhri.azmar@email.com', '081298765431', '2024-02-20', 3, 0, 135000.00, 'confirmed', '2024-01-15 07:30:00'),
(3, 'WB20240102ZK01', 5, 2, 'Zahra Kalista', 'zahra.kalista@email.com', '081287654329', '2024-02-15', 4, 2, 300000.00, 'confirmed', '2024-01-02 03:20:00'),
(4, 'WB20240120ZK02', 5, 5, 'Zahra Kalista', 'zahra.kalista@email.com', '081287654329', '2024-03-05', 2, 1, 95000.00, 'pending', '2024-01-20 09:45:00'),
(5, 'WB20240103RA01', 6, 4, 'Rafi Ananta', 'rafi.ananta@email.com', '081276543218', '2024-02-18', 3, 1, 190000.00, 'confirmed', '2024-01-03 04:30:00'),
(6, 'WB20240118RA02', 6, 7, 'Rafi Ananta', 'rafi.ananta@email.com', '081276543218', '2024-03-10', 2, 0, 100000.00, 'confirmed', '2024-01-18 06:15:00'),
(7, 'WB20240104SN01', 7, 6, 'Salsabila Naura', 'salsabila.naura@email.com', '081265432107', '2024-02-22', 2, 3, 115000.00, 'confirmed', '2024-01-04 01:45:00'),
(8, 'WB20240122SN02', 7, 9, 'Salsabila Naura', 'salsabila.naura@email.com', '081265432107', '2024-03-15', 5, 2, 170000.00, 'pending', '2024-01-22 10:20:00'),
(9, 'WB20240105KA01', 8, 8, 'Kenzo Arjuna', 'kenzo.arjuna@email.com', '081254321096', '2024-02-25', 4, 2, 320000.00, 'confirmed', '2024-01-05 05:10:00'),
(10, 'WB20240125KA02', 8, 10, 'Kenzo Arjuna', 'kenzo.arjuna@email.com', '081254321096', '2024-03-20', 6, 3, 180000.00, 'confirmed', '2024-01-25 08:30:00'),
(11, 'WB20240106NP01', 9, 1, 'Nayla Putri', 'nayla.putri@email.com', '081243210985', '2024-02-28', 2, 0, 100000.00, 'confirmed', '2024-01-06 02:50:00'),
(12, 'WB20240128NP02', 9, 3, 'Nayla Putri', 'nayla.putri@email.com', '081243210985', '2024-03-25', 3, 1, 145000.00, 'cancelled', '2024-01-28 07:40:00'),
(13, 'WB20240107BP01', 10, 2, 'Bagas Pratama', 'bagas.pratama@email.com', '081232109874', '2024-03-01', 5, 2, 340000.00, 'confirmed', '2024-01-07 03:25:00'),
(14, 'WB20240130BP02', 10, 5, 'Bagas Pratama', 'bagas.pratama@email.com', '081232109874', '2024-03-30', 2, 2, 110000.00, 'pending', '2024-01-30 11:15:00'),
(15, 'WB20240108CM01', 11, 4, 'Citra Melati', 'citra.melati@email.com', '081221098763', '2024-03-03', 3, 0, 165000.00, 'confirmed', '2024-01-08 04:35:00'),
(16, 'WB20240201CM02', 11, 7, 'Citra Melati', 'citra.melati@email.com', '081221098763', '2024-04-05', 4, 1, 230000.00, 'confirmed', '2024-02-01 09:50:00'),
(17, 'WB20240109DR01', 12, 6, 'Daffa Rizki', 'daffa.rizki@email.com', '081210987652', '2024-03-08', 2, 1, 85000.00, 'confirmed', '2024-01-09 06:20:00'),
(18, 'WB20240205DR02', 12, 9, 'Daffa Rizki', 'daffa.rizki@email.com', '081210987652', '2024-04-10', 3, 2, 120000.00, 'pending', '2024-02-05 12:30:00'),
(19, 'WB20240110EP01', 13, 8, 'Elsa Permata', 'elsa.permata@email.com', '081209876541', '2024-03-12', 4, 3, 385000.00, 'confirmed', '2024-01-10 08:45:00'),
(20, 'WB20240210EP02', 13, 10, 'Elsa Permata', 'elsa.permata@email.com', '081209876541', '2024-04-15', 5, 1, 140000.00, 'confirmed', '2024-02-10 13:10:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `no_telepon`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@wisata.com', '$2y$10$N9yACAzVhYEvBw4BDVbpc.s/u8doDgJOsZpyrc3qctrHOMLlbyQ1G', NULL, 'admin', '2025-10-27 09:42:26'),
(4, 'Fakhri Azmar', 'fakhri.azmar@email.com', '$2y$10$oiJvAD5bZmVhe1l8cPfLeOhLGAi7DfuqGEOpoFC83FxB26gkcsiR6', '081298765431', 'user', '2025-10-28 06:41:00'),
(5, 'Zahra Kalista', 'zahra.kalista@email.com', '$2y$10$uz/k7H11SS5iNV7MsHYLu.ix1yT7rAgANhDMUJVHw4mVsMMgT7txG', '081287654329', 'user', '2025-10-28 06:41:01'),
(6, 'Rafi Ananta', 'rafi.ananta@email.com', '$2y$10$t7rCY1enNhpyXRqpw8bGA.DIL8f3WVCGV3larnMHV1d9bDjrzxAZy', '081276543218', 'user', '2025-10-28 06:41:01'),
(7, 'Salsabila Naura', 'salsabila.naura@email.com', '$2y$10$YFzcfjENNk8skU0aHKD1A.uloZ0oJqcZDTOidFm85vYcPj0fEbdcW', '081265432107', 'user', '2025-10-28 06:41:01'),
(8, 'Kenzo Arjuna', 'kenzo.arjuna@email.com', '$2y$10$XtvHqwG/pq/K6z.nmIG1x.gmVHD4D4qK5ZElqeYwVz4P4k1rxV6pu', '081254321096', 'user', '2025-10-28 06:41:01'),
(9, 'Nayla Putri', 'nayla.putri@email.com', '$2y$10$RJwGMgQto2yX2Nn9v4lSQub/ojc.8EA9oBkXKRL7Qg/.7RHgl01tK', '081243210985', 'user', '2025-10-28 06:41:01'),
(10, 'Bagas Pratama', 'bagas.pratama@email.com', '$2y$10$fjepV2pvcXXQVQszylaCnOtizNuFgKGJWa4xTwD0CWgPPQTsunSOK', '081232109874', 'user', '2025-10-28 06:41:02'),
(11, 'Citra Melati', 'citra.melati@email.com', '$2y$10$V9qBhxCwn5sWu1edUTpiKe9wEwkj8bGwkz//nNPa5H9al8r5qlEga', '081221098763', 'user', '2025-10-28 06:41:02'),
(12, 'Daffa Rizki', 'daffa.rizki@email.com', '$2y$10$.K3i4QMEvooW62DsNea3QOr/9tk8ftvWYa6uvruWf7sywq.FWmpGa', '081210987652', 'user', '2025-10-28 06:41:02'),
(13, 'Elsa Permata', 'elsa.permata@email.com', '$2y$10$ubMDCffwuLdX0dtINXNxKOshs22pLDhjN.Wrzx2xzXRneZu1.gXZ6', '081209876541', 'user', '2025-10-28 06:41:03');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_booking` (`kode_booking`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `destinasi_id` (`destinasi_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
