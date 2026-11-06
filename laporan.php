<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

// Batasi halaman laporan hanya untuk admin
if (($_SESSION['peran'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

// Mengambil filter tanggal dari form. Mendukung format date (YYYY-MM-DD)
// dari frontend Ilhan dan dd/mm/yyyy jika dikirim dari form lama.
$tanggal_awal_input = trim($_GET['tanggal_awal'] ?? '');
$tanggal_akhir_input = trim($_GET['tanggal_akhir'] ?? '');
$id_kelas = $_GET['id_kelas'] ?? '';

function normalisasiTanggalLaporan($input) {
    if ($input === '') {
        return '';
    }

    $tanggal = DateTime::createFromFormat('!Y-m-d', $input);
    if ($tanggal && $tanggal->format('Y-m-d') === $input) {
        return $tanggal->format('Y-m-d');
    }

    $tanggal = DateTime::createFromFormat('!d/m/Y', $input);
    if ($tanggal && $tanggal->format('d/m/Y') === $input) {
        return $tanggal->format('Y-m-d');
    }

    return '';
}

$tanggal_awal = normalisasiTanggalLaporan($tanggal_awal_input);
$tanggal_akhir = normalisasiTanggalLaporan($tanggal_akhir_input);

// Mengambil daftar kelas untuk dropdown
$query_kelas = mysqli_query(
    $conn,
    "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC"
);

// Menyusun query laporan
$sql = "SELECT
            pk.id_pencatatan,
            k.nama_kelas,
            km.tanggal,
            km.sesi,
            km.menu_makanan,
            pk.jumlah_porsi_diterima,
            pk.jumlah_ompreng_diterima,
            pk.jumlah_ompreng_kembali,
            pk.pj_penerimaan,
            pk.pj_pengembalian
        FROM pencatatan_kelas pk
        JOIN kelas k ON pk.id_kelas = k.id_kelas
        JOIN kegiatan_mbg km ON pk.id_kegiatan = km.id_kegiatan
        WHERE 1=1";

$params = [];
$types = '';

if ($tanggal_awal !== '' && $tanggal_akhir === '') {
    // Jika hanya tanggal awal yang diisi, tampilkan tanggal itu saja
    $sql .= " AND km.tanggal = ?";
    $params[] = $tanggal_awal;
    $types .= 's';
}

if ($tanggal_awal !== '' && $tanggal_akhir !== '') {
    // Jika kedua tanggal diisi, tampilkan data dalam rentang tanggal
    $sql .= " AND km.tanggal BETWEEN ? AND ?";
    $params[] = $tanggal_awal;
    $params[] = $tanggal_akhir;
    $types .= 'ss';
}

if ($tanggal_awal === '' && $tanggal_akhir !== '') {
    // Jika hanya tanggal akhir yang diisi, tampilkan tanggal itu saja
    $sql .= " AND km.tanggal = ?";
    $params[] = $tanggal_akhir;
    $types .= 's';
}

if ($id_kelas !== '') {
    $sql .= " AND pk.id_kelas = ?";
    $params[] = (int) $id_kelas;
    $types .= 'i';
}

$sql .= " ORDER BY
    km.tanggal DESC,
    km.sesi ASC,
    CAST(k.nama_kelas AS UNSIGNED) ASC,
    k.nama_kelas ASC";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die('Gagal menyiapkan laporan.');
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$query_kelas || !$result) {
    die('Gagal mengambil data laporan.');
}

$dashboard_link = (($_SESSION['peran'] ?? '') === 'admin')
    ? 'dashboard.php'
    : 'dashboard_petugas.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Monitoring MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=8">
    <style>
    .report-table {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .report-table thead th {
        padding: 14px 10px;
        background-color: #d1e7dd;
        vertical-align: middle;
    }

    .report-table tbody td {
        padding: 14px 10px;
        background-color: #ffffff;
        vertical-align: middle;
        border-top: 1px solid #e1e8e3;
        border-bottom: 1px solid #e1e8e3;
    }

    .report-table tbody td:first-child {
        border-left: 1px solid #e1e8e3;
        border-radius: 6px 0 0 6px;
    }

    .report-table tbody td:last-child {
        border-right: 1px solid #e1e8e3;
        border-radius: 0 6px 6px 0;
    }

    .report-table .kolom-tanggal {
        white-space: nowrap;
        min-width: 105px;
    }

    @media print {
        .report-table {
            border-spacing: 0;
        }
    }
</style>
</head>
<body class="kegiatan-ui laporan-ui">

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

        <?php if (($_SESSION['peran'] ?? '') === 'admin'): ?>
            <a href="kelas.php">Data Kelas</a>
        <?php endif; ?>

        <a href="kegiatan.php">Kegiatan MBG</a>
        <a href="dokumentasi.php">Dokumentasi</a>

        <a href="laporan.php" class="active" aria-current="page">
            Laporan
        </a>

        <?php if (($_SESSION['peran'] ?? '') === 'admin'): ?>
            <a href="petugas.php">Data Petugas</a>
            <a href="penerima.php">Data Penerima MBG</a>
            
        <?php endif; ?>
    </nav>
</header>

<main class="kg-shell kg-main">
    <h1>Laporan MBG</h1>
    <p class="kg-subtitle">
        Tinjau penerimaan porsi dan pengembalian ompreng per kelas.
    </p>

    <section class="kg-panel lp-filter-panel"
             aria-labelledby="judul-filter">
        <h2 id="judul-filter">Filter laporan</h2>

        <form method="GET" action="laporan.php">
            <div class="lp-filter-grid">
                <div>
                    <label for="tanggal-awal">Tanggal awal</label>
                    <input type="date"
                           id="tanggal-awal"
                           name="tanggal_awal"
                           value="<?= htmlspecialchars($tanggal_awal) ?>">
                </div>

                <div>
                    <label for="tanggal-akhir">Tanggal akhir</label>
                    <input type="date"
                           id="tanggal-akhir"
                           name="tanggal_akhir"
                           value="<?= htmlspecialchars($tanggal_akhir) ?>">
                </div>

                <div>
                    <label for="filter-kelas">Kelas</label>
                    <select id="filter-kelas" name="id_kelas">
                        <option value="">Semua kelas</option>

                        <?php while ($kelas = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= (int) $kelas['id_kelas'] ?>"
                                <?= (string) $id_kelas === (string) $kelas['id_kelas']
                                    ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kelas['nama_kelas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="kg-actions lp-filter-actions">
                <button type="submit" class="kg-button kg-primary">
                    Tampilkan laporan
                </button>
                <a href="laporan.php" class="kg-button">Reset</a>
            </div>
        </form>
    </section>

    <section class="kg-panel lp-results"
             aria-labelledby="judul-pencatatan">
        <div class="lp-results-heading">
            <h2 id="judul-pencatatan">Pencatatan siswa per kelas</h2>
            <span class="lp-count">
                <?= (int) mysqli_num_rows($result) ?> pencatatan
            </span>
        </div>

        <p class="lp-period">
            Periode:
            <?= $tanggal_awal !== ''
                ? date('d-m-Y', strtotime($tanggal_awal)) : 'Semua tanggal awal' ?>
            sampai
            <?= $tanggal_akhir !== ''
                ? date('d-m-Y', strtotime($tanggal_akhir)) : 'Semua tanggal akhir' ?>
        </p>

        <div class="lp-table-wrap" tabindex="0"
             role="region" aria-label="Tabel laporan MBG">
            <table class="lp-table">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Sesi</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Menu makanan</th>
                        <th scope="col">Porsi diterima</th>
                        <th scope="col">Ompreng diterima</th>
                        <th scope="col">Ompreng kembali</th>
                        <th scope="col">Belum kembali</th>
                        <th scope="col">PJ penerimaan</th>
                        <th scope="col">PJ pengembalian</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($row['sesi']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                <td class="lp-menu">
                                    <?= nl2br(htmlspecialchars($row['menu_makanan'])) ?>
                                </td>
                                <td class="lp-number">
                                    <?= (int) $row['jumlah_porsi_diterima'] ?>
                                </td>
                                <td class="lp-number">
                                    <?= (int) $row['jumlah_ompreng_diterima'] ?>
                                </td>
                                <td class="lp-number">
                                    <?= (int) $row['jumlah_ompreng_kembali'] ?>
                                </td>
                                <td class="lp-number">
                                    <?= max(
                                        0,
                                        (int) $row['jumlah_ompreng_diterima']
                                        - (int) $row['jumlah_ompreng_kembali']
                                    ) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['pj_penerimaan'] ?? '') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['pj_pengembalian'] ?? '') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="lp-empty">
                                Tidak ada data laporan sesuai filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="kg-note lp-scroll-help">
        </p>
        <p class="kg-note">
            Jumlah ompreng menunjukkan kotak makan,
            bukan jumlah sisa makanan.
        </p>
    </section>

    <div class="kg-actions lp-export-actions">
        <button type="button" onclick="window.print()"
                class="kg-button kg-primary">
            Cetak laporan
        </button>

        <a href="export_pdf.php?<?= htmlspecialchars(http_build_query([
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'id_kelas' => $id_kelas
        ])) ?>" class="kg-button">
            Ekspor PDF
        </a>
    </div>
</main>

</body>
</html>
