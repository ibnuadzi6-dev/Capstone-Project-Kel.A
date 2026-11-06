<?php
require_once 'koneksi.php';

$nama = 'Admin Sekolah';
$username = 'admin';
$password = password_hash('Admin123!', PASSWORD_DEFAULT);
$peran = 'admin';

$sql = "INSERT INTO pengguna (nama, username, password_hash, peran)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $nama,
    $username,
    $password,
    $peran
);

if (mysqli_stmt_execute($stmt)) {
    echo "Akun admin berhasil dibuat!";
} else {
    echo "Gagal membuat akun: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
?>
