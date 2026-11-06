-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Nov 2026 pada 14.11
-- Versi server: 10.4.32-MariaDB-log
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mbg_sekolah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumentasi`
--

CREATE TABLE `dokumentasi` (
  `id_dokumentasi` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `lokasi_file` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dokumentasi`
--

INSERT INTO `dokumentasi` (`id_dokumentasi`, `id_kegiatan`, `lokasi_file`, `keterangan`) VALUES
(1, 4, 'uploads/dokumentasi/2d42242523e529cbda7906b7f0e7338c.jpg', 'Penyerahan makanan kepada siswa'),
(2, 1, 'uploads/dokumentasi/7d10c4cf8c5dc4f82479afd0f315a09c.jpg', 'penyerahan makanan kepada siswa'),
(3, 7, 'uploads/dokumentasi/0c755802d02331cdda45f9b66f5b7a7a.jpg', 'Penyerahan makanan kepada siswa'),
(4, 8, 'uploads/dokumentasi/7b0522a47676f8e5cd742e3791751521.jpg', 'Pembagian MBG'),
(5, 9, 'uploads/dokumentasi/e4b8b136e4f8e7f03c76184e54c791c2.jpg', 'penyerahan kepada siswa'),
(7, 10, 'uploads/dokumentasi/16f8129c76e32e8b6c4262fce153a24b.jpg', 'Penyerahan makanan kepada siswa'),
(8, 11, 'uploads/dokumentasi/bcdcb5b3bfbe2dd6c031049c05b5cfc6.jpg', 'Penyerahan makanan kepada siswa'),
(9, 12, 'uploads/dokumentasi/6a5128385a61946b164e0b685f6c8376.jpg', 'Penyerahan makanan kepada siswa'),
(10, 13, 'uploads/dokumentasi/2235c44775f22ba33c0693423d85f3a5.jpg', 'Penyerahan makanan kepada siswa'),
(12, 14, 'uploads/dokumentasi/73844ef8352dc9fc50fb809c0e8868bd.jpg', 'Penyerahan makanan kepada siswa'),
(13, 15, 'uploads/dokumentasi/91cb83a8b68d091eb9eeeeb697dace3c.jpg', 'Penyerahan makanan kepada siswa'),
(14, 16, 'uploads/dokumentasi/66dbced61ff493ef4682cbc04a23b84b.jpg', 'Penyerahan makanan kepada siswa'),
(15, 17, 'uploads/dokumentasi/a12090dd73cdf104bc08f4e97a09c7dc.jpg', 'Penyerahan makanan kepada siswa'),
(16, 18, 'uploads/dokumentasi/2bba4bb9981e18dc7c216bee9bd1f833.jpg', 'Penerimaan MBG'),
(17, 19, 'uploads/dokumentasi/b183b99f1eba0f74eea7a82a04574cab.jpg', 'Penyerahan makanan kepada siswa'),
(18, 20, 'uploads/dokumentasi/38870d3f5bb6e5010f2e7c9e009db92f.jpg', 'Penerimaan MBG'),
(19, 22, 'uploads/dokumentasi/13cac7b0b180c5c1abbe8239863aafd7.jpg', 'Penerimaan MBG'),
(20, 23, 'uploads/dokumentasi/262238d065b6f15eff0d4d85b2868acf.jpg', 'Penerimaan MBG'),
(21, 28, 'uploads/dokumentasi/abd0244b42f73b68e1b69eaf25c34db7.jpg', 'Penyerahan makanan kepada siswa'),
(22, 29, 'uploads/dokumentasi/9c689990ca23dc6c9d9ff281655c483f.jpg', 'Penyerahan makanan kepada siswa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatan_mbg`
--

CREATE TABLE `kegiatan_mbg` (
  `id_kegiatan` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `sesi` varchar(50) DEFAULT NULL,
  `menu_makanan` varchar(255) DEFAULT NULL,
  `kondisi_makanan` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kegiatan_mbg`
--

INSERT INTO `kegiatan_mbg` (`id_kegiatan`, `id_pengguna`, `tanggal`, `sesi`, `menu_makanan`, `kondisi_makanan`, `catatan`) VALUES
(1, 2, '2026-10-12', 'Pagi', 'Nasi Putih\r\nTelur Gulai\r\nSusu UHT 115 ml\r\nTumis Labu Siam + Jagung\r\nBuah Klengkeng', 'Baik', ''),
(4, 2, '2026-10-12', 'Siang', 'Nasi Putih  \r\nTelur Gulai  \r\n Susu UHT 115 ml  \r\n Tumis Labu Siam + Jagung  \r\n Buah Kelengkeng', 'Baik', ''),
(7, 2, '2026-10-13', 'Pagi', 'Nasi Putih,\r\nAyam Kecap,\r\nTahu Bakso, \r\nTumis Buncis Jagung Pipil,\r\nBuah Anggur Hijau / Shine Muscat', 'Baik', ''),
(8, 2, '2026-10-13', 'Siang', 'Nasi Putih, Ayam Kecap,\r\nTahu Bakso,\r\nTumis Buncis Jagung Pipil,\r\nBuah Anggur Hijau / Shine Muscat', 'Baik', ''),
(9, 2, '2026-10-14', 'Pagi', 'Nasi Putih,\r\nSempol Ayam,\r\nJamur Crispy,\r\nTumis Putren,\r\nWortel & Sawi Putih,\r\nBuah Pisang Cavendish', 'Baik', ''),
(10, 2, '2026-10-14', 'Siang', 'Nasi Putih,\r\nSempol Ayam,\r\nJamur Crispy,\r\nTumis Putren,\r\nWortel & Sawi Putih,\r\nBuah Pisang Cavendish', 'Baik', ''),
(11, 2, '2026-10-15', 'Pagi', 'Nasi Putih,\r\nAyam Yakiniku,\r\nTahu Sumedang,  \r\nLumpia Sayur,  \r\nBuah Anggur Merah', 'Baik', ''),
(12, 2, '2026-10-15', 'Siang', 'Nasi Putih,\r\nAyam Yakiniku,\r\nTahu Sumedang,\r\nLumpia Sayur,\r\nBuah Anggur Merah', 'Baik', ''),
(13, 2, '2026-10-16', 'Pagi', 'Nasi Putih. \r\nTelur Gulai,  \r\nSusu UHT 115 ml,  \r\nTumis Labu Siam + Jagung,  \r\nBuah Kelengkeng', 'Baik', ''),
(14, 2, '2026-10-16', 'Siang', 'Nasi Putih,\r\nTelur Gulai,\r\nSusu UHT 115 ml,\r\nTumis Labu Siam + Jagung,\r\nBuah Kelengkeng', 'Baik', ''),
(15, 2, '2026-10-19', 'Pagi', 'Nasi putih,\r\nAyam goreng,\r\nTumis wortel dan buncis,\r\nTempe goreng,\r\nBuah pisang', 'Baik', ''),
(16, 2, '2026-10-19', 'Siang', 'Nasi putih, \r\nAyam goreng,\r\nTumis wortel dan buncis,\r\nTempe goreng,\r\nBuah pisang', 'Baik', ''),
(17, 2, '2026-10-20', 'Pagi', 'Nasi putih,\r\nAyam teriyaki,\r\nTumis brokoli dan wortel,\r\nTelur rebus,\r\nBuah semangka', 'Baik', ''),
(18, 2, '2026-10-20', 'Siang', 'Nasi putih, \r\nAyam teriyaki,\r\nTumis brokoli dan wortel,\r\nTelur rebus, \r\nBuah semangka', 'Baik', ''),
(19, 2, '2026-10-21', 'Pagi', 'Potato Wedges,  \r\nDory Crispy Saus Tomat,  \r\nTahu Sumedang,\r\nCapcay,\r\nBuah Pisang Cavendish', 'Baik', ''),
(20, 2, '2026-10-21', 'Siang', 'Potato Wedges,\r\nDory Crispy Saus Tomat,\r\nTahu Sumedang,\r\nCapcay,\r\nBuah Pisang Cavendish', 'Baik', ''),
(22, 2, '2026-10-22', 'Pagi', 'Nasi Daun Jeruk,\r\nChicken Katsu,\r\nTempe Balado,  \r\nTumis Sawi Putih,  \r\nBuah Kelengkeng', 'Baik', ''),
(23, 2, '2026-10-22', 'Siang', 'Nasi Daun Jeruk, \r\nChicken Katsu, \r\nTempe Balado,\r\nTumis Sawi Putih,\r\nBuah Kelengkeng', 'Baik', ''),
(28, 2, '2026-10-23', 'Pagi', 'Nasi Putih,\r\nTelur Fuyunghai Asam Manis,\r\nTahu Garam,\r\nTumis Buncis Bawang Putih, \r\nBuah Anggur Hijau / Shine Muscat', 'Baik', ''),
(29, 2, '2026-10-23', 'Siang', 'Nasi Putih,\r\nTelur Fuyunghai Asam Manis,\r\nTahu Garam,\r\nTumis Buncis Bawang Putih, \r\nBuah Anggur Hijau / Shine Muscat', 'Baik', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `jumlah_siswa_l` int(11) DEFAULT 0,
  `jumlah_siswa_p` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `nama_kelas`, `tahun_ajaran`, `jumlah_siswa_l`, `jumlah_siswa_p`) VALUES
(1, '1A', '2026/2027', 17, 15),
(2, '1B', '2026/2027', 17, 15),
(4, '1C', '2026/2027', 17, 15),
(5, '2A', '2026/2027', 14, 14),
(6, '2B', '2026/2027', 14, 14),
(7, '2C', '2026/2027', 13, 15),
(8, '2D', '2026/2027', 14, 14),
(15, '3A', '2026/2027', 18, 13),
(16, '3B', '2026/2027', 18, 14),
(17, '3C', '2026/2027', 18, 13),
(18, '3D', '2026/2027', 17, 14),
(19, '4A', '2026/2027', 16, 16),
(20, '4B', '2026/2027', 13, 19),
(21, '4C', '2026/2027', 14, 18),
(22, '4D', '2026/2027', 12, 19),
(23, '5A', '2026/2027', 15, 14),
(24, '5B', '2026/2027', 16, 12),
(25, '5C', '2026/2027', 15, 14),
(26, '5D', '2026/2027', 14, 15),
(27, '6A', '2026/2027', 16, 17),
(28, '6B', '2026/2027', 20, 13),
(29, '6C', '2026/2027', 17, 16),
(35, '1D', '2026/2027', 9, 12);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pencatatan_guru`
--

CREATE TABLE `pencatatan_guru` (
  `id_pencatatan_guru` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `jumlah_porsi_diterima` int(11) NOT NULL DEFAULT 0,
  `jumlah_ompreng_kembali` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pencatatan_kelas`
--

CREATE TABLE `pencatatan_kelas` (
  `id_pencatatan` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `jumlah_porsi_diterima` int(11) NOT NULL DEFAULT 0,
  `jumlah_ompreng_diterima` int(11) NOT NULL DEFAULT 0,
  `jumlah_ompreng_kembali` int(11) NOT NULL DEFAULT 0,
  `pj_penerimaan` varchar(100) DEFAULT NULL,
  `pj_pengembalian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pencatatan_kelas`
--

INSERT INTO `pencatatan_kelas` (`id_pencatatan`, `id_kegiatan`, `id_kelas`, `jumlah_porsi_diterima`, `jumlah_ompreng_diterima`, `jumlah_ompreng_kembali`, `pj_penerimaan`, `pj_pengembalian`) VALUES
(1, 1, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(2, 1, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(3, 1, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(4, 1, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(5, 1, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(6, 1, 7, 28, 28, 28, 'Nita Herawat', 'Nita Herawat'),
(7, 1, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(8, 1, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(9, 1, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(10, 1, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(11, 1, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(12, 1, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(13, 1, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(14, 4, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(15, 4, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(16, 4, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(17, 4, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(18, 4, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(19, 4, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(20, 4, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(21, 4, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(22, 4, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(23, 7, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(24, 7, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(25, 7, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(26, 7, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(27, 7, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(28, 7, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(29, 7, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(30, 7, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(31, 7, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(32, 7, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(33, 7, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(34, 7, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(35, 7, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(36, 8, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(37, 8, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(38, 8, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(39, 8, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(40, 8, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(41, 8, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(42, 8, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(43, 8, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(44, 8, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(45, 9, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(46, 9, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(47, 9, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(48, 9, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(49, 9, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(50, 9, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(51, 9, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(52, 9, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(53, 9, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(54, 9, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(55, 9, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(56, 9, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(57, 9, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(58, 10, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(59, 10, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(60, 10, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(61, 10, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(62, 10, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(63, 11, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(64, 10, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(65, 10, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(66, 10, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(67, 10, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(68, 11, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(69, 11, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(70, 11, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(71, 11, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(72, 11, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(73, 11, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(74, 11, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(75, 11, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(76, 11, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(77, 11, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(78, 11, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(79, 11, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(80, 12, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(81, 12, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(82, 12, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(83, 12, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(84, 13, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(85, 12, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(86, 12, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(87, 12, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(88, 12, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(89, 12, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(90, 13, 1, 32, 32, 32, 'Tri Winarti', 'Tri Winarti'),
(91, 13, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(92, 13, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(93, 13, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(94, 13, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(95, 13, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(96, 13, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(97, 13, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(98, 13, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(99, 13, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(100, 13, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(101, 13, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(102, 14, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(103, 14, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(104, 14, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(105, 14, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(106, 14, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(107, 14, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(108, 14, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(109, 14, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(110, 14, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(111, 15, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(112, 15, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(113, 15, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(114, 15, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(115, 15, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(116, 15, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(117, 15, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(118, 15, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(119, 15, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(120, 15, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(121, 15, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(122, 15, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(123, 15, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(124, 16, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(125, 16, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(126, 16, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(127, 16, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(128, 16, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(129, 16, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(130, 16, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(131, 16, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(132, 16, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(133, 17, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(134, 17, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(135, 17, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(136, 17, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(137, 17, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(138, 17, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(139, 17, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(140, 17, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(141, 17, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(142, 17, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(143, 17, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(144, 17, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(145, 17, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(146, 18, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(147, 18, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(148, 18, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(149, 18, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(150, 18, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(151, 18, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(152, 18, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(153, 18, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(154, 18, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(155, 19, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(156, 19, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(157, 19, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(158, 19, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(159, 19, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(160, 19, 7, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(161, 19, 8, 28, 28, 28, 'Nita Herawati', 'Nita Herawati'),
(162, 19, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(163, 19, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(164, 19, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(165, 19, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(166, 19, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(167, 19, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(168, 20, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(169, 20, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(170, 20, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(171, 20, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(172, 20, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(173, 20, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(174, 20, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(175, 20, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(176, 20, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(177, 22, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(178, 22, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(179, 22, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(180, 22, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(181, 22, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(182, 22, 7, 28, 28, 28, 'Nita Herawat', 'Nita Herawat'),
(184, 22, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(185, 22, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(186, 22, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(187, 22, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(188, 22, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(189, 22, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(190, 23, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(191, 23, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(192, 23, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(193, 23, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(194, 23, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(195, 23, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(196, 23, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(197, 23, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(198, 23, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya'),
(199, 22, 8, 28, 28, 28, 'Nita Herawat', 'Nita Herawat'),
(200, 28, 1, 32, 32, 32, 'Titin Kartini', 'Titin Kartini'),
(201, 28, 2, 32, 32, 32, 'Riri Addaafi\'ah Febriana', 'Riri Addaafi\'ah Febriana'),
(202, 28, 4, 32, 32, 32, 'Santiyanah', 'Santiyanah'),
(203, 28, 5, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(204, 28, 6, 28, 28, 28, 'Zahrotul Humairoh', 'Zahrotul Humairoh'),
(205, 28, 7, 28, 28, 28, 'Nita Herawat', 'Nita Herawat'),
(206, 28, 8, 28, 28, 28, 'Nita Herawat', 'Nita Herawat'),
(207, 28, 23, 29, 29, 29, 'Dyah Purbowati', 'Dyah Purbowati'),
(208, 28, 24, 28, 28, 28, 'Tri Winarti', 'Tri Winarti'),
(209, 28, 26, 29, 29, 29, 'Yulianti', 'Yulianti'),
(210, 28, 27, 33, 33, 33, 'Riana Widayanthi', 'Riana Widayanthi'),
(211, 28, 28, 33, 33, 33, 'Diana Mardiana', 'Diana Mardiana'),
(212, 28, 29, 33, 33, 33, 'Dwi Wastuti', 'Dwi Wastuti'),
(213, 29, 15, 31, 31, 31, 'Rustardi', 'Rustardi'),
(214, 29, 19, 32, 32, 32, 'Laniy Andrean', 'Laniy Andrean'),
(215, 29, 16, 32, 32, 32, 'Mega Septi Harnah', 'Mega Septi Harnah'),
(216, 29, 17, 31, 31, 31, 'Bagja Restu Ramadhan', 'Bagja Restu Ramadhan'),
(217, 29, 18, 31, 31, 31, 'Eneng Nurma Sulfiani', 'Eneng Nurma Sulfiani'),
(218, 29, 20, 32, 32, 32, 'Mahpudin', 'Mahpudin'),
(219, 29, 21, 32, 32, 32, 'Trisnaningsih', 'Trisnaningsih'),
(220, 29, 22, 31, 31, 31, 'Evi Oktaviani', 'Evi Oktaviani'),
(221, 29, 25, 29, 29, 29, 'Anggi Rizcy Retyani Kamya', 'Anggi Rizcy Retyani Kamya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penerima_mbg`
--

CREATE TABLE `penerima_mbg` (
  `id_penerima` int(11) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `status_penerima` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penerima_mbg`
--

INSERT INTO `penerima_mbg` (`id_penerima`, `nama_penerima`, `id_kelas`, `status_penerima`, `tanggal_input`) VALUES
(1, 'Titin Kartini', 1, 'Aktif', '2026-10-19 16:01:10'),
(2, 'Riri Addaafi\'ah Febriana', 2, 'Aktif', '2026-10-22 17:47:59'),
(3, 'Santiyanah', 4, 'Aktif', '2026-10-22 17:48:25'),
(5, 'Zahrotul Humairoh', 6, 'Aktif', '2026-10-22 17:48:38'),
(6, 'Nita Herawati', 7, 'Aktif', '2026-10-22 17:48:54'),
(7, 'Nita Herawati', 8, 'Aktif', '2026-10-22 17:49:22'),
(8, 'Rustardi', 15, 'Aktif', '2026-10-22 17:49:33'),
(10, 'Bagja Restu Ramadhan', 17, 'Aktif', '2026-10-22 17:49:57'),
(11, 'Eneng Nurma Sulfiani', 18, 'Aktif', '2026-10-22 17:50:13'),
(12, 'Dwi Wastuti', 29, 'Aktif', '2026-10-22 17:50:24'),
(14, 'Trisnaningsih', 21, 'Aktif', '2026-10-22 17:50:41'),
(15, 'Evi Oktaviani', 22, 'Aktif', '2026-10-22 17:50:56'),
(16, 'Mahpudin', 20, 'Aktif', '2026-10-22 17:51:08'),
(17, 'Anggi Rizcy Retyani Kamya', 25, 'Aktif', '2026-10-22 17:51:17'),
(18, 'Tri Winarti', 24, 'Aktif', '2026-10-22 17:51:17'),
(19, 'Yulianti', 26, 'Aktif', '2026-10-22 17:51:40'),
(21, 'Diana Mardiana', 28, 'Aktif', '2026-10-22 17:51:58'),
(23, 'Riana Widyanthi', 27, 'Aktif', '2026-10-25 06:18:29'),
(24, 'Dyah Purbowati', 23, 'Aktif', '2026-10-25 06:19:09'),
(25, 'Laniy Andrean', 19, 'Aktif', '2026-10-25 06:19:44'),
(26, 'Mega Septi Harnah', 16, 'Aktif', '2026-10-25 06:20:29'),
(27, 'Zahrotul Humairoh', 5, 'Aktif', '2026-10-25 06:21:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `peran` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `username`, `password_hash`, `peran`) VALUES
(1, 'Admin Sekolah', 'admin', '$2y$10$XyKsPHdu5eeXuqujvnR4/.h6VDAgzwJbkxuNgNJYpXGhp5QWipnhO', 'admin'),
(2, 'Trisna Ningsih', 'petugas', '$2y$10$Rv2skdcFd4/UiZM7iDq7t.eToEuyxsgTIlEdhcqtGjMImB18aXHRG', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dokumentasi`
--
ALTER TABLE `dokumentasi`
  ADD PRIMARY KEY (`id_dokumentasi`),
  ADD KEY `id_kegiatan` (`id_kegiatan`);

--
-- Indeks untuk tabel `kegiatan_mbg`
--
ALTER TABLE `kegiatan_mbg`
  ADD PRIMARY KEY (`id_kegiatan`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id_kelas`);

--
-- Indeks untuk tabel `pencatatan_guru`
--
ALTER TABLE `pencatatan_guru`
  ADD PRIMARY KEY (`id_pencatatan_guru`),
  ADD UNIQUE KEY `id_kegiatan` (`id_kegiatan`);

--
-- Indeks untuk tabel `pencatatan_kelas`
--
ALTER TABLE `pencatatan_kelas`
  ADD PRIMARY KEY (`id_pencatatan`),
  ADD KEY `id_kegiatan` (`id_kegiatan`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indeks untuk tabel `penerima_mbg`
--
ALTER TABLE `penerima_mbg`
  ADD PRIMARY KEY (`id_penerima`),
  ADD KEY `fk_penerima_kelas` (`id_kelas`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dokumentasi`
--
ALTER TABLE `dokumentasi`
  MODIFY `id_dokumentasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `kegiatan_mbg`
--
ALTER TABLE `kegiatan_mbg`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `pencatatan_guru`
--
ALTER TABLE `pencatatan_guru`
  MODIFY `id_pencatatan_guru` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pencatatan_kelas`
--
ALTER TABLE `pencatatan_kelas`
  MODIFY `id_pencatatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT untuk tabel `penerima_mbg`
--
ALTER TABLE `penerima_mbg`
  MODIFY `id_penerima` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dokumentasi`
--
ALTER TABLE `dokumentasi`
  ADD CONSTRAINT `dokumentasi_ibfk_1` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan_mbg` (`id_kegiatan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kegiatan_mbg`
--
ALTER TABLE `kegiatan_mbg`
  ADD CONSTRAINT `kegiatan_mbg_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pencatatan_guru`
--
ALTER TABLE `pencatatan_guru`
  ADD CONSTRAINT `pencatatan_guru_ibfk_1` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan_mbg` (`id_kegiatan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pencatatan_kelas`
--
ALTER TABLE `pencatatan_kelas`
  ADD CONSTRAINT `pencatatan_kelas_ibfk_1` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan_mbg` (`id_kegiatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pencatatan_kelas_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penerima_mbg`
--
ALTER TABLE `penerima_mbg`
  ADD CONSTRAINT `fk_penerima_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
