<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_produk'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = floatval($_POST['harga'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '' || $kategori === '') {
        $error = 'Nama produk dan kategori wajib diisi.';
    } else {
        $upload = uploadFoto($_FILES['foto'] ?? null);
        if (!$upload['status']) {
            $error = $upload['message'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, kategori, harga, deskripsi, foto) VALUES (:nama, :kategori, :harga, :deskripsi, :foto)");
            $stmt->execute([
                'nama'      => $nama,
                'kategori'  => $kategori,
                'harga'     => $harga,
                'deskripsi' => $deskripsi,
                'foto'      => $upload['filename']
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produk berhasil ditambahkan.'];
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 650px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Tambah Produk Baru</h5>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" required value="<?= htmlspecialchars($_POST['nama_produk'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" required value="<?= htmlspecialchars($_POST['kategori'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" min="0" step="100" required value="<?= htmlspecialchars($_POST['harga'] ?? '0') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Produk (JPG/PNG/WEBP, Maks 2MB)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" class="mt-3 rounded d-none" style="max-height: 180px;">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function previewImage(event) {
    const img = document.getElementById('preview');
    if (event.target.files && event.target.files[0]) {
        img.src = URL.createObjectURL(event.target.files[0]);
        img.classList.remove('d-none');
    }
}
</script>
</body>
</html>
