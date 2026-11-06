<?php
session_start();
require_once 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

// Halaman ini khusus petugas MBG
if (($_SESSION['peran'] ?? '') !== 'petugas') {
    header('Location: dashboard.php');
    exit;
}

$nama = $_SESSION['nama'] ?? 'Petugas MBG';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Petugas MBG</title>
    <link rel="stylesheet" href="assets/css/style.css?v=13">
</head>
<body class="mbg-petugas-page">
<header class="pt-header">
    <div class="pt-shell pt-header-inner">
        <a href="dashboard_petugas.php" class="pt-brand">
            <strong>MONITORING MBG</strong>
            <span>SDN Cimpaeun 03</span>
        </a>
        <div class="pt-account">
            <span><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?></span>
            <a href="logout.php" class="pt-button pt-primary">Keluar</a>
        </div>
    </div>
    <nav class="pt-shell pt-nav" aria-label="Navigasi utama">
        <a href="dashboard_petugas.php" class="active">Dashboard</a>
        <a href="kegiatan.php">Kegiatan MBG</a>
        <a href="pencatatan.php">Pencatatan Porsi dan Ompreng</a>
        <a href="dokumentasi.php">Dokumentasi</a>
    </nav>
</header>
<main class="pt-shell pt-main">
    <section class="pt-heading">
        <h1>Dashboard Petugas</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?></strong>! Silakan pilih menu untuk melakukan pencatatan kegiatan MBG.</p>
    </section>
    <section class="pt-menu-grid" aria-label="Menu petugas">
        <a href="kegiatan.php" class="pt-menu-card">
            <div class="pt-menu-icon" aria-hidden="true">📋</div>
            <h2>Kegiatan MBG</h2>
            <p>Lihat dan kelola kegiatan MBG berdasarkan tanggal dan sesi.</p>
            <span class="pt-menu-link">Buka kegiatan →</span>
        </a>
        <a href="pencatatan.php" class="pt-menu-card">
            <div class="pt-menu-icon" aria-hidden="true">🍱</div>
            <h2>Pencatatan Porsi dan Ompreng</h2>
            <p>Catat jumlah porsi makanan yang diterima siswa serta pengembalian ompreng setiap kelas.</p>
            <span class="pt-menu-link">Mulai pencatatan →</span>
        </a>
        <a href="dokumentasi.php" class="pt-menu-card">
            <div class="pt-menu-icon" aria-hidden="true">📷</div>
            <h2>Dokumentasi</h2>
            <p>Buka halaman dokumentasi kegiatan MBG.</p>
            <span class="pt-menu-link">Buka dokumentasi →</span>
        </a>
    </section>
</main>
</body>
</html>
