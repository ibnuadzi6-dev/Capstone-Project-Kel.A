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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    $tahun_ajaran = trim($_POST['tahun_ajaran'] ?? '');
    $jumlah_siswa_l = (int) ($_POST['jumlah_siswa_l'] ?? 0);
    $jumlah_siswa_p = (int) ($_POST['jumlah_siswa_p'] ?? 0);

    if ($nama_kelas !== '' && $tahun_ajaran !== '') {
        $sql = "INSERT INTO kelas
                (nama_kelas, tahun_ajaran, jumlah_siswa_l, jumlah_siswa_p)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "ssii",
            $nama_kelas,
            $tahun_ajaran,
            $jumlah_siswa_l,
            $jumlah_siswa_p
        );

        if (mysqli_stmt_execute($stmt)) {
            $pesan = 'Data kelas berhasil ditambahkan.';
        } else {
            $pesan = 'Data kelas gagal ditambahkan.';
        }

        mysqli_stmt_close($stmt);
    } else {
        $pesan = 'Nama kelas dan tahun ajaran wajib diisi.';
    }
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM kelas
     ORDER BY
        CAST(nama_kelas AS UNSIGNED) ASC,
        nama_kelas ASC,
        id_kelas ASC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Kelas MBG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=3">
</head>
<body class="mbg-dashboard mbg-kelas-page">

<header class="mbg-header">
    <div class="mbg-shell mbg-header-inner">
        <div class="mbg-brand">
            <strong>MONITORING MBG</strong>
            <span>SDN Cimpaeun 03</span>
        </div>

        <div class="mbg-account">
            <span>
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin Sekolah') ?>
            </span>
            <a href="logout.php" class="mbg-outline-button">Keluar</a>
        </div>
    </div>

    <nav class="mbg-shell mbg-navigation" aria-label="Navigasi admin">
        <a href="dashboard.php">Dashboard</a>
        <a href="kelas.php" class="active" aria-current="page">Data Kelas</a>
        <a href="kegiatan.php">Kegiatan MBG</a>
        <a href="laporan.php">Laporan</a>
        <a href="petugas.php">Data Petugas</a>
        <a href="penerima.php">Data Penerima MBG</a>
    </nav>
</header>

<main class="mbg-shell mbg-main">
    <div class="mbg-page-heading">
        <div>
            <h1>Data Kelas</h1>
            <p>
                Kelola jumlah siswa per kelas tanpa mencatat
                nama siswa satu per satu.
            </p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-info" role="status">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <section class="mbg-panel kelas-panel">
        <h2>Daftar kelas</h2>

        <div class="kelas-toolbar">
            <div class="kelas-search">
                <label for="cari-kelas" class="form-label">Cari kelas</label>
                <input
                    type="search"
                    id="cari-kelas"
                    class="form-control"
                    placeholder="Masukkan nama kelas"
                    aria-controls="tabel-kelas"
                >
            </div>

            <button
                type="button"
                id="tombol-tambah-kelas"
                class="btn kelas-primary"
                aria-expanded="false"
                aria-controls="form-tambah-kelas"
            >
                Tambah kelas
            </button>
        </div>

        <section id="form-tambah-kelas" class="kelas-form-box" hidden>
            <h2>Tambah Data Kelas</h2>

            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama-kelas" class="form-label">
                            Nama Kelas
                        </label>
                        <input
                            type="text"
                            id="nama-kelas"
                            name="nama_kelas"
                            class="form-control"
                            placeholder="Contoh: 1A"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="tahun-ajaran" class="form-label">
                            Tahun Ajaran
                        </label>
                        <input
                            type="text"
                            id="tahun-ajaran"
                            name="tahun_ajaran"
                            class="form-control"
                            placeholder="Contoh: 2026/2027"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="siswa-l" class="form-label">
                            Jumlah Siswa Laki-laki
                        </label>
                        <input
                            type="number"
                            id="siswa-l"
                            name="jumlah_siswa_l"
                            class="form-control"
                            min="0"
                            value="0"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="siswa-p" class="form-label">
                            Jumlah Siswa Perempuan
                        </label>
                        <input
                            type="number"
                            id="siswa-p"
                            name="jumlah_siswa_p"
                            class="form-control"
                            min="0"
                            value="0"
                            required
                        >
                    </div>
                </div>

                <div class="kelas-form-actions">
                    <button type="submit" class="btn kelas-primary">
                        Simpan Data
                    </button>
                    <button
                        type="button"
                        id="tombol-batal-kelas"
                        class="btn kelas-secondary"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </section>

        <div class="table-responsive">
            <table id="tabel-kelas" class="table mbg-table kelas-table">
                <thead>
                    <tr>
                        <th scope="col">Kelas</th>
                        <th scope="col">Tahun ajaran</th>
                        <th scope="col">Laki-laki</th>
                        <th scope="col">Perempuan</th>
                        <th scope="col">Total siswa</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="kelas-data-row">
                                <td class="kelas-nama">
                                    <?= htmlspecialchars($row['nama_kelas']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['tahun_ajaran']) ?>
                                </td>
                                <td><?= (int) $row['jumlah_siswa_l'] ?></td>
                                <td><?= (int) $row['jumlah_siswa_p'] ?></td>
                                <td>
                                    <?= (int) $row['jumlah_siswa_l']
                                        + (int) $row['jumlah_siswa_p'] ?>
                                </td>
                                <td>
                                    <div class="kelas-row-actions">
                                        <a
                                            href="edit_kelas.php?id=<?= (int) $row['id_kelas'] ?>"
                                            class="btn kelas-secondary"
                                        >
                                            Edit
                                        </a>
                                        <a
                                            href="hapus_kelas.php?id=<?= (int) $row['id_kelas'] ?>"
                                            class="btn kelas-delete"
                                            onclick="return confirm('Yakin ingin menghapus kelas ini?')"
                                        >
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="mbg-empty">
                                Belum ada data kelas.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr id="kelas-tidak-ditemukan" hidden>
                        <td colspan="6" class="mbg-empty">
                            Tidak ada kelas yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mbg-caption kelas-note">
            Total siswa dihitung dari jumlah laki-laki dan perempuan.
        </p>
    </section>
</main>

<script>
    const tombolTambah = document.getElementById('tombol-tambah-kelas');
    const tombolBatal = document.getElementById('tombol-batal-kelas');
    const kotakForm = document.getElementById('form-tambah-kelas');

    function tampilkanForm(buka) {
        kotakForm.hidden = !buka;
        tombolTambah.setAttribute('aria-expanded', String(buka));

        if (buka) {
            document.getElementById('nama-kelas').focus();
        } else {
            tombolTambah.focus();
        }
    }

    tombolTambah.addEventListener('click', function () {
        tampilkanForm(kotakForm.hidden);
    });

    tombolBatal.addEventListener('click', function () {
        tampilkanForm(false);
    });

    const pencarian = document.getElementById('cari-kelas');
    const barisKelas = document.querySelectorAll('.kelas-data-row');
    const tidakDitemukan = document.getElementById('kelas-tidak-ditemukan');

    pencarian.addEventListener('input', function () {
        const kata = pencarian.value.trim().toLowerCase();
        let jumlahTerlihat = 0;

        barisKelas.forEach(function (baris) {
            const nama = baris.querySelector('.kelas-nama')
                .textContent.trim().toLowerCase();

            const cocok = nama.includes(kata);
            baris.hidden = !cocok;

            if (cocok) jumlahTerlihat++;
        });

        tidakDitemukan.hidden =
            barisKelas.length === 0 || jumlahTerlihat > 0;
    });
</script>

</body>
</html>
