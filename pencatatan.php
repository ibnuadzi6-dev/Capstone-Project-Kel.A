<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}
require_once 'koneksi.php';

$pesan = '';
$error = '';

$id_edit = (int) ($_GET['edit'] ?? 0);
$data_edit = null;

if ($id_edit > 0) {
    $stmt_edit = mysqli_prepare(
        $conn,
        "SELECT * FROM pencatatan_kelas WHERE id_pencatatan = ?"
    );

    mysqli_stmt_bind_param($stmt_edit, "i", $id_edit);
    mysqli_stmt_execute($stmt_edit);

    $hasil_edit = mysqli_stmt_get_result($stmt_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    mysqli_stmt_close($stmt_edit);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    // Hapus hanya melalui POST dan query terparameterisasi.
    $id_hapus = (int) ($_POST['id_pencatatan'] ?? 0);

    if ($id_hapus > 0) {
        $stmt_hapus = mysqli_prepare(
            $conn,
            "DELETE FROM pencatatan_kelas WHERE id_pencatatan = ?"
        );

        if ($stmt_hapus) {
            mysqli_stmt_bind_param($stmt_hapus, "i", $id_hapus);
            if (mysqli_stmt_execute($stmt_hapus) && mysqli_stmt_affected_rows($stmt_hapus) > 0) {
                $pesan = 'Data pencatatan berhasil dihapus.';
            } else {
                $error = 'Data pencatatan tidak ditemukan atau gagal dihapus.';
            }
            mysqli_stmt_close($stmt_hapus);
        } else {
            $error = 'Terjadi kesalahan saat menyiapkan proses hapus.';
        }
    } else {
        $error = 'ID pencatatan tidak valid.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pencatatan = (int) ($_POST['id_pencatatan'] ?? 0);
    $id_kegiatan = (int) ($_POST['id_kegiatan'] ?? 0);
    $id_kelas = (int) ($_POST['id_kelas'] ?? 0);
    $jumlah_porsi = (int) ($_POST['jumlah_porsi_diterima'] ?? 0);
    $ompreng_diterima = (int) ($_POST['jumlah_ompreng_diterima'] ?? 0);
    $ompreng_kembali = (int) ($_POST['jumlah_ompreng_kembali'] ?? 0);
    $pj_penerimaan = trim($_POST['pj_penerimaan'] ?? '');
    $pj_pengembalian = trim($_POST['pj_pengembalian'] ?? '');

    if (
        $id_kegiatan <= 0 ||
        $id_kelas <= 0 ||
        $jumlah_porsi < 0 ||
        $ompreng_diterima < 0 ||
        $ompreng_kembali < 0
    ) {
        $error = 'Periksa kembali data yang dimasukkan.';
    } elseif ($ompreng_kembali > $ompreng_diterima) {
        $error = 'Ompreng yang dikembalikan tidak boleh melebihi jumlah yang diterima.';
    } else {
        if ($id_pencatatan > 0) {
            $sql = "UPDATE pencatatan_kelas
                    SET id_kegiatan = ?, id_kelas = ?,
                        jumlah_porsi_diterima = ?,
                        jumlah_ompreng_diterima = ?,
                        jumlah_ompreng_kembali = ?,
                        pj_penerimaan = ?, pj_pengembalian = ?
                    WHERE id_pencatatan = ?";
        } else {
            $sql = "INSERT INTO pencatatan_kelas
                    (id_kegiatan, id_kelas, jumlah_porsi_diterima,
                     jumlah_ompreng_diterima, jumlah_ompreng_kembali,
                     pj_penerimaan, pj_pengembalian)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        }

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            if ($id_pencatatan > 0) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiiiissi",
                    $id_kegiatan,
                    $id_kelas,
                    $jumlah_porsi,
                    $ompreng_diterima,
                    $ompreng_kembali,
                    $pj_penerimaan,
                    $pj_pengembalian,
                    $id_pencatatan
                );
            } else {
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiiiiss",
                    $id_kegiatan,
                    $id_kelas,
                    $jumlah_porsi,
                    $ompreng_diterima,
                    $ompreng_kembali,
                    $pj_penerimaan,
                    $pj_pengembalian
                );
            }

            if (mysqli_stmt_execute($stmt)) {
                $pesan = $id_pencatatan > 0
                    ? 'Pencatatan berhasil diperbarui.'
                    : 'Pencatatan berhasil disimpan.';
            } else {
                $error = 'Data gagal disimpan atau diperbarui.';
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = 'Terjadi kesalahan saat menyiapkan query database.';
        }
    }
}

$kegiatan = mysqli_query(
    $conn,
    "SELECT id_kegiatan, tanggal, sesi
     FROM kegiatan_mbg
     ORDER BY tanggal ASC, sesi ASC"
);

$kelas = mysqli_query(
    $conn,
    "SELECT id_kelas, nama_kelas
     FROM kelas
     ORDER BY nama_kelas ASC"
);

$data = mysqli_query(
    $conn,
    "SELECT pk.*, k.nama_kelas, km.tanggal, km.sesi
    FROM pencatatan_kelas pk
    JOIN kelas k ON pk.id_kelas = k.id_kelas
    JOIN kegiatan_mbg km ON pk.id_kegiatan = km.id_kegiatan
    ORDER BY km.tanggal DESC,
    km.sesi ASC,
    CAST(k.nama_kelas AS UNSIGNED) ASC,
    k.nama_kelas ASC"
);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pencatatan Porsi dan Ompreng MBG</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Penyesuaian khusus halaman pencatatan agar ukurannya konsisten dengan kegiatan.php */
        .mbg-pencatatan-page {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #172b24;
            background: #f5f7f5;
        }
        .mbg-pencatatan-page *,
        .mbg-pencatatan-page *::before,
        .mbg-pencatatan-page *::after { box-sizing: border-box; }
        .mbg-pencatatan-page .mbg-shell {
            width: 100%;
            max-width: 1526px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 5%;
            padding-right: 5%;
        }
        .mbg-pencatatan-page .mbg-header { width: 100%; }
        .mbg-pencatatan-page .mbg-header-inner {
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding-top: 16px;
            padding-bottom: 16px;
        }
        .mbg-pencatatan-page .mbg-navigation {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 32px;
            padding-top: 16px;
            padding-bottom: 20px;
        }
        .mbg-pencatatan-page .mbg-main {
            padding-top: 28px;
            padding-bottom: 48px;
        }
        .mbg-pencatatan-page .mbg-page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
            margin-bottom: 24px;
        }
        .mbg-pencatatan-page .mbg-page-heading h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.25;
            font-weight: 700;
        }
        .mbg-pencatatan-page .pc-eyebrow {
            margin: 0 0 8px;
            color: #66776e;
            font-size: 15px;
        }
        .mbg-pencatatan-page .pc-description {
            max-width: 460px;
            margin: 0;
            color: #66776e;
            font-size: 15px;
            text-align: right;
        }
        .mbg-pencatatan-page .mbg-panel {
            width: 100%;
            min-width: 0;
            padding: 28px 32px;
            margin-bottom: 24px;
            background: #fff;
            border: 1px solid #dee6df;
            border-radius: 16px;
        }
        .mbg-pencatatan-page .pc-panel-heading { margin-bottom: 22px; }
        .mbg-pencatatan-page .pc-panel-heading h2 {
            margin: 0 0 8px;
            font-size: 22px;
            line-height: 1.35;
        }
        .mbg-pencatatan-page .pc-panel-heading p {
            margin: 0;
            color: #66776e;
            font-size: 14px;
        }
        .mbg-pencatatan-page .pc-form { display: grid; gap: 20px; margin: 0; }
        .mbg-pencatatan-page .pc-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px 32px;
        }
        .mbg-pencatatan-page .pc-field { min-width: 0; }
        .mbg-pencatatan-page .pc-field label {
            display: block;
            margin-bottom: 8px;
            color: #172b24;
            font-size: 14px;
            font-weight: 500;
        }
        .mbg-pencatatan-page .pc-field input,
        .mbg-pencatatan-page .pc-field select {
            display: block;
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            background: #fff;
            border: 1px solid #d9dfda;
            border-radius: 8px;
            color: #172b24;
            font: inherit;
        }
        .mbg-pencatatan-page .pc-form-actions { margin-top: 4px; display:flex; gap:12px; flex-wrap:wrap; }
        .mbg-pencatatan-page .pc-list { margin-top: 24px; }
        .mbg-pencatatan-page .pc-table-wrap { width:100%; overflow-x:auto; }
        .mbg-pencatatan-page .pc-table { width:100%; min-width:1000px; border-collapse:collapse; }
        .mbg-pencatatan-page .pc-table th {
            padding: 14px 16px;
            background: #eaf4ed;
            color: #166534;
            text-align: left;
            border-bottom: 3px solid #166534;
            font-size: 14px;
        }
        .mbg-pencatatan-page .pc-table td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #e5ebe6;
            font-size: 14px;
        }
        .mbg-pencatatan-page .pc-table tbody tr.pc-date-separator td { border-top: 3px solid #166534; }
        .mbg-pencatatan-page .pc-nowrap { white-space: nowrap; }
        .mbg-pencatatan-page .pc-edit-button {
            display:inline-block;
            min-height:36px;
            padding:6px 16px;
            color:#166534;
            background:#fff;
            border:1px solid #d9dfda;
            border-radius:8px;
            text-decoration:none;
        }
        @media (max-width: 700px) {
            .mbg-pencatatan-page .mbg-shell { padding-left: 18px; padding-right: 18px; }
            .mbg-pencatatan-page .mbg-page-heading { align-items:flex-start; flex-direction:column; gap:8px; }
            .mbg-pencatatan-page .pc-description { text-align:left; }
            .mbg-pencatatan-page .pc-grid { grid-template-columns:1fr; gap:20px; }
            .mbg-pencatatan-page .mbg-panel { padding:22px 18px; }
            .mbg-pencatatan-page .mbg-navigation { gap:16px; }
        }
    </style>
<style>
.mbg-pencatatan-page .pc-row-actions { display:flex; align-items:center; gap:8px; }
.mbg-pencatatan-page .pc-delete-form { display:inline; margin:0; }
.mbg-pencatatan-page .pc-delete-button { min-height:36px; padding:6px 14px; border:1px solid #f0caca; border-radius:8px; background:#fff; color:#b42318; font:inherit; cursor:pointer; }
.mbg-pencatatan-page .pc-delete-button:hover { background:#fff1f0; border-color:#d92d20; }
@media (max-width: 600px) { .mbg-pencatatan-page .pc-row-actions { flex-wrap:wrap; } }
</style>

</head>
<body class="mbg-pencatatan-page">
    <header class="mbg-header">
        <div class="mbg-shell mbg-header-inner">
            <div class="mbg-brand">
                <strong>MONITORING MBG</strong>
                <span>SDN Cimpaeun 03</span>
            </div>
            <div class="mbg-account">
                <span><?= htmlspecialchars($_SESSION['nama_pengguna'] ?? $_SESSION['nama'] ?? 'Petugas') ?></span>
                <a href="logout.php" class="mbg-outline-button">Keluar</a>
            </div>
        </div>
        <nav class="mbg-shell mbg-navigation" aria-label="Navigasi utama">
            <a href="dashboard_petugas.php">Dashboard</a>
            <a href="kegiatan.php">Kegiatan MBG</a>
            <a href="pencatatan.php" class="active" aria-current="page">Pencatatan Porsi dan Ompreng</a>
            <a href="dokumentasi.php">Dokumentasi</a>
        </nav>
    </header>

    <main class="mbg-shell mbg-main">
        <header class="mbg-page-heading">
            <div>
                <p class="pc-eyebrow">Program Makan Bergizi Gratis</p>
                <h1>Pencatatan Porsi dan Ompreng</h1>
            </div>
            <p class="pc-description">Catat jumlah porsi yang diterima dan pengembalian ompreng setiap kelas.</p>
        </header>

        <?php if ($pesan !== ''): ?>
            <div class="pc-alert pc-alert-success"><?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <div class="pc-alert pc-alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <section class="mbg-panel">
            <div class="pc-panel-heading">
                <h2><?= $data_edit ? 'Edit Pencatatan' : 'Tambah Pencatatan' ?></h2>
                <p>Lengkapi informasi penerimaan porsi dan pengembalian ompreng.</p>
            </div>

            <form method="POST" class="pc-form">
                <input type="hidden" name="id_pencatatan" value="<?= $data_edit ? (int) $data_edit['id_pencatatan'] : 0 ?>">

                <div class="pc-grid">
                    <div class="pc-field">
                        <label for="id_kegiatan">Kegiatan MBG</label>
                        <select id="id_kegiatan" name="id_kegiatan" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            <?php while ($k = mysqli_fetch_assoc($kegiatan)): ?>
                                <option value="<?= (int) $k['id_kegiatan'] ?>" <?= ($data_edit && (int) $data_edit['id_kegiatan'] === (int) $k['id_kegiatan']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(date('d-m-Y', strtotime($k['tanggal'])) . ' - ' . $k['sesi']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="pc-field">
                        <label for="id_kelas">Kelas</label>
                        <select id="id_kelas" name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                                <option value="<?= (int) $k['id_kelas'] ?>" <?= ($data_edit && (int) $data_edit['id_kelas'] === (int) $k['id_kelas']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_kelas']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="pc-field">
                        <label for="jumlah_porsi_diterima">Jumlah Porsi Diterima</label>
                        <input id="jumlah_porsi_diterima" type="number" name="jumlah_porsi_diterima" min="0" value="<?= $data_edit ? (int) $data_edit['jumlah_porsi_diterima'] : 0 ?>" required>
                    </div>

                    <div class="pc-field">
                        <label for="jumlah_ompreng_diterima">Jumlah Ompreng Diterima</label>
                        <input id="jumlah_ompreng_diterima" type="number" name="jumlah_ompreng_diterima" min="0" value="<?= $data_edit ? (int) $data_edit['jumlah_ompreng_diterima'] : 0 ?>" required>
                    </div>

                    <div class="pc-field">
                        <label for="jumlah_ompreng_kembali">Jumlah Ompreng Dikembalikan</label>
                        <input id="jumlah_ompreng_kembali" type="number" name="jumlah_ompreng_kembali" min="0" value="<?= $data_edit ? (int) $data_edit['jumlah_ompreng_kembali'] : 0 ?>" required>
                    </div>

                    <div class="pc-field">
                        <label for="pj_penerimaan">Penanggung Jawab Penerimaan</label>
                        <input id="pj_penerimaan" type="text" name="pj_penerimaan" placeholder="Masukkan nama penanggung jawab" value="<?= htmlspecialchars($data_edit['pj_penerimaan'] ?? '') ?>">
                    </div>

                    <div class="pc-field">
                        <label for="pj_pengembalian">Penanggung Jawab Pengembalian</label>
                        <input id="pj_pengembalian" type="text" name="pj_pengembalian" placeholder="Masukkan nama penanggung jawab" value="<?= htmlspecialchars($data_edit['pj_pengembalian'] ?? '') ?>">
                    </div>
                </div>

                <div class="pc-form-actions">
                    <button type="submit" class="mbg-button mbg-button-primary pc-button pc-button-primary"><?= $data_edit ? 'Update Pencatatan' : 'Simpan Pencatatan' ?></button>
                </div>
            </form>
        </section>

        <section class="mbg-panel pc-list pc-table-panel">
            <div class="pc-panel-heading">
                <h2>Daftar Pencatatan</h2>
                <p>Data penerimaan porsi dan pengembalian ompreng per kelas.</p>
            </div>

            <div class="pc-table-wrap">
                <table class="pc-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Sesi</th>
                            <th>Kelas</th>
                            <th>Porsi Diterima</th>
                            <th>Ompreng Diterima</th>
                            <th>Ompreng Kembali</th>
                            <th>Belum Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($data && mysqli_num_rows($data) > 0): ?>
                            <?php
                            $no = 1;
                            $tanggal_sebelumnya = '';
                            ?>
                            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                                <?php $tanggal_baru = ($row['tanggal'] !== $tanggal_sebelumnya); ?>
                                <tr class="<?= $tanggal_baru ? 'pc-date-separator' : '' ?>">
                                    <td><?= $no++ ?></td>
                                    <td class="pc-nowrap"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['sesi'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                    <td><?= (int) $row['jumlah_porsi_diterima'] ?></td>
                                    <td><?= (int) $row['jumlah_ompreng_diterima'] ?></td>
                                    <td><?= (int) $row['jumlah_ompreng_kembali'] ?></td>
                                    <td><?= max(0, (int) $row['jumlah_ompreng_diterima'] - (int) $row['jumlah_ompreng_kembali']) ?></td>
                                    <td>
                                        <div class="pc-row-actions">
                                            <a href="pencatatan.php?edit=<?= (int) $row['id_pencatatan'] ?>" class="pc-button pc-edit-button">Edit</a>
                                            <form method="POST" class="pc-delete-form" onsubmit="return confirm('Yakin ingin menghapus pencatatan kelas <?= htmlspecialchars($row['nama_kelas'], ENT_QUOTES) ?> tanggal <?= date('d-m-Y', strtotime($row['tanggal'])) ?>? Data yang dihapus tidak dapat dikembalikan.');">
                                                <input type="hidden" name="aksi" value="hapus">
                                                <input type="hidden" name="id_pencatatan" value="<?= (int) $row['id_pencatatan'] ?>">
                                                <button type="submit" class="pc-button pc-delete-button">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php $tanggal_sebelumnya = $row['tanggal']; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="pc-empty">Belum ada data pencatatan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>