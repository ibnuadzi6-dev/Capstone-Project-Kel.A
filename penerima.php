<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['peran'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'koneksi.php';

$pesan = '';
$error = '';
$data_edit = null;

// Proses tambah atau update penerima
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penerima = (int) ($_POST['id_penerima'] ?? 0);
    $nama = trim($_POST['nama_penerima'] ?? '');
    $id_kelas = (int) ($_POST['id_kelas'] ?? 0);
    $status = $_POST['status_penerima'] ?? 'Aktif';

    if ($nama === '' || $id_kelas <= 0) {
        $error = 'Nama penerima dan kelas wajib diisi.';
    } elseif (!in_array($status, ['Aktif', 'Tidak Aktif'], true)) {
        $error = 'Status penerima tidak valid.';
    } else {
        // Pastikan kelas yang dipilih tersedia
        $cek = mysqli_prepare(
            $conn,
            "SELECT id_kelas FROM kelas WHERE id_kelas = ?"
        );
        mysqli_stmt_bind_param($cek, 'i', $id_kelas);
        mysqli_stmt_execute($cek);
        $hasil_cek = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil_cek) === 0) {
            $error = 'Kelas tidak ditemukan.';
        } elseif ($id_penerima > 0) {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE penerima_mbg
                 SET nama_penerima = ?, id_kelas = ?, status_penerima = ?
                 WHERE id_penerima = ?"
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sisi',
                $nama,
                $id_kelas,
                $status,
                $id_penerima
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: penerima.php?pesan=diubah');
                exit;
            } else {
                $error = 'Data penerima gagal diperbarui.';
            }
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO penerima_mbg
                 (nama_penerima, id_kelas, status_penerima)
                 VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param(
                $stmt,
                'sis',
                $nama,
                $id_kelas,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                header('Location: penerima.php?pesan=ditambahkan');
                exit;
            } else {
                $error = 'Data penerima gagal disimpan.';
            }
        }
    }
}

// Pesan setelah penyimpanan berhasil
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'ditambahkan') {
        $pesan = 'Data penerima berhasil ditambahkan.';
    } elseif ($_GET['pesan'] === 'diubah') {
        $pesan = 'Data penerima berhasil diperbarui.';
    }
}

// Ambil data untuk diedit
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];

    $stmt_edit = mysqli_prepare(
        $conn,
        "SELECT * FROM penerima_mbg WHERE id_penerima = ?"
    );
    mysqli_stmt_bind_param($stmt_edit, 'i', $id_edit);
    mysqli_stmt_execute($stmt_edit);
    $hasil_edit = mysqli_stmt_get_result($stmt_edit);
    $data_edit = mysqli_fetch_assoc($hasil_edit);

    if (!$data_edit) {
        $error = 'Data penerima tidak ditemukan.';
    }
}

// Ambil daftar kelas
$query_kelas = mysqli_query(
    $conn,
    "SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC"
);

// Ambil seluruh data penerima
$sql_data = "SELECT
                p.id_penerima,
                p.nama_penerima,
                p.status_penerima,
                k.nama_kelas
             FROM penerima_mbg p
             JOIN kelas k ON p.id_kelas = k.id_kelas
             ORDER BY k.nama_kelas, p.nama_penerima";

$result = mysqli_query($conn, $sql_data);

if (!$result) {
    die('Gagal mengambil data penerima.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Penerima MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>
<body class="kegiatan-ui pengguna-ui penerima-ui">

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
        <a href="petugas.php">Data Petugas</a>
        <a href="penerima.php" class="active" aria-current="page">
            Data Penerima MBG
        </a>
    </nav>
</header>

<main class="kg-shell kg-main">
    <h1>Data Penerima MBG</h1>
    <p class="kg-subtitle">
        Kelola nama penanggung jawab penerimaan MBG untuk setiap kelas.
    </p>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-success" role="status">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <section class="kg-panel" aria-labelledby="judul-daftar-pj">
        <h2 id="judul-daftar-pj">Daftar penanggung jawab</h2>

        <div class="pg-toolbar">
            <div class="pg-search">
                <label for="cari-pj">Cari penanggung jawab</label>
                <input type="search" id="cari-pj"
                       placeholder="Nama atau kelas">
            </div>

            <button type="button" id="tambah-pj"
                    class="kg-button kg-primary"
                    aria-controls="form-pj"
                    aria-expanded="true">
                Tambah penanggung jawab
            </button>
        </div>

        <div class="pg-table-wrap">
            <table class="pg-table pj-table">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Nama penanggung jawab</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="pj-row">
                                <td><?= $no++ ?></td>
                                <td class="pj-nama">
                                    <?= htmlspecialchars($row['nama_penerima']) ?>
                                </td>
                                <td class="pj-kelas">
                                    <?= htmlspecialchars($row['nama_kelas']) ?>
                                </td>
                                <td>
                                    <span class="pj-status <?= $row['status_penerima'] === 'Aktif'
                                        ? 'pj-aktif' : 'pj-nonaktif' ?>">
                                        <?= htmlspecialchars($row['status_penerima']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="penerima.php?edit=<?= (int) $row['id_penerima'] ?>#form-pj"
                                       class="kg-button">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="pg-empty">
                                Belum ada data penanggung jawab.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr id="pj-kosong" hidden>
                        <td colspan="5" class="pg-empty">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section id="form-pj" class="kg-panel pg-form-panel"
             data-buka="<?= ($data_edit || $error !== '') ? 'ya' : 'tidak' ?>"
             aria-labelledby="judul-form-pj">
        <h2 id="judul-form-pj">
            <?= $data_edit
                ? 'Edit penanggung jawab'
                : 'Tambah penanggung jawab' ?>
        </h2>

        <form method="POST" action="penerima.php">
            <input type="hidden" name="id_penerima"
                   value="<?= (int) ($data_edit['id_penerima'] ?? 0) ?>">

            <div class="pg-form-grid">
                <div>
                    <label for="nama-pj">Nama penanggung jawab *</label>
                    <input type="text" id="nama-pj"
                           name="nama_penerima"
                           maxlength="100" required
                           value="<?= htmlspecialchars($data_edit['nama_penerima'] ?? '') ?>">
                </div>

                <div>
                    <label for="kelas-pj">Kelas *</label>
                    <select id="kelas-pj" name="id_kelas" required>
                        <option value="">Pilih kelas</option>

                        <?php while ($kelas = mysqli_fetch_assoc($query_kelas)): ?>
                            <option value="<?= (int) $kelas['id_kelas'] ?>"
                                <?= isset($data_edit['id_kelas']) &&
                                    (int) $data_edit['id_kelas'] === (int) $kelas['id_kelas']
                                    ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kelas['nama_kelas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="status-pj">Status *</label>
                    <select id="status-pj" name="status_penerima" required>
                        <option value="Aktif"
                            <?= ($data_edit['status_penerima'] ?? 'Aktif') === 'Aktif'
                                ? 'selected' : '' ?>>
                            Aktif
                        </option>
                        <option value="Tidak Aktif"
                            <?= ($data_edit['status_penerima'] ?? '') === 'Tidak Aktif'
                                ? 'selected' : '' ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>
            </div>

            <p class="kg-note">* Wajib diisi.</p>

            <div class="kg-actions pg-form-actions">
                <button type="submit" class="kg-button kg-primary">
                    <?= $data_edit ? 'Simpan perubahan' : 'Simpan data' ?>
                </button>
                <a href="penerima.php" class="kg-button">Batal</a>
            </div>
        </form>
    </section>
</main>

<script>
    const formPJ = document.getElementById('form-pj');
    const tombolTambahPJ = document.getElementById('tambah-pj');

    formPJ.hidden = formPJ.dataset.buka !== 'ya';
    tombolTambahPJ.setAttribute('aria-expanded', String(!formPJ.hidden));

    tombolTambahPJ.addEventListener('click', function () {
        // Keluar dari mode edit sebelum menambah data baru.
        if (new URLSearchParams(window.location.search).has('edit')) {
            window.location.href = 'penerima.php?tambah=1#form-pj';
            return;
        }

        bukaFormPJ();
    });

    function bukaFormPJ() {
        formPJ.hidden = false;
        tombolTambahPJ.setAttribute('aria-expanded', 'true');
        formPJ.scrollIntoView({ block: 'start' });
        document.getElementById('nama-pj').focus({ preventScroll: true });
    }

    if (new URLSearchParams(window.location.search).get('tambah') === '1') {
        bukaFormPJ();
    }

    const cariPJ = document.getElementById('cari-pj');
    const barisPJ = document.querySelectorAll('.pj-row');
    const pesanKosongPJ = document.getElementById('pj-kosong');

    cariPJ.addEventListener('input', function () {
        const kata = cariPJ.value.trim().toLowerCase();
        let terlihat = 0;

        barisPJ.forEach(function (baris) {
            const nama = baris.querySelector('.pj-nama')
                .textContent.toLowerCase();
            const kelas = baris.querySelector('.pj-kelas')
                .textContent.toLowerCase();

            const cocok = nama.includes(kata) || kelas.includes(kata);
            baris.hidden = !cocok;

            if (cocok) terlihat++;
        });

        pesanKosongPJ.hidden = barisPJ.length === 0 || terlihat > 0;
    });
</script>

</body>
</html>
