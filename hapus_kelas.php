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

if ($id <= 0) {
    die("ID kelas tidak valid.");
}

// Ambil data kelas untuk konfirmasi
$stmt = mysqli_prepare(
    $conn,
    "SELECT nama_kelas FROM kelas WHERE id_kelas = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data kelas tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM kelas WHERE id_kelas = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: kelas.php');
        exit;
    } else {
        $error = "Data kelas gagal dihapus. Data mungkin masih digunakan pada pencatatan lain.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hapus Data Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <h3>Konfirmasi Hapus Data Kelas</h3>
            <p>
                Apakah kamu yakin ingin menghapus kelas
                <strong><?= htmlspecialchars($data['nama_kelas']) ?></strong>?
            </p>
            <p class="text-danger">
                Data yang dihapus tidak dapat dikembalikan dengan mudah.
            </p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus
                </button>
                <a href="kelas.php" class="btn btn-secondary">
                    Batal
                </a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
