
<?php
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_var(
    $_POST["id"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($id === false || $id === null) {
    header("Location: index.php?error=" . urlencode("ID menu tidak valid."));
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM memu WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header("Location: index.php?pesan=hapus");
        exit;
    }

    mysqli_stmt_close($stmt);
    header("Location: index.php?error=" . urlencode("Menu tidak ditemukan."));
    exit;
}

mysqli_stmt_close($stmt);
header("Location: index.php?error=" . urlencode("Gagal menghapus menu."));
exit;
?>
