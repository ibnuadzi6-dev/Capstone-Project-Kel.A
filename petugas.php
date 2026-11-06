<?php
session_start();
require_once 'koneksi.php';

// Hanya admin yang boleh mengelola akun petugas.
if (!isset($_SESSION['peran']) || $_SESSION['peran'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pesan = '';
$error = '';
$editData = null;

// Token untuk mencegah pengiriman formulir lintas situs (CSRF).
if (empty($_SESSION['csrf_petugas'])) {
    $_SESSION['csrf_petugas'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_petugas'];

function h($nilai) {
    return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_petugas'], $token)) {
        $error = 'Permintaan tidak valid. Muat ulang halaman lalu coba kembali.';
    } else {
        $aksi = $_POST['aksi'] ?? '';

        // HAPUS AKUN
        if ($aksi === 'hapus') {
            $id = filter_input(INPUT_POST, 'id_pengguna', FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                $error = 'ID akun tidak valid.';
            } else {
                $stmt = mysqli_prepare(
                    $conn,
                    "DELETE FROM pengguna WHERE id_pengguna = ? AND peran = 'petugas'"
                );

                if (!$stmt) {
                    $error = 'Terjadi kesalahan saat menyiapkan penghapusan akun.';
                } else {
                    mysqli_stmt_bind_param($stmt, 'i', $id);
                    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                        $pesan = 'Akun petugas berhasil dihapus.';
                    } else {
                        $error = 'Akun petugas tidak ditemukan atau gagal dihapus.';
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }

        // EDIT AKUN
        elseif ($aksi === 'edit') {
            $id = filter_input(INPUT_POST, 'id_pengguna', FILTER_VALIDATE_INT);
            $nama = trim($_POST['nama'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!$id || $id < 1) {
                $error = 'ID akun tidak valid.';
            } elseif ($nama === '' || $username === '') {
                $error = 'Nama dan username wajib diisi.';
            } elseif (strlen($nama) > 100 || strlen($username) > 100) {
                $error = 'Nama dan username maksimal 100 karakter.';
            } elseif ($password !== '' && strlen($password) < 8) {
                $error = 'Password baru minimal 8 karakter.';
            } else {
                // Username harus unik, selain milik akun yang sedang diedit.
                $stmt = mysqli_prepare(
                    $conn,
                    'SELECT id_pengguna FROM pengguna WHERE username = ? AND id_pengguna <> ?'
                );

                if (!$stmt) {
                    $error = 'Terjadi kesalahan saat memeriksa username.';
                } else {
                    mysqli_stmt_bind_param($stmt, 'si', $username, $id);
                    mysqli_stmt_execute($stmt);
                    $hasil = mysqli_stmt_get_result($stmt);
                    $usernameAda = mysqli_num_rows($hasil) > 0;
                    mysqli_stmt_close($stmt);

                    if ($usernameAda) {
                        $error = 'Username sudah digunakan. Silakan pilih username lain.';
                    } else {
                        if ($password !== '') {
                            $hash = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = mysqli_prepare(
                                $conn,
                                "UPDATE pengguna
                                 SET nama = ?, username = ?, password_hash = ?
                                 WHERE id_pengguna = ? AND peran = 'petugas'"
                            );
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, 'sssi', $nama, $username, $hash, $id);
                            }
                        } else {
                            $stmt = mysqli_prepare(
                                $conn,
                                "UPDATE pengguna
                                 SET nama = ?, username = ?
                                 WHERE id_pengguna = ? AND peran = 'petugas'"
                            );
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, 'ssi', $nama, $username, $id);
                            }
                        }

                        if (!$stmt) {
                            $error = 'Terjadi kesalahan saat menyiapkan perubahan akun.';
                        } else {
                            if (mysqli_stmt_execute($stmt)) {
                                $pesan = 'Data petugas berhasil diperbarui.';
                            } else {
                                $error = 'Data petugas gagal diperbarui.';
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                }
            }
        }

        // TAMBAH AKUN (perilaku lama tetap dipertahankan)
        elseif ($aksi === 'tambah') {
            $nama = trim($_POST['nama'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($nama === '' || $username === '' || $password === '') {
                $error = 'Semua kolom wajib diisi.';
            } elseif (strlen($nama) > 100 || strlen($username) > 100) {
                $error = 'Nama dan username maksimal 100 karakter.';
            } elseif (strlen($password) < 8) {
                $error = 'Password minimal 8 karakter.';
            } else {
                $stmt = mysqli_prepare($conn, 'SELECT id_pengguna FROM pengguna WHERE username = ?');
                if (!$stmt) {
                    $error = 'Terjadi kesalahan saat memeriksa username.';
                } else {
                    mysqli_stmt_bind_param($stmt, 's', $username);
                    mysqli_stmt_execute($stmt);
                    $hasil = mysqli_stmt_get_result($stmt);
                    $usernameAda = mysqli_num_rows($hasil) > 0;
                    mysqli_stmt_close($stmt);

                    if ($usernameAda) {
                        $error = 'Username sudah digunakan. Silakan pilih username lain.';
                    } else {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $peran = 'petugas';
                        $stmt = mysqli_prepare(
                            $conn,
                            'INSERT INTO pengguna (nama, username, password_hash, peran) VALUES (?, ?, ?, ?)'
                        );
                        if (!$stmt) {
                            $error = 'Terjadi kesalahan saat menyiapkan data petugas.';
                        } else {
                            mysqli_stmt_bind_param($stmt, 'ssss', $nama, $username, $hash, $peran);
                            if (mysqli_stmt_execute($stmt)) {
                                $pesan = 'Akun Petugas MBG berhasil dibuat.';
                            } else {
                                $error = 'Akun gagal dibuat. Periksa kembali data petugas.';
                            }
                            mysqli_stmt_close($stmt);
                        }
                    }
                }
            }
        }
    }
}

// Jika tombol Edit diklik, ambil data akun untuk ditampilkan pada formulir.
$idEdit = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($idEdit && $idEdit > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_pengguna, nama, username FROM pengguna
         WHERE id_pengguna = ? AND peran = 'petugas'"
    );
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $idEdit);
        mysqli_stmt_execute($stmt);
        $hasilEdit = mysqli_stmt_get_result($stmt);
        $editData = mysqli_fetch_assoc($hasilEdit) ?: null;
        mysqli_stmt_close($stmt);
    }
    if (!$editData && $error === '') {
        $error = 'Data petugas yang akan diedit tidak ditemukan.';
    }
}

// Ambil daftar akun petugas.
$daftar = mysqli_query(
    $conn,
    "SELECT id_pengguna, nama, username, peran
     FROM pengguna
     WHERE peran = 'petugas'
     ORDER BY nama ASC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Petugas MBG</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/css/style.css?v=6">

    <style>
        /* Jarak dan tata letak khusus halaman petugas */
        .pt-main {
            padding-top: 34px;
            padding-bottom: 48px;
        }

        .pt-heading {
            margin-bottom: 28px;
        }

        .pt-heading h1 {
            margin-bottom: 8px;
        }

        .pt-subtitle {
            margin-bottom: 0;
        }

        .pt-panel {
            background: #fff;
            border: 1px solid #dce7df;
            border-radius: 18px;
            padding: 28px 32px;
            margin-bottom: 28px;
        }

        .pt-panel h2 {
            font-size: 23px;
            font-weight: 700;
            margin-bottom: 24px;
        }

        .pt-field {
            margin-bottom: 22px;
        }

        .pt-field label {
            display: block;
            font-weight: 500;
            margin-bottom: 9px;
        }

        .pt-field input {
            width: 100%;
            min-height: 48px;
            padding: 11px 14px;
            border: 1px solid #d7e0da;
            border-radius: 10px;
            box-sizing: border-box;
        }

        .pt-field input:focus {
            outline: none;
            border-color: #17643b;
            box-shadow: 0 0 0 3px rgba(23, 100, 59, 0.12);
        }

        .pt-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .pt-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 11px 22px;
            border: 1px solid #dce7df;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            font: inherit;
        }

        .pt-button-primary {
            color: #fff;
            background: #17643b;
            border-color: #17643b;
        }

        .pt-button-primary:hover {
            color: #fff;
            background: #104d2c;
        }

        .pt-button-secondary {
            color: #263c31;
            background: #fff;
        }

        .pt-button-secondary:hover {
            background: #f3f7f4;
        }

        .pt-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .pt-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 0;
        }

        .pt-table thead th {
            padding: 12px 16px;
            font-weight: 700;
            white-space: nowrap;
            border: none;
        }

        .pt-table tbody td {
            padding: 17px 16px;
            vertical-align: middle;
            background: #fff;
            border-top: 1px solid #dce7df;
            border-bottom: 1px solid #dce7df;
        }

        .pt-table tbody td:first-child {
            border-left: 1px solid #dce7df;
            border-radius: 10px 0 0 10px;
        }

        .pt-table tbody td:last-child {
            border-right: 1px solid #dce7df;
            border-radius: 0 10px 10px 0;
        }

        .pt-role {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            background: #e8f5ec;
            color: #17643b;
            font-size: 14px;
            font-weight: 600;
        }

        .pt-note {
            color: #687b70;
            margin-top: 18px;
            margin-bottom: 0;
        }

        @media (max-width: 576px) {
            .pt-main {
                padding-top: 22px;
            }

            .pt-panel {
                padding: 20px 16px;
                margin-bottom: 20px;
                border-radius: 14px;
            }

            .pt-panel h2 {
                font-size: 20px;
            }

            .pt-table thead th,
            .pt-table tbody td {
                padding: 12px;
            }
        }
    </style>
</head>

<body class="kegiatan-ui">

<header class="kg-header">
    <div class="kg-shell kg-header-top">
        <div class="kg-brand">
            <strong>MONITORING MBG</strong>
            <span>SDN Cimpaeun 03</span>
        </div>

        <div class="kg-account">
            <span>
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin Sekolah') ?>
            </span>
            <a href="logout.php" class="kg-button">Keluar</a>
        </div>
    </div>

    <nav class="kg-shell kg-nav" aria-label="Navigasi utama">
        <a href="dashboard.php">Dashboard</a>
        <a href="kelas.php">Data Kelas</a>
        <a href="kegiatan.php">Kegiatan MBG</a>
        <a href="dokumentasi.php">Dokumentasi</a>
        <a href="laporan.php">Laporan</a>
        <a href="petugas.php" class="active" aria-current="page">Data Petugas</a>
        <a href="penerima.php">Data Penerima MBG</a>
    </nav>
</header>

<main class="kg-shell pt-main">

    <div class="pt-heading">
        <h1>Pengelolaan Pengguna</h1>
        <p class="pt-subtitle">
            Kelola akun Petugas MBG.
        </p>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Panel daftar petugas -->
    <section class="pt-panel">
        <h2>Daftar akun</h2>

        <div class="pt-table-wrap">
            <table class="pt-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($daftar && mysqli_num_rows($daftar) > 0): ?>
                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($daftar)): ?>
                            <tr>
                                <td><?= $no++ ?></td>

                                <td>
                                    <?= htmlspecialchars($row['nama']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['username']) ?>
                                </td>

                                <td>
                                    <span class="pt-role">
                                        <?= h($row['peran']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="pt-actions">
                                        <a class="pt-button pt-button-secondary"
                                           href="petugas.php?edit=<?= (int)$row['id_pengguna'] ?>#form-petugas">
                                            Edit
                                        </a>
                                        <form method="POST" action="petugas.php"
                                              onsubmit="return confirm('Yakin ingin menghapus akun petugas ini? Tindakan ini tidak dapat dibatalkan.');"
                                              style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                                            <input type="hidden" name="aksi" value="hapus">
                                            <input type="hidden" name="id_pengguna" value="<?= (int)$row['id_pengguna'] ?>">
                                            <button type="submit" class="pt-button"
                                                    style="background:#b42318;color:#fff;border-color:#b42318;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                Belum ada akun petugas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="pt-note">
            Kata sandi tidak ditampilkan demi keamanan akun.
        </p>
    </section>

    <!-- Panel tambah petugas -->
    <section class="pt-panel" id="form-petugas">
        <h2><?= $editData ? 'Edit akun Petugas' : 'Tambah akun Petugas' ?></h2>

        <form method="POST" action="petugas.php">
            <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
            <input type="hidden" name="aksi" value="<?= $editData ? 'edit' : 'tambah' ?>">
            <?php if ($editData): ?>
                <input type="hidden" name="id_pengguna" value="<?= (int)$editData['id_pengguna'] ?>">
            <?php endif; ?>
            <div class="pt-field">
                <label for="nama">Nama petugas</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="<?= h($editData['nama'] ?? ($_POST['nama'] ?? '')) ?>"
                    maxlength="100"
                    required
                >
            </div>

            <div class="pt-field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= h($editData['username'] ?? ($_POST['username'] ?? '')) ?>"
                    maxlength="100"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="pt-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    autocomplete="new-password"
                    <?= $editData ? '' : 'required' ?>
                >
                <small class="text-muted">
                    <?php if ($editData): ?>
                        Kosongkan jika password tidak ingin diubah. Jika diganti, minimal 8 karakter.
                    <?php else: ?>
                        Password minimal 8 karakter.
                    <?php endif; ?>
                </small>
            </div>

            <div class="pt-actions">
                <button type="submit" class="pt-button pt-button-primary">
                    <?= $editData ? 'Simpan Perubahan' : 'Simpan Petugas' ?>
                </button>

                <?php if ($editData): ?>
                    <a href="petugas.php" class="pt-button pt-button-secondary">Batal Edit</a>
                <?php endif; ?>

                <a href="dashboard.php" class="pt-button pt-button-secondary">
                    Kembali ke Dashboard
                </a>
            </div>
        </form>
    </section>

</main>

</body>
</html>