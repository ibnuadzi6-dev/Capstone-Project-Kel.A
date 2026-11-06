<?php
session_start();
require_once 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT id_pengguna, nama, username, password_hash, peran
            FROM pengguna
            WHERE username = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($hasil);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['peran'] = $user['peran'];

            mysqli_stmt_close($stmt);

            if ($user['peran'] === 'admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: dashboard_petugas.php');
            }
            exit;
        } else {
            $error = 'Username atau password salah.';
        }

        mysqli_stmt_close($stmt);
    } else {
        $error = 'Terjadi kesalahan saat memproses login. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Monitoring MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=6">
    <style>
        .login-page {
            min-height: 100vh;
            background-image:
                linear-gradient(
                   rgba(245, 247, 245, 0.30),
                   rgba(245, 247, 245, 0.30)
                ),
                url("assets/img/sekolah.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
          }
        
        .login-wrapper {
            width: 100%;
            min-height: 100vh;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 26px 28px;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .login-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 12px;
        }
        .login-header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }
        .login-header p {
            margin-bottom: 20px;
        }
        .login-description {
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .login-form .form-control {
            min-height: 44px;
            font-size: 15px;
        }
        .login-button {
            width: 100%;
            min-height: 44px;
            font-size: 16px;
        }
        .login-help {
            margin-top: 16px;
            margin-bottom: 0;
            font-size: 13px;
        }
        .login-footer {
            margin-top: 12px;
            text-align: center;
            font-size: 12px;
        }
        @media (max-width: 576px) {
            .login-card {
                padding: 24px 20px;
                border-radius: 20px;
            }
            .login-header h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body class="login-page">
    <main class="login-wrapper">
        <section class="login-card" aria-labelledby="login-title">
            <header class="login-header">
                <div class="login-logo" aria-hidden="true">M</div>
                <h1 id="login-title">Monitoring MBG</h1>
                <p>SDN Cimpaeun 03</p>
            </header>

            <p class="login-description">
                Masuk untuk mengelola pencatatan dan pemantauan kegiatan MBG.
            </p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div>
                    <label for="username" class="form-label">Username</label>
                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn login-button">Masuk</button>
            </form>

            <p class="login-help">Gunakan akun yang diberikan admin sekolah.</p>
        </section>

        <p class="login-footer">Sistem Monitoring dan Pendataan Makan Bergizi Gratis</p>
    </main>
</body>
</html>
