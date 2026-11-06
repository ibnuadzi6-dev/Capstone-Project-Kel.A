<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['peran'] !== 'admin') {
    header('Location: dashboard_petugas.php');
    exit;
}

require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT * FROM kelas WHERE id_kelas = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data kelas tidak ditemukan.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    $tahun_ajaran = trim($_POST['tahun_ajaran'] ?? '');
    $jumlah_siswa_l = (int) ($_POST['jumlah_siswa_l'] ?? 0);
    $jumlah_siswa_p = (int) ($_POST['jumlah_siswa_p'] ?? 0);

    if ($nama_kelas === '' || $tahun_ajaran === '') {
        $error = 'Nama kelas dan tahun ajaran wajib diisi.';
    } elseif ($jumlah_siswa_l < 0 || $jumlah_siswa_p < 0) {
        $error = 'Jumlah siswa tidak boleh negatif.';
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE kelas
             SET nama_kelas = ?, tahun_ajaran = ?,
                 jumlah_siswa_l = ?, jumlah_siswa_p = ?
             WHERE id_kelas = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssiii",
            $nama_kelas,
            $tahun_ajaran,
            $jumlah_siswa_l,
            $jumlah_siswa_p,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header('Location: kelas.php');
            exit;
        } else {
            $error = 'Gagal memperbarui data kelas.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Kelas MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4">Edit Data Kelas</h2>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="card card-body">
        <div class="mb-3">
            <label class="form-label">Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control"
                   value="<?= htmlspecialchars($data['nama_kelas']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran" class="form-control"
                   value="<?= htmlspecialchars($data['tahun_ajaran']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah Siswa Laki-laki</label>
            <input type="number" name="jumlah_siswa_l" class="form-control"
                   min="0" value="<?= (int) $data['jumlah_siswa_l'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah Siswa Perempuan</label>
            <input type="number" name="jumlah_siswa_p" class="form-control"
                   min="0" value="<?= (int) $data['jumlah_siswa_p'] ?>" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="kelas.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
