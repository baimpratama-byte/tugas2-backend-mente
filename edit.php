
<?php
require_once "koneksi.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$kategoriValid = ["Makanan", "Minuman", "Camilan"];
$statusValid = ["Tersedia", "Habis"];

$id = filter_var(
    $_GET["id"] ?? $_POST["id"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($id === false || $id === null) {
    header("Location: index.php?error=" . urlencode("ID menu tidak valid."));
    exit;
}

// Ambil data lama berdasarkan ID.
$stmt = mysqli_prepare($conn, "SELECT * FROM memu WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$memu = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$memu) {
    header("Location: index.php?error=" . urlencode("Menu tidak ditemukan."));
    exit;
}

$kode_menu = $memu["kode_menu"];
$nama_menu = $memu["nama_menu"];
$kategori = $memu["kategori"];
$harga = $memu["harga"];
$status = $memu["status"];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kode_menu = trim($_POST["kode_menu"] ?? "");
    $nama_menu = trim($_POST["nama_menu"] ?? "");
    $kategori = $_POST["kategori"] ?? "";
    $harga = trim($_POST["harga"] ?? "");
    $status = $_POST["status"] ?? "";

    if (
        $kode_menu === "" ||
        $nama_menu === "" ||
        $kategori === "" ||
        $harga === "" ||
        $status === ""
    ) {
        $error = "Semua field wajib diisi!";
    }
    elseif (!is_numeric($harga) || !is_finite((float)$harga)
        || (float)$harga <= 0) {
        $error = "Harga harus berupa angka lebih dari 0!";
    }
    elseif (strlen($kode_menu) > 20) {
        $error = "Kode menu maksimal 20 karakter!";
    }
    elseif (strlen($nama_menu) > 100) {
        $error = "Nama menu maksimal 100 karakter!";
    }
    elseif (!in_array($kategori, $kategoriValid, true)) {
        $error = "Kategori yang dipilih tidak valid!";
    }
    elseif (!in_array($status, $statusValid, true)) {
        $error = "Status yang dipilih tidak valid!";
    }
    elseif (strlen($harga) > 15 || !preg_match('/^\d+(\.\d{1,2})?$/', $harga)) {
        $error = "Format harga tidak valid. Maksimal 2 angka desimal.";
    }
    else {
        // Kode menu tidak boleh sama dengan menu lain.
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM memu WHERE kode_menu = ? AND id <> ?"
        );
        mysqli_stmt_bind_param($cek, "si", $kode_menu, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            $error = "Kode menu sudah digunakan oleh menu lain.";
        } else {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE memu
                 SET kode_menu = ?, nama_menu = ?, kategori = ?,
                     harga = ?, status = ?
                 WHERE id = ?"
            );

            $hargaDB = (float)$harga;
            mysqli_stmt_bind_param(
                $stmt,
                "sssdsi",
                $kode_menu,
                $nama_menu,
                $kategori,
                $hargaDB,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_stmt_close($cek);
                header("Location: index.php?pesan=edit");
                exit;
            }

            $error = "Gagal memperbarui menu. Silakan coba lagi.";
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
    <title>Edit Menu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container form-container">
    <a href="index.php" class="back-link">← Kembali ke daftar menu</a>

    <section class="panel form-panel">
        <p class="eyebrow">PERBARUI DATA</p>
        <h1>Edit Menu</h1>
        <p class="subtitle">Ubah informasi menu sesuai kebutuhan.</p>

        <?php if ($error !== ""): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php">
            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <div class="form-group">
                <label for="kode_menu">Kode Menu</label>
                <input type="text" id="kode_menu" name="kode_menu"
                       maxlength="20" required
                       value="<?= e($kode_menu) ?>">
            </div>

            <div class="form-group">
                <label for="nama_menu">Nama Menu</label>
                <input type="text" id="nama_menu" name="nama_menu"
                       maxlength="100" required
                       value="<?= e($nama_menu) ?>">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
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
                       value="<?= e($harga) ?>">
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
                    Simpan Perubahan
                </button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </section>
</div>
</body>
</html>
