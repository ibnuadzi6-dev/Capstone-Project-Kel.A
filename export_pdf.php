
<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Mengambil filter yang sama dengan laporan.php
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';
$id_kelas = $_GET['id_kelas'] ?? '';

$sql = "SELECT
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

if ($tanggal_awal !== '' && $tanggal_akhir !== '') {
    // Jika tanggal awal dan akhir diisi, ambil data dalam rentang tanggal
    $sql .= " AND km.tanggal >= ? AND km.tanggal <= ?";
    $params[] = $tanggal_awal;
    $params[] = $tanggal_akhir;
    $types .= 'ss';
} elseif ($tanggal_awal !== '') {
    // Jika hanya tanggal awal diisi, ambil data pada tanggal tersebut saja
    $sql .= " AND km.tanggal = ?";
    $params[] = $tanggal_awal;
    $types .= 's';
} elseif ($tanggal_akhir !== '') {
    // Jika hanya tanggal akhir diisi, ambil data sampai tanggal tersebut
    $sql .= " AND km.tanggal <= ?";
    $params[] = $tanggal_akhir;
    $types .= 's';
}

if ($id_kelas !== '') {
    $sql .= " AND pk.id_kelas = ?";
    $params[] = $id_kelas;
    $types .= 'i';
}

$sql .= " ORDER BY
    km.tanggal DESC,
    km.sesi ASC,
    CAST(k.nama_kelas AS UNSIGNED) ASC,
    k.nama_kelas ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die('Gagal menyiapkan laporan PDF.');
}

if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

$judul = 'Laporan Monitoring MBG';
$periode = 'Semua tanggal';

if ($tanggal_awal !== '' && $tanggal_akhir !== '') {
    $periode = date('d/m/Y', strtotime($tanggal_awal))
        . ' s.d. '
        . date('d/m/Y', strtotime($tanggal_akhir));
} elseif ($tanggal_awal !== '') {
    $periode = date('d/m/Y', strtotime($tanggal_awal));
} elseif ($tanggal_akhir !== '') {
    $periode = date('d/m/Y', strtotime($tanggal_akhir));
}

$nama_kelas = 'Semua Kelas';

if ($id_kelas !== '') {
    $stmt_kelas = mysqli_prepare(
        $conn,
        "SELECT nama_kelas FROM kelas WHERE id_kelas = ?"
    );
    mysqli_stmt_bind_param($stmt_kelas, 'i', $id_kelas);
    mysqli_stmt_execute($stmt_kelas);
    $hasil_kelas = mysqli_stmt_get_result($stmt_kelas);
    $data_kelas = mysqli_fetch_assoc($hasil_kelas);

    if ($data_kelas) {
        $nama_kelas = $data_kelas['nama_kelas'];
    }
}

$html = '
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; }
    h2, p { text-align: center; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #555; padding: 5px; }
    th { background-color: #d1e7dd; }
</style>
</head>
<body>
<h2>' . $judul . '</h2>
<p>Periode: ' . htmlspecialchars($periode) . '<br>
Kelas: ' . htmlspecialchars($nama_kelas) . '</p>

<table>
<thead>
<tr>
    <th>No.</th>
    <th>Tanggal</th>
    <th>Sesi</th>
    <th>Kelas</th>
    <th>Menu Makanan</th>
    <th>Porsi</th>
    <th>Ompreng Diterima</th>
    <th>Ompreng Kembali</th>
    <th>Sisa Ompreng</th>
    <th>PJ Penerimaan</th>
    <th>PJ Pengembalian</th>
</tr>
</thead>
<tbody>';

$no = 1;

while ($row = mysqli_fetch_assoc($result)) {
    $sisa = max(
        0,
        (int) $row['jumlah_ompreng_diterima']
        - (int) $row['jumlah_ompreng_kembali']
    );

    $html .= '<tr>
    <td>' . $no++ . '</td>
    <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
    <td>' . htmlspecialchars($row['sesi']) . '</td>
    <td>' . htmlspecialchars($row['nama_kelas']) . '</td>
    <td>' . nl2br(htmlspecialchars($row['menu_makanan'])) . '</td>
    <td>' . (int) $row['jumlah_porsi_diterima'] . '</td>
    <td>' . (int) $row['jumlah_ompreng_diterima'] . '</td>
    <td>' . (int) $row['jumlah_ompreng_kembali'] . '</td>
    <td>' . $sisa . '</td>
    <td>' . htmlspecialchars($row['pj_penerimaan']) . '</td>
    <td>' . htmlspecialchars($row['pj_pengembalian']) . '</td>
</tr>';
}

$html .= '</tbody></table></body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('laporan-monitoring-mbg.pdf', ['Attachment' => true]);
exit;
