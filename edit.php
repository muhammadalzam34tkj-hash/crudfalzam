<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id");
$stmt->execute(['id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    header('Location: index.php');
    exit;
}

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
            $fotoFinal = $produk['foto'];

            // Jika ada foto baru yang diunggah, hapus foto lama
            if ($upload['filename'] !== null) {
                if (!empty($produk['foto'])) {
                    $oldPath = __DIR__ . '/uploads/' . $produk['foto'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $fotoFinal = $upload['filename'];
            }

            $update = $pdo->prepare("UPDATE produk SET nama_produk = :nama, kategori = :kategori, harga = :harga, deskripsi = :deskripsi, foto = :foto WHERE id = :id");
            $update->execute([
                'nama'      => $nama,
                'kategori'  => $kategori,
                'harga'     => $harga,
                'deskripsi' => $deskripsi,
                'foto'      => $fotoFinal,
                'id'        => $id
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data produk berhasil diperbarui.'];
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
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 650px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Edit Produk</h5>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" required value="<?= htmlspecialchars($produk['nama_produk']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control" required value="<?= htmlspecialchars($produk['kategori']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" min="0" step="100" required value="<?= htmlspecialchars($produk['harga']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Saat Ini</label><br>
                    <?php if (!empty($produk['foto']) && file_exists(__DIR__ . '/uploads/' . $produk['foto'])): ?>
                        <img src="uploads/<?= htmlspecialchars($produk['foto']) ?>" id="preview" class="rounded mb-2" style="max-height: 160px;">
                    <?php else: ?>
                        <p class="text-muted small">Belum ada foto.</p>
                        <img id="preview" class="rounded mb-2 d-none" style="max-height: 160px;">
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control mt-2" accept="image/*" onchange="previewImage(event)">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-warning">Update Data</button>
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
