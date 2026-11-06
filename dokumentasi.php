<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

$pesan = '';
$error = '';

// Membuat folder penyimpanan foto jika belum tersedia
$folder = __DIR__ . '/uploads/dokumentasi/';

if (!is_dir($folder)) {
    if (!mkdir($folder, 0755, true) && !is_dir($folder)) {
        die('Folder dokumentasi gagal dibuat.');
    }
}

// Proses unggah dokumentasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kegiatan = (int) ($_POST['id_kegiatan'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($id_kegiatan <= 0) {
        $error = 'Silakan pilih kegiatan MBG.';
    } elseif (
        !isset($_FILES['foto']) ||
        $_FILES['foto']['error'] !== UPLOAD_ERR_OK
    ) {
        $error = 'Pilih foto yang akan diunggah.';
    } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
        $error = 'Ukuran foto maksimal 5 MB.';
    } else {
        $tmp = $_FILES['foto']['tmp_name'];
        $info = @getimagesize($tmp);
        $mime = $info['mime'] ?? '';

        $jenis_diizinkan = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($jenis_diizinkan[$mime])) {
            $error = 'Format foto harus JPG, PNG, atau WebP.';
        } else {
            // Pastikan kegiatan benar-benar tersedia
            $cek = mysqli_prepare(
                $conn,
                "SELECT id_kegiatan FROM kegiatan_mbg WHERE id_kegiatan = ?"
            );
            mysqli_stmt_bind_param($cek, 'i', $id_kegiatan);
            mysqli_stmt_execute($cek);
            $hasil_cek = mysqli_stmt_get_result($cek);

            if (mysqli_num_rows($hasil_cek) === 0) {
                $error = 'Kegiatan MBG tidak ditemukan.';
            } else {
                // Nama file acak agar tidak menimpa foto lain
                $nama_file = bin2hex(random_bytes(16))
                    . '.' . $jenis_diizinkan[$mime];

                $tujuan = $folder . $nama_file;
                $lokasi_db = 'uploads/dokumentasi/' . $nama_file;

                if (move_uploaded_file($tmp, $tujuan)) {
                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO dokumentasi
                         (id_kegiatan, lokasi_file, keterangan)
                         VALUES (?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        'iss',
                        $id_kegiatan,
                        $lokasi_db,
                        $keterangan
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        header('Location: dokumentasi.php?status=berhasil');
                        exit;
                    } else {
                        // Hapus file jika pencatatan database gagal
                        if (is_file($tujuan)) {
                            unlink($tujuan);
                        }
                        $error = 'Dokumentasi gagal disimpan ke database.';
                    }
                } else {
                    $error = 'Foto gagal dipindahkan ke folder penyimpanan.';
                }
            }
        }
    }
}

if (($_GET['status'] ?? '') === 'berhasil') {
    $pesan = 'Dokumentasi berhasil diunggah.';
}

// Daftar kegiatan untuk pilihan form
$query_kegiatan = mysqli_query(
    $conn,
    "SELECT id_kegiatan, tanggal, sesi, menu_makanan
     FROM kegiatan_mbg
     ORDER BY tanggal DESC"
);

// Daftar dokumentasi berdasarkan kegiatan
$id_kegiatan_filter = (int) ($_GET['id_kegiatan'] ?? 0);

$sql = "SELECT
            d.id_dokumentasi,
            d.id_kegiatan,
            d.lokasi_file,
            d.keterangan,
            km.tanggal,
            km.sesi,
            km.menu_makanan
        FROM dokumentasi d
        JOIN kegiatan_mbg km ON d.id_kegiatan = km.id_kegiatan";

if ($id_kegiatan_filter > 0) {
    $sql .= " WHERE d.id_kegiatan = $id_kegiatan_filter";
}

$sql .= " ORDER BY d.id_dokumentasi DESC";

$result = mysqli_query($conn, $sql);

if (!$query_kegiatan || !$result) {
    die('Gagal mengambil data dokumentasi.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dokumentasi Kegiatan MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=6">
</head>
<body class="kegiatan-ui dokumentasi-ui">

<header class="kg-header">
    <div class="kg-shell kg-header-top">
        <div class="kg-brand">
            <strong>MONITORING MBG</strong>
            <span>SDN Cimpaeun 03</span>
        </div>

        <div class="kg-account">
            <span>
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna MBG') ?>
            </span>
            <a href="logout.php" class="kg-button">Keluar</a>
        </div>
    </div>

    <nav class="kg-shell kg-nav" aria-label="Navigasi utama">
        <a href="<?= ($_SESSION['peran'] ?? '') === 'admin'
            ? 'dashboard.php' : 'dashboard_petugas.php' ?>">
            Dashboard
        </a>
        <a href="kegiatan.php">Kegiatan MBG</a>
        <a href="dokumentasi.php" class="active" aria-current="page">
            Dokumentasi
        </a>
    </nav>
</header>

<main class="kg-shell kg-main">
    <h1>Dokumentasi Kegiatan</h1>
    <p class="kg-subtitle">
        Foto terhubung dengan tanggal dan sesi kegiatan.
    </p>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-success" role="status">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <section class="kg-panel" aria-labelledby="judul-unggah">
        <h2 id="judul-unggah">Unggah dokumentasi</h2>

        <form method="POST" action="dokumentasi.php"
              enctype="multipart/form-data"
              class="dok-form">

            <div>
                <label for="dok-kegiatan">Kegiatan *</label>
                <select id="dok-kegiatan" name="id_kegiatan" required>
                    <option value="">Pilih kegiatan MBG</option>

                    <?php while ($k = mysqli_fetch_assoc($query_kegiatan)): ?>
                        <option value="<?= (int) $k['id_kegiatan'] ?>">
                            <?= htmlspecialchars(
                                $k['tanggal'] . ' — ' .
                                $k['sesi'] . ' — ' .
                                $k['menu_makanan']
                            ) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="dok-upload">
                <h3>Tambahkan foto pelaksanaan MBG</h3>

                <p>
                    Pilih foto penerimaan, pembagian,
                    atau pengembalian ompreng.
                </p>

                <label for="dok-foto" class="visually-hidden">
                    Foto dokumentasi *
                </label>

                <input type="file"
                       id="dok-foto"
                       name="foto"
                       accept=".jpg,.jpeg,.png,.webp"
                       aria-describedby="dok-bantuan"
                       required>

                <p id="dok-bantuan" class="dok-help">
                    Format JPG, PNG, atau WebP. Maksimal 5 MB.
                </p>
            </div>

            <div>
                <label for="dok-keterangan">Keterangan</label>
                <textarea id="dok-keterangan"
                          name="keterangan"
                          rows="2"
                          placeholder="Contoh: Penerimaan MBG sesi pagi"></textarea>
            </div>

            <div class="kg-actions">
                <button type="submit" class="kg-button kg-primary">
                    Simpan dokumentasi
                </button>

                <a href="kegiatan.php" class="kg-button">
                    Daftar kegiatan
                </a>
            </div>
        </form>
    </section>

    <section class="kg-panel dok-gallery-panel"
             aria-labelledby="judul-galeri">
        <h2 id="judul-galeri">Dokumentasi tersimpan</h2>

        <?php if ($id_kegiatan_filter > 0): ?>
            <p class="dok-filter-info">
                Menampilkan dokumentasi kegiatan yang dipilih.
                <a href="dokumentasi.php">Lihat semua dokumentasi</a>
            </p>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="dok-gallery">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <article class="dok-photo-card">
                        <a href="<?= htmlspecialchars($row['lokasi_file']) ?>"
                           target="_blank"
                           rel="noopener"
                           aria-label="Buka foto dokumentasi di tab baru">
                            <img
                                src="<?= htmlspecialchars($row['lokasi_file']) ?>"
                                alt="<?= htmlspecialchars(
                                    'Dokumentasi MBG tanggal ' .
                                    $row['tanggal'] . ', sesi ' . $row['sesi']
                                ) ?>"
                                loading="lazy">
                        </a>

                        <div class="dok-photo-info">
                            <h3>
                                <?= date('d-m-Y', strtotime($row['tanggal'])) ?>
                                 — <?= htmlspecialchars($row['sesi']) ?>
                            </h3>

                            <p>
                                <strong>Menu:</strong>
                                <?= htmlspecialchars($row['menu_makanan']) ?>
                            </p>

                            <p class="dok-caption">
                                <strong>Keterangan:</strong>
                                <?= htmlspecialchars($row['keterangan'] ?? '') ?: 'Tidak ada keterangan.' ?>
                            </p>

                            <a class="dok-photo-link"
                               href="<?= htmlspecialchars($row['lokasi_file']) ?>"
                               target="_blank"
                               rel="noopener">
                                Lihat foto lengkap ↗
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="dok-empty-title">
                <?= $id_kegiatan_filter > 0
                    ? 'Belum ada foto untuk kegiatan ini.'
                    : 'Belum ada foto dokumentasi.' ?>
            </p>
            <p class="dok-empty-help">
                Foto yang berhasil diunggah akan tampil di sini
                beserta keterangannya.
            </p>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
