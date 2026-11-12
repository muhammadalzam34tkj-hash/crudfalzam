<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT foto FROM produk WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $produk = $stmt->fetch();

    if ($produk) {
        if (!empty($produk['foto'])) {
            $filePath = __DIR__ . '/uploads/' . $produk['foto'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $del = $pdo->prepare("DELETE FROM produk WHERE id = :id");
        $del->execute(['id' => $id]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data dan foto berhasil dihapus.'];
    }
}

header('Location: index.php');
exit;
