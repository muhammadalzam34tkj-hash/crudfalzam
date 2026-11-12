CREATE DATABASE IF NOT EXISTS db_crud_centos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'admin_crud'@'localhost' IDENTIFIED BY 'PasswordKuat123!';
GRANT ALL PRIVILEGES ON db_crud_centos.* TO 'admin_crud'@'localhost';
FLUSH PRIVILEGES;

USE db_crud_centos;

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    harga DECIMAL(12, 2) NOT NULL DEFAULT 0,
    deskripsi TEXT,
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
