# PERANCANGAN SISTEM MONITORING DAN PENDATAAN PROGRAM MAKAN BERGIZI GRATIS (MBG)

## 1. Deskripsi Project

Project ini merupakan aplikasi berbasis web yang dirancang untuk membantu proses monitoring dan pendataan pelaksanaan Program Makan Bergizi Gratis (MBG) pada tingkat sekolah dasar.

Sistem ini digunakan untuk mengelola data penerima MBG, data kelas, data petugas, kegiatan distribusi makanan, dokumentasi kegiatan, serta pembuatan laporan.

## 2. Teknologi yang Digunakan

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- Bootstrap (jika digunakan)
- XAMPP
- Composer

## 3. Fitur Aplikasi

1. Login Admin dan Petugas.
2. Dashboard Admin.
3. Dashboard Petugas.
4. Pengelolaan Data Kelas.
5. Pengelolaan Data Penerima MBG.
6. Pengelolaan Data Petugas.
7. Pengelolaan Kegiatan MBG.
8. Dokumentasi Kegiatan.
9. Laporan dan Filter Data.
10. Export Laporan ke PDF.

## 4. Struktur Project

```text
Capstone-Project-Kel.A/
│
├── assets/
├── uploads/
├── buat_admin.php
├── dashboard.php
├── dashboard_petugas.php
├── dokumentasi.php
├── kegiatan.php
├── kelas.php
├── koneksi.php
├── laporan.php
├── login.php
├── penerima.php
├── petugas.php
├── composer.json
├── composer.lock
├── db_mbg_sekolah.sql
└── README.md
```

## 5. Persyaratan Instalasi

Sebelum menjalankan aplikasi, siapkan:

- XAMPP
- Web Browser (Google Chrome atau sejenisnya)
- Composer (jika diperlukan oleh aplikasi)

## 6. Cara Instalasi

### Langkah 1. Menyiapkan Project

1. Download atau clone repository GitHub.
2. Ekstrak folder project.
3. Pindahkan folder `mbg-sekolah` ke dalam direktori:

```text
C:\xampp\htdocs\
```

### Langkah 2. Menjalankan XAMPP

1. Buka XAMPP Control Panel.
2. Aktifkan Apache.
3. Aktifkan MySQL.

### Langkah 3. Membuat Database

1. Buka browser.
2. Akses `http://localhost/phpmyadmin`.
3. Buat database dengan nama:

```text
db_mbg_sekolah
```

4. Pilih database tersebut.
5. Klik menu Import.
6. Pilih file `db_mbg_sekolah.sql` dari folder project.
7. Klik Import atau Go untuk memasukkan struktur dan data database.

### Langkah 4. Konfigurasi Database

1. Buka file `koneksi.php`.
2. Sesuaikan konfigurasi koneksi MySQL dengan XAMPP yang digunakan.
3. Pastikan nama database sesuai dengan `db_mbg_sekolah`.
4. Simpan perubahan.

### Langkah 5. Menjalankan Aplikasi

Buka browser dan akses alamat:

```text
http://localhost/mbg-sekolah/
```

Jika konfigurasi sudah benar, halaman aplikasi akan ditampilkan.

## 7. Database

Nama database: `db_mbg_sekolah`

File database: `db_mbg_sekolah.sql`

Database digunakan untuk menyimpan data kelas, penerima MBG, petugas, kegiatan, dokumentasi, dan laporan.

## 8. Repository GitHub

Source code dan database project dapat diakses melalui:

https://github.com/ibnuadzi6-dev/Capstone-Project-Kel.A

## 9. Penutup

Aplikasi ini dikembangkan sebagai bagian dari kegiatan Capstone Project dengan tujuan menghasilkan sistem informasi yang dapat membantu proses pendataan dan monitoring Program Makan Bergizi Gratis di lingkungan sekolah dasar.
