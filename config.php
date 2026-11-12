<?php
session_start();

$host    = 'localhost';
$db      = 'db_crud_centos';
$user    = 'admin_crud';
$pass    = 'PasswordKuat123!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function uploadFoto($file) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['status' => true, 'filename' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => 'Terjadi kesalahan saat mengunggah foto.'];
    }

    // Batas ukuran file 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['status' => false, 'message' => 'Ukuran foto maksimal 2MB.'];
    }

    // Validasi tipe MIME asli file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mime, $allowedMimes)) {
        return ['status' => false, 'message' => 'Format foto hanya mendukung JPG, PNG, atau WEBP.'];
    }

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $ext         = $allowedMimes[$mime];
    $newFilename = uniqid('img_', true) . '.' . $ext;
    $destination = $uploadDir . $newFilename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['status' => true, 'filename' => $newFilename];
    }

    return ['status' => false, 'message' => 'Gagal menyimpan file ke folder uploads.'];
}
