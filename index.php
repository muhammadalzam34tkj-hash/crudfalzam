<?php
require 'config.php';

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE nama_produk LIKE :q1 OR kategori LIKE :q2 ORDER BY id DESC");
    $stmt->execute(['q1' => "%$search%", 'q2' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM produk ORDER BY id DESC");
}
$produkList = $stmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - CRUD PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Data Produk</h2>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Produk</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" placeholder="Cari nama produk atau kategori..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($produkList) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produkList as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (!empty($row['foto']) && file_exists(__DIR__ . '/uploads/' . $row['foto'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['foto']) ?>" alt="Foto" class="rounded" style="width: 65px; height: 65px; object-fit: cover;">
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tanpa Foto</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['kategori']) ?></span></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td class="text-end">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form action="hapus.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data dan fotonya?');">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
