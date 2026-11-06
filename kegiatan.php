<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

$error = '';
$berhasil = '';
$editData = null;

if (isset($_SESSION['pesan_kegiatan'])) {
    $berhasil = $_SESSION['pesan_kegiatan'];
    unset($_SESSION['pesan_kegiatan']);
}

// Proses tambah, edit, dan hapus kegiatan.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'hapus') {
        $id = filter_input(INPUT_POST, 'id_kegiatan', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $error = 'ID kegiatan tidak valid.';
        } else {
            $cek = mysqli_prepare($conn, "SELECT COUNT(*) FROM pencatatan_kelas WHERE id_kegiatan = ?");
            if ($cek) {
                mysqli_stmt_bind_param($cek, 'i', $id);
                mysqli_stmt_execute($cek);
                mysqli_stmt_bind_result($cek, $jumlahCatatan);
                mysqli_stmt_fetch($cek);
                mysqli_stmt_close($cek);

                if ($jumlahCatatan > 0) {
                    $error = 'Kegiatan tidak bisa dihapus karena sudah memiliki data pencatatan kelas.';
                } else {
                    $stmt = mysqli_prepare($conn, "DELETE FROM kegiatan_mbg WHERE id_kegiatan = ?");
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'i', $id);
                        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                            $_SESSION['pesan_kegiatan'] = 'Kegiatan MBG berhasil dihapus.';
                            mysqli_stmt_close($stmt);
                            header('Location: kegiatan.php');
                            exit;
                        }
                        $error = 'Data gagal dihapus atau tidak ditemukan.';
                        mysqli_stmt_close($stmt);
                    } else {
                        $error = 'Permintaan hapus gagal diproses.';
                    }
                }
            } else {
                $error = 'Pengecekan pencatatan kelas gagal dilakukan.';
            }
        }
    } else {
        $tanggal = trim($_POST['tanggal'] ?? '');
        $sesi = trim($_POST['sesi'] ?? '');
        $menu = trim($_POST['menu_makanan'] ?? '');
        $kondisi = trim($_POST['kondisi_makanan'] ?? '');
        $catatan = trim($_POST['catatan'] ?? '');
        $idEdit = filter_input(INPUT_POST, 'id_kegiatan', FILTER_VALIDATE_INT);

        if ($tanggal === '' || $sesi === '' || $menu === '' || $kondisi === '') {
            $error = 'Tanggal, sesi, menu, dan kondisi makanan wajib diisi.';
        } elseif ($aksi === 'edit') {
            if (!$idEdit || $idEdit < 1) {
                $error = 'ID kegiatan tidak valid.';
            } else {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE kegiatan_mbg
                     SET tanggal = ?, sesi = ?, menu_makanan = ?, kondisi_makanan = ?, catatan = ?
                     WHERE id_kegiatan = ?"
                );
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssssi', $tanggal, $sesi, $menu, $kondisi, $catatan, $idEdit);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['pesan_kegiatan'] = 'Kegiatan MBG berhasil diperbarui.';
                        mysqli_stmt_close($stmt);
                        header('Location: kegiatan.php');
                        exit;
                    }
                    $error = 'Data kegiatan gagal diperbarui.';
                    mysqli_stmt_close($stmt);
                } else {
                    $error = 'Permintaan perubahan gagal diproses.';
                }
            }
        } else {
            $id_pengguna = (int) $_SESSION['id_pengguna'];
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO kegiatan_mbg (id_pengguna, tanggal, sesi, menu_makanan, kondisi_makanan, catatan)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'isssss', $id_pengguna, $tanggal, $sesi, $menu, $kondisi, $catatan);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['pesan_kegiatan'] = 'Kegiatan MBG berhasil disimpan.';
                    mysqli_stmt_close($stmt);
                    header('Location: kegiatan.php');
                    exit;
                }
                $error = 'Data gagal disimpan. Periksa koneksi dan struktur database.';
                mysqli_stmt_close($stmt);
            } else {
                $error = 'Permintaan penyimpanan gagal diproses.';
            }
        }
    }
}

// Ambil data untuk form edit.
if (isset($_GET['edit'])) {
    $idEdit = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($idEdit && $idEdit > 0) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM kegiatan_mbg WHERE id_kegiatan = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $idEdit);
            mysqli_stmt_execute($stmt);
            $hasilEdit = mysqli_stmt_get_result($stmt);
            $editData = mysqli_fetch_assoc($hasilEdit) ?: null;
            mysqli_stmt_close($stmt);
            if (!$editData) {
                $error = 'Data kegiatan tidak ditemukan.';
            }
        } else {
            $error = 'Data kegiatan gagal dimuat untuk diedit.';
        }
    }
}

// Rekap pencatatan kelas dihitung terpisah agar jumlah tidak terduplikasi.
$data = mysqli_query(
    $conn,
    "SELECT k.*, p.nama,
            COALESCE(pk.total_porsi, 0) AS total_porsi,
            COALESCE(pk.total_ompreng_kembali, 0) AS total_ompreng_kembali
     FROM kegiatan_mbg k
     LEFT JOIN pengguna p ON k.id_pengguna = p.id_pengguna
     LEFT JOIN (
         SELECT id_kegiatan,
                SUM(jumlah_porsi_diterima) AS total_porsi,
                SUM(jumlah_ompreng_kembali) AS total_ompreng_kembali
         FROM pencatatan_kelas
         GROUP BY id_kegiatan
     ) pk ON pk.id_kegiatan = k.id_kegiatan
     ORDER BY k.tanggal DESC, k.id_kegiatan DESC"
);

if (!$data && $error === '') {
    $error = 'Data kegiatan gagal dimuat. Periksa struktur tabel pencatatan_kelas.';
}

$bukaForm = ($editData !== null) || ($error !== '' && ($_POST['aksi'] ?? '') !== 'hapus');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kegiatan MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=5">
</head>
<body class="kegiatan-ui">
<header class="kg-header">
    <div class="kg-shell kg-header-top">
        <div class="kg-brand">
            <strong>MONITORING MBG</strong>
            <span>SDN Cimpaeun 03</span>
        </div>
        <div class="kg-account">
            <span><?= htmlspecialchars($_SESSION['nama'] ?? 'Pengguna MBG', ENT_QUOTES, 'UTF-8') ?></span>
            <a href="logout.php" class="kg-button">Keluar</a>
        </div>
    </div>
    <nav class="kg-shell kg-nav" aria-label="Navigasi utama">
        <a href="<?= (($_SESSION['peran'] ?? '') === 'admin') ? 'dashboard.php' : 'dashboard_petugas.php' ?>">Dashboard</a>
        <a href="kegiatan.php" class="active" aria-current="page">Kegiatan MBG</a>
        <a href="dokumentasi.php">Dokumentasi</a>
    </nav>
</header>

<main class="kg-shell kg-main">
    <h1>Kegiatan MBG</h1>
    <p class="kg-subtitle">Temukan dan kelola kegiatan MBG berdasarkan tanggal dan sesi.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($berhasil !== ''): ?>
        <div class="alert alert-success" role="status"><?= htmlspecialchars($berhasil, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <section class="kg-panel" aria-labelledby="judul-daftar">
        <h2 id="judul-daftar">Daftar kegiatan</h2>
        <div class="kg-toolbar">
            <div class="kg-filter">
                <label for="filter-tanggal">Tanggal</label>
                <input type="date" id="filter-tanggal">
            </div>
            <div class="kg-filter">
                <label for="filter-sesi">Sesi</label>
                <select id="filter-sesi">
                    <option value="">Semua sesi</option>
                    <option value="pagi">Pagi</option>
                    <option value="siang">Siang</option>
                </select>
            </div>
            <button type="button" id="tombol-catat" class="kg-button kg-primary" aria-controls="panel-form" aria-expanded="<?= $bukaForm ? 'true' : 'false' ?>">Catat kegiatan</button>
            <button type="button" id="reset-filter" class="kg-reset">Reset filter</button>
        </div>

        <div class="kg-table-wrap">
            <table class="kg-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Sesi</th>
                        <th>Porsi siswa</th>
                        <th>Ompreng kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($data && mysqli_num_rows($data) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <tr class="kg-data-row"
                            data-tanggal="<?= htmlspecialchars($row['tanggal'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            data-sesi="<?= htmlspecialchars(strtolower($row['sesi'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-' ?></td>
                            <td><?= htmlspecialchars($row['sesi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((int) ($row['total_porsi'] ?? 0), 0, ',', '.') ?></td>
                            <td><?= number_format((int) ($row['total_ompreng_kembali'] ?? 0), 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['kondisi_makanan'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="kg-detail">
                                    <button type="button" class="kg-button kg-open-detail" aria-haspopup="dialog">Detail</button>
                                    <div class="kg-detail-content" hidden>
                                        <dl>
                                            <dt>Menu makanan</dt>
                                            <dd><?= htmlspecialchars($row['menu_makanan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
                                            <dt>Kondisi makanan</dt>
                                            <dd><?= htmlspecialchars($row['kondisi_makanan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
                                            <dt>Petugas</dt>
                                            <dd><?= htmlspecialchars($row['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
                                            <dt>Catatan</dt>
                                            <dd><?= htmlspecialchars(trim($row['catatan'] ?? '') !== '' ? $row['catatan'] : 'Tidak ada catatan.', ENT_QUOTES, 'UTF-8') ?></dd>
                                        </dl>
                                        <div class="kg-actions">
                                            <a href="kegiatan.php?edit=<?= (int) $row['id_kegiatan'] ?>#panel-form" class="kg-button">Edit</a>
                                            <form method="POST" action="kegiatan.php" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?');">
                                                <input type="hidden" name="aksi" value="hapus">
                                                <input type="hidden" name="id_kegiatan" value="<?= (int) $row['id_kegiatan'] ?>">
                                                <button type="submit" class="kg-button kg-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="kg-empty">Belum ada data kegiatan MBG.</td></tr>
                <?php endif; ?>
                    <tr id="filter-kosong" hidden><td colspan="6" class="kg-empty">Tidak ada kegiatan sesuai filter.</td></tr>
                </tbody>
            </table>
        </div>
        <p class="kg-note">Kolom status menampilkan kondisi makanan. Rekap porsi dan ompreng dihitung dari pencatatan kelas yang terkait dengan kegiatan.</p>
    </section>

    <section class="kg-panel kg-form-panel" id="panel-form" data-buka="<?= $bukaForm ? 'ya' : 'tidak' ?>" aria-labelledby="judul-form" hidden>
        <h2 id="judul-form"><?= $editData ? 'Edit Kegiatan MBG' : 'Catat Kegiatan MBG' ?></h2>
        <form method="POST" action="kegiatan.php">
            <?php if ($editData): ?>
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id_kegiatan" value="<?= (int) $editData['id_kegiatan'] ?>">
            <?php endif; ?>
            <div class="kg-form-grid">
                <div class="kg-field">
                    <label for="tanggal-kegiatan">Tanggal kegiatan *</label>
                    <input type="date" id="tanggal-kegiatan" name="tanggal" value="<?= htmlspecialchars($editData['tanggal'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="kg-field">
                    <label for="sesi-kegiatan">Sesi *</label>
                    <select id="sesi-kegiatan" name="sesi" required>
                        <option value="">Pilih sesi</option>
                        <option value="Pagi" <?= ($editData['sesi'] ?? '') === 'Pagi' ? 'selected' : '' ?>>Pagi</option>
                        <option value="Siang" <?= ($editData['sesi'] ?? '') === 'Siang' ? 'selected' : '' ?>>Siang</option>
                    </select>
                </div>
                <div class="kg-field kg-full">
                    <label for="menu-kegiatan">Menu makanan *</label>
                    <textarea id="menu-kegiatan" name="menu_makanan" rows="3" maxlength="255" required><?= htmlspecialchars($editData['menu_makanan'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="kg-field">
                    <label for="kondisi-kegiatan">Kondisi makanan *</label>
                    <select id="kondisi-kegiatan" name="kondisi_makanan" required>
                        <option value="">Pilih kondisi</option>
                        <option value="Baik" <?= ($editData['kondisi_makanan'] ?? '') === 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Kurang Baik" <?= ($editData['kondisi_makanan'] ?? '') === 'Kurang Baik' ? 'selected' : '' ?>>Kurang Baik</option>
                        <option value="Tidak Layak" <?= ($editData['kondisi_makanan'] ?? '') === 'Tidak Layak' ? 'selected' : '' ?>>Tidak Layak</option>
                    </select>
                </div>
                <div class="kg-field kg-full">
                    <label for="catatan-kegiatan">Catatan</label>
                    <textarea id="catatan-kegiatan" name="catatan" rows="3"><?= htmlspecialchars($editData['catatan'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
            <p class="kg-note">* Wajib diisi.</p>
            <div class="kg-actions">
                <button type="submit" class="kg-button kg-primary"><?= $editData ? 'Simpan perubahan' : 'Simpan kegiatan' ?></button>
                <a href="kegiatan.php" class="kg-button">Batal</a>
            </div>
        </form>
    </section>
</main>

<dialog id="modal-detail" class="kg-modal" aria-labelledby="judul-modal-detail">
    <div class="kg-modal-header">
        <h2 id="judul-modal-detail">Detail kegiatan MBG</h2>
        <button type="button" id="tutup-detail" class="kg-button" autofocus>Tutup</button>
    </div>
    <p id="info-detail" class="kg-modal-info"></p>
    <div id="isi-detail"></div>
</dialog>

<script>
const panelForm = document.getElementById('panel-form');
const tombolCatat = document.getElementById('tombol-catat');
panelForm.hidden = panelForm.dataset.buka !== 'ya';
tombolCatat.setAttribute('aria-expanded', String(!panelForm.hidden));

function bukaFormKegiatan() {
    panelForm.hidden = false;
    tombolCatat.setAttribute('aria-expanded', 'true');
    panelForm.scrollIntoView({ block: 'start', behavior: 'smooth' });
    document.getElementById('tanggal-kegiatan').focus({ preventScroll: true });
}

tombolCatat.addEventListener('click', function () {
    if (new URLSearchParams(window.location.search).has('edit')) {
        window.location.href = 'kegiatan.php?tambah=1#panel-form';
        return;
    }
    bukaFormKegiatan();
});

if (new URLSearchParams(window.location.search).get('tambah') === '1') {
    bukaFormKegiatan();
}
if (panelForm.dataset.buka === 'ya') {
    panelForm.scrollIntoView({ block: 'start' });
}

const tanggalFilter = document.getElementById('filter-tanggal');
const sesiFilter = document.getElementById('filter-sesi');
const barisKegiatan = document.querySelectorAll('.kg-data-row');
const pesanKosong = document.getElementById('filter-kosong');

function saringKegiatan() {
    let terlihat = 0;
    barisKegiatan.forEach(function (baris) {
        const tanggalCocok = !tanggalFilter.value || baris.dataset.tanggal === tanggalFilter.value;
        const sesiCocok = !sesiFilter.value || baris.dataset.sesi === sesiFilter.value;
        baris.hidden = !(tanggalCocok && sesiCocok);
        if (!baris.hidden) terlihat++;
    });
    pesanKosong.hidden = barisKegiatan.length === 0 || terlihat > 0;
}

tanggalFilter.addEventListener('change', saringKegiatan);
sesiFilter.addEventListener('change', saringKegiatan);
document.getElementById('reset-filter').addEventListener('click', function () {
    tanggalFilter.value = '';
    sesiFilter.value = '';
    saringKegiatan();
});

const modalDetail = document.getElementById('modal-detail');
const isiDetail = document.getElementById('isi-detail');
const infoDetail = document.getElementById('info-detail');
let sumberDetail = null;
let kontenDetail = null;
let tombolDetailAktif = null;

document.querySelectorAll('.kg-open-detail').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
        sumberDetail = tombol.closest('.kg-detail');
        kontenDetail = sumberDetail.querySelector('.kg-detail-content');
        tombolDetailAktif = tombol;
        const baris = tombol.closest('tr');
        infoDetail.textContent = baris.cells[0].textContent.trim() + ' • Sesi ' + baris.cells[1].textContent.trim();
        kontenDetail.hidden = false;
        isiDetail.appendChild(kontenDetail);
        modalDetail.showModal();
    });
});

document.getElementById('tutup-detail').addEventListener('click', function () {
    modalDetail.close();
});
modalDetail.addEventListener('close', function () {
    if (sumberDetail && kontenDetail) {
        kontenDetail.hidden = true;
        sumberDetail.appendChild(kontenDetail);
    }
    if (tombolDetailAktif) tombolDetailAktif.focus();
    sumberDetail = null;
    kontenDetail = null;
    tombolDetailAktif = null;
});
</script>
</body>
</html>
