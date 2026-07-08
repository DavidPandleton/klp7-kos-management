CREATE DATABASE IF NOT EXISTS kos_management;
USE kos_management;

-- 1. USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pemilik', 'penyewa') NOT NULL DEFAULT 'penyewa',
    no_telepon VARCHAR(20),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. KAMAR
CREATE TABLE kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_kamar VARCHAR(10) NOT NULL UNIQUE,
    tipe VARCHAR(50),
    harga DECIMAL(12,2) NOT NULL,
    kapasitas INT DEFAULT 1,
    fasilitas TEXT,
    status ENUM('tersedia', 'terisi', 'maintenance') NOT NULL DEFAULT 'tersedia',
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. KONTRAK SEWA
CREATE TABLE kontrak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penyewa_id INT NOT NULL,
    kamar_id INT NOT NULL,
    tgl_mulai DATE NOT NULL,
    tgl_akhir DATE NOT NULL,
    status ENUM('aktif', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (penyewa_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kamar_id) REFERENCES kamar(id) ON DELETE CASCADE
);

-- 4. PEMBAYARAN
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kontrak_id INT NOT NULL,
    bulan INT NOT NULL,
    tahun INT NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL,
    denda DECIMAL(12,2) DEFAULT 0,
    bukti VARCHAR(255),
    status ENUM('belum_bayar', 'menunggu', 'lunas') NOT NULL DEFAULT 'belum_bayar',
    tgl_bayar DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) ON DELETE CASCADE
);

-- 5. PENGADUAN
CREATE TABLE pengaduan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penyewa_id INT NOT NULL,
    kamar_id INT,
    keluhan TEXT NOT NULL,
    foto VARCHAR(255),
    status ENUM('baru', 'diproses', 'selesai') NOT NULL DEFAULT 'baru',
    respon TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (penyewa_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kamar_id) REFERENCES kamar(id) ON DELETE SET NULL
);

-- SEED DATA
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@kos.com', '$2y$10$YUA4p4Kct662tQYTxXBSt.OjvI2gG6c/cEQX8gDp3CWvi7kJqj7nm', 'admin'),
('pemilik1', 'pemilik@kos.com', '$2y$10$YUA4p4Kct662tQYTxXBSt.OjvI2gG6c/cEQX8gDp3CWvi7kJqj7nm', 'pemilik'),
('penyewa1', 'penyewa@kos.com', '$2y$10$YUA4p4Kct662tQYTxXBSt.OjvI2gG6c/cEQX8gDp3CWvi7kJqj7nm', 'penyewa');
-- password for all seeds above: password

INSERT INTO kamar (nomor_kamar, tipe, harga, kapasitas, fasilitas, status) VALUES
('A01', 'Standar', 500000, 1, 'AC, WiFi', 'terisi'),
('A02', 'VIP', 1000000, 2, 'AC, WiFi, Kamar Mandi Dalam', 'tersedia'),
('A03', 'Standar', 500000, 1, 'WiFi', 'tersedia');

-- KONTRAK
INSERT INTO kontrak (penyewa_id, kamar_id, tgl_mulai, tgl_akhir, status) VALUES
(3, 1, '2026-06-01', '2026-12-31', 'aktif');

-- PEMBAYARAN
INSERT INTO pembayaran (kontrak_id, bulan, tahun, jumlah, status, tgl_bayar) VALUES
(1, 6, 2026, 500000, 'lunas', '2026-06-01'),
(1, 7, 2026, 500000, 'menunggu', NULL);

-- PENGADUAN
INSERT INTO pengaduan (penyewa_id, kamar_id, keluhan, status) VALUES
(3, 1, 'AC di kamar A01 tidak dingin, sudah dilaporkan sejak kemarin tapi belum ada tindakan.', 'baru');
