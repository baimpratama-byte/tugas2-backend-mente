<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


require_once "koneksi.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$kode_menu = "";
$nama_menu = "";
$kategori = "";
$harga = "";
$status = "Tersedia";
$error = "";

$kategoriValid = ["Makanan", "Minuman", "Camilan"];
$statusValid = ["Tersedia", "Habis"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode_menu = trim($_POST["kode_menu"] ?? "");
    $nama_menu = trim($_POST["nama_menu"] ?? "");
    $kategori = $_POST["kategori"] ?? "";
    $harga = trim($_POST["harga"] ?? "");
    $status = $_POST["status"] ?? "";

    if(
        $kode_menu === "" ||
        $nama_menu === "" ||
        $kategori === ""  ||
        $harga === ""     ||
        $status === ""
         ) {
            $error = "Semua field wajib diisi!";
         }

    elseif (!is_numeric($harga) || !is_finite((float)$harga)
        || (float)$harga <=0) {
        $error = "Harga harus berupa angka lebih dari 0!";
    }
    elseif (strlen($kode_menu) >20 ) {
        $error = "Kode menu maksimal 20 karakter!";
    }
    elseif (strlen($nama_menu) >100 ){
        $error = "Nama menu maksimal 100 karakter";
    }
    elseif (!in_array($kategori,$kategoriValid, true)) {
        $error = "Kategori yg di pilih tidak valid!";
    }
    elseif (!in_array($status, $statusValid, true)){
        $error = "Status yg di pilih tidak valid!";
    }
    elseif (strlen($harga) > 15 || !preg_match('/^\d+(\.\d{1,2})?$/', $harga)) {
        $error = "Format harga tidak valid. Maksimal 2 angka desimal.";
    }
    else {
        $cek = mysqli_prepare(
            $conn,
            "select id from memu where kode_menu = ?"
        );
        mysqli_stmt_bind_param($cek, "s",$kode_menu);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0){
            $error = "kode menu sudah digunakan. Gunakan kode lain.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "insert into memu
                (kode_menu, nama_menu, kategori, harga, status)
                values (?, ?, ?, ?, ?)"
            );

            $hargaDB = (float)$harga;
            mysqli_stmt_bind_param(
                $stmt,
                "sssds",
                $kode_menu,
                $nama_menu,
                $kategori,
                $hargaDB,
                $status

            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_stmt_close($cek);
                header("Location : index.php?pesan=tambah");
            }

            $error = "gagal menyimpan menu. Silakan coba lagi.";
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($cek);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container form-container">
    <a href="index.php" class="back-link">← Kembali ke daftar menu</a>

    <section class="panel form-panel">
        <p class="eyebrow">DATA MENU</p>
        <h1>Tambah Menu Baru</h1>
        <p class="subtitle">Isi semua informasi menu di bawah ini.</p>

        <?php if ($error !== ""): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="tambah.php">
            <div class="form-group">
                <label for="kode_menu">Kode Menu</label>
                <input type="text" id="kode_menu" name="kode_menu"
                       maxlength="20" required
                       value="<?= e($kode_menu) ?>"
                       placeholder="Contoh: MKN002">
            </div>

            <div class="form-group">
                <label for="nama_menu">Nama Menu</label>
                <input type="text" id="nama_menu" name="nama_menu"
                       maxlength="100" required
                       value="<?= e($nama_menu) ?>"
                       placeholder="Contoh: Nasi Goreng">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoriValid as $pilihan): ?>
                        <option value="<?= e($pilihan) ?>"
                            <?= $kategori === $pilihan ? "selected" : "" ?>>
                            <?= e($pilihan) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga"
                       min="0.01" step="0.01" required
                       value="<?= e($harga) ?>"
                       placeholder="Contoh: 18000">
            </div>

            <div class="form-group">
                <label for="status">Status Menu</label>
                <select id="status" name="status" required>
                    <?php foreach ($statusValid as $pilihan): ?>
                        <option value="<?= e($pilihan) ?>"
                            <?= $status === $pilihan ? "selected" : "" ?>>
                            <?= e($pilihan) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Simpan Menu
                </button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
</div>
</body>
</html>