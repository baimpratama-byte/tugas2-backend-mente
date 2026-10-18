<?php
require_once "koneksi.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$query = mysqli_query($conn, "select * from memu order by id desc");

if ("Gagal mengambil data: " . mysqli_error($conn));

$pesan = $_GET["pesan"] ?? "";
$jenis = $_GET["jenis"] ?? "success";

$daftarPesan = [
    "tambah" => "Menu berhasil ditambahkan.",
    "edit" => "Menu berhasil diperbarui.",
    "hapus" => "Menu berhasil dihapus."
];

$pesanTampil = $daftarPesan[$pesan] ?? "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MANAJEMEN MENU KAFE</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class ="container">
        <header class="header">
            <div>
                <P class ="eyebrown">ADMINISTRASI KAFE</p>
                <h1>Manajemn Menu</h1>
                <p class="subtitle">Kelola makanan, minuman, dan camilan.</p>
            </div>
            <a href="tambah.php" class="btn btn-primary">+ Tambah Menu</a>
        </header>

        <?php if ($pesanTampil !== ""): ?>
            <div class="alert alert-succes">
                <?= e($pesanTampil) ?>
            </div>

        <?php elseif (isset($_GET["error"])): ?>
            <div class="alert alert-error">
                <?= e($_GET["error"]) ?>
            </div>

        <?Php endif; ?>

        <section class="panel">
            <div class="panel-heading">
                <h2>Daftar Menu</h2>
                <span class="count">
                    <?= mysqli_num_rows($query) ?> menu
                </span>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Menu</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php $no =1; ?>
                            <?php while ($memu = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span class="code">
                                            <?=  e($memu["kode_menu"]) ?>
                                        </span>
                                    </td>
                                    <td class="menu-name">
                                            <?= e($memu["nama_menu"]) ?>
                                     </td>
                                    <td><?= ($memu["kategori"]) ?></td>
                                    <td class="price">
                                        Rp <?= number_format(
                                            (float)$memu["harga"],
                                            0,",","."
                                        ) ?>
                                    </td>
                                    <td>
                                        <span class="badge" <?= $memu["status"] === "Tersedia" ? "available" : "sold-out" ?>>
                                            <?= e(["status"]) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action">
                                            <a class="btn btn-edit"
                                            href="edit.php?id=<?=  (int)$memu["id"] ?>">
                                            Edit
                                        </a>

                                        <form action="hapus.php" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus menu <?=  e($memu["nama_menu"]) ?>? Data yg dihapus tidak dapat dikembalikan.');">
                                            <input type="hidden" name="id"
                                                    value="<?=  (int)$memu["id"] ?>">
                                            <button type="submit" class="btn btn-delete">
                                                Hapus
                                            </button>
                                            </form>
                                        </div>
                                    </td>
                                 </tr>
                                 <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty">
                                        Belum ada Menu. Klik Tambah Menu Untuk memulai.
                                    </td>
                                </tr>
                                <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>