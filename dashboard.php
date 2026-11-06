<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['peran'] ?? '') !== 'admin') {
    header('Location: dashboard_petugas.php');
    exit;
}

require_once 'koneksi.php';

// Ambil tanggal kegiatan terbaru jika filter belum dipilih.
$resultTanggal = mysqli_query(
    $conn,
    "SELECT MAX(tanggal) AS tanggal_terbaru FROM kegiatan_mbg"
);
$rowTanggal = mysqli_fetch_assoc($resultTanggal);

$tanggalDefault = $rowTanggal['tanggal_terbaru']
    ?? date('Y-m-d');

$tanggal = $_GET['tanggal'] ?? $tanggalDefault;
$sesi = $_GET['sesi'] ?? 'Pagi';

if (!in_array($sesi, ['Pagi', 'Siang'], true)) {
    $sesi = 'Pagi';
}

// Cari kegiatan berdasarkan tanggal dan sesi.
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_kegiatan, menu_makanan, kondisi_makanan
     FROM kegiatan_mbg
     WHERE tanggal = ? AND sesi = ?
     ORDER BY id_kegiatan DESC
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, 'ss', $tanggal, $sesi);
mysqli_stmt_execute($stmt);

$hasilKegiatan = mysqli_stmt_get_result($stmt);
$kegiatan = mysqli_fetch_assoc($hasilKegiatan);

$idKegiatan = $kegiatan['id_kegiatan'] ?? 0;

// Ringkasan penerimaan guru.
$totalPorsiGuru = 0;
$totalOmprengGuru = 0;

if ($idKegiatan > 0) {
    $stmtGuru = mysqli_prepare(
        $conn,
        "SELECT
            COALESCE(SUM(jumlah_porsi_diterima), 0) AS total_porsi,
            COALESCE(SUM(jumlah_ompreng_kembali), 0) AS total_ompreng
         FROM pencatatan_guru
         WHERE id_kegiatan = ?"
    );

    mysqli_stmt_bind_param($stmtGuru, 'i', $idKegiatan);
    mysqli_stmt_execute($stmtGuru);

    $hasilGuru = mysqli_stmt_get_result($stmtGuru);
    $dataGuru = mysqli_fetch_assoc($hasilGuru);

    $totalPorsiGuru = (int) ($dataGuru['total_porsi'] ?? 0);
    $totalOmprengGuru = (int) ($dataGuru['total_ompreng'] ?? 0);

    mysqli_stmt_close($stmtGuru);
}

// Nilai awal ringkasan.
$totalKelas = 0;
$totalPorsi = 0;
$totalDiterima = 0;
$totalKembali = 0;
$totalBelumKembali = 0;
$rekap = [];

// Ambil rekap kelas untuk kegiatan terpilih.
if ($idKegiatan > 0) {
    $stmtRekap = mysqli_prepare(
        $conn,
        "SELECT
            k.id_kelas,
            k.nama_kelas,
            COALESCE(SUM(p.jumlah_porsi_diterima), 0) AS porsi,
            COALESCE(SUM(p.jumlah_ompreng_diterima), 0) AS ompreng_diterima,
            COALESCE(SUM(p.jumlah_ompreng_kembali), 0) AS ompreng_kembali
         FROM pencatatan_kelas p
         INNER JOIN kelas k ON k.id_kelas = p.id_kelas
         WHERE p.id_kegiatan = ?
         GROUP BY k.id_kelas, k.nama_kelas
         ORDER BY k.nama_kelas"
    );

    mysqli_stmt_bind_param($stmtRekap, 'i', $idKegiatan);
    mysqli_stmt_execute($stmtRekap);

    $hasilRekap = mysqli_stmt_get_result($stmtRekap);

    while ($baris = mysqli_fetch_assoc($hasilRekap)) {
        $baris['belum_kembali'] = max(
            0,
            (int) $baris['ompreng_diterima']
            - (int) $baris['ompreng_kembali']
        );

        $rekap[] = $baris;

        $totalKelas++;
        $totalPorsi += (int) $baris['porsi'];
        $totalDiterima += (int) $baris['ompreng_diterima'];
        $totalKembali += (int) $baris['ompreng_kembali'];
        $totalBelumKembali += $baris['belum_kembali'];
    }
}

function h($nilai) {
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Monitoring MBG</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>

<body class="mbg-dashboard">
<header class="mbg-header">
    <div class="mbg-shell mbg-header-inner">
        <div class="mbg-brand">
            <strong>MONITORING MBG</strong>
            <span>SDN Cimpaeun 03</span>
        </div>

        <div class="mbg-account">
            <span><?= h($_SESSION['nama'] ?? 'Admin Sekolah') ?></span>
            <a href="logout.php" class="mbg-outline-button">Keluar</a>
        </div>
    </div>

    <nav class="mbg-shell mbg-navigation" aria-label="Navigasi admin">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="kelas.php">Data Kelas</a>
        <a href="kegiatan.php">Kegiatan MBG</a>
        <a href="laporan.php">Laporan</a>
        <a href="petugas.php">Data Petugas</a>
        <a href="penerima.php">Data Penerima MBG</a>
    </nav>
</header>

<main class="mbg-shell mbg-main">
    <div class="mbg-page-heading">
        <div>
            <h1>Dashboard</h1>
            <p>Pantau penerimaan MBG dan pengembalian ompreng per kelas.</p>
        </div>
    </div>

    <form method="GET" class="mbg-panel mbg-filter">
        <label for="dashboard-tanggal">Tanggal</label>
        <input type="date"
               id="dashboard-tanggal"
               name="tanggal"
               class="form-control"
               value="<?= h($tanggal) ?>">

        <span>Sesi</span>
        <div class="mbg-session">
            <button type="submit" name="sesi" value="Pagi"
                    class="<?= $sesi === 'Pagi' ? 'selected' : '' ?>">
                Pagi
            </button>
            <button type="submit" name="sesi" value="Siang"
                    class="<?= $sesi === 'Siang' ? 'selected' : '' ?>">
                Siang
            </button>
        </div>
        <noscript><button type="submit">Tampilkan</button></noscript>
    </form>

    <section class="mbg-summary">
        <article class="mbg-summary-card">
            <h2>Kelas tercatat</h2>
            <p class="mbg-number"><?= $totalKelas ?></p>
            <small>Jumlah kelas yang memiliki pencatatan</small>
        </article>

        <article class="mbg-summary-card">
            <h2>Porsi diterima siswa</h2>
            <p class="mbg-number"><?= $totalPorsi ?></p>
            <small>Total porsi dari setiap kelas</small>
        </article>

        <article class="mbg-summary-card">
            <h2>Ompreng kembali</h2>
            <p class="mbg-number"><?= $totalKembali ?></p>
            <small>Jumlah ompreng yang dikembalikan</small>
        </article>

        <article class="mbg-summary-card mbg-warning">
            <h2>Belum kembali</h2>
            <p class="mbg-number"><?= $totalBelumKembali ?></p>
            <small>Ompreng yang perlu ditindaklanjuti</small>
        </article>
    </section>

    <div class="mbg-content-grid">
        <section class="mbg-panel mbg-recap">
            <h2>Penerimaan &amp; pengembalian per kelas</h2>
            <p class="mbg-caption">
               Rekap tanggal <?= date('d-m-Y', strtotime($tanggal)) ?>, sesi <?= h($sesi) ?>.
            </p>

            <div class="table-responsive">
                <table class="table mbg-table">
                    <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Porsi diterima</th>
                        <th>Ompreng kembali</th>
                        <th>Belum kembali</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!$kegiatan): ?>
                        <tr>
                            <td colspan="5" class="mbg-empty">
                                Belum ada kegiatan pada tanggal dan sesi ini.
                            </td>
                        </tr>
                    <?php elseif (!$rekap): ?>
                        <tr>
                            <td colspan="5" class="mbg-empty">
                                Kegiatan tersedia, tetapi pencatatan kelas belum ada.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rekap as $baris): ?>
                            <tr>
                                <td><?= h($baris['nama_kelas']) ?></td>
                                <td><?= (int) $baris['porsi'] ?></td>
                                <td><?= (int) $baris['ompreng_kembali'] ?></td>
                                <td><?= (int) $baris['belum_kembali'] ?></td>
                                <td>
                                    <?= $baris['belum_kembali'] === 0
                                        ? 'Lengkap'
                                        : 'Belum lengkap' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="mbg-side">
            <section class="mbg-panel">
                <h2>Menu kegiatan</h2>
                <p><?= $kegiatan ? nl2br(h($kegiatan['menu_makanan'] ?? '—')) : '—' ?></p>
                <span class="mbg-badge">
                    Kondisi: <?= h($kegiatan['kondisi_makanan'] ?? '—') ?>
                </span>
            </section>

            <section class="mbg-panel">
                 <h2>Penerimaan guru</h2>

                 <p class="mbg-caption">
                 Rekap penerimaan guru pada tanggal
                 <?= date('d-m-Y', strtotime($tanggal)) ?>, sesi <?= h($sesi) ?>.
                </p>

        <div class="mbg-guru-summary">
            <div>
            <span>Porsi diterima</span>
            <strong><?= $totalPorsiGuru ?></strong>
        </div>

        <div>
            <span>Ompreng kembali</span>
            <strong><?= $totalOmprengGuru ?></strong>
        </div>
        </div>
        </section>
        </aside>
    </div>

    <p class="mbg-footnote">
        Pengembalian adalah jumlah kotak makan (ompreng), bukan sisa makanan.
    </p>
</main>
</body>
</html>