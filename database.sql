CREATE DATABASE wisata_simple;
USE wisata_simple;

-- Table destinasi
CREATE TABLE destinasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_destinasi VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga_dewasa DECIMAL(10,2) NOT NULL,
    harga_anak DECIMAL(10,2) NOT NULL,
    kuota_harian INT NOT NULL,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_telepon VARCHAR(15),
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pemesanan
CREATE TABLE pemesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_booking VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    destinasi_id INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    tanggal_berkunjung DATE NOT NULL,
    jumlah_dewasa INT NOT NULL,
    jumlah_anak INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (destinasi_id) REFERENCES destinasi(id)
);

-- Insert admin (password: password123)
INSERT INTO users (nama_lengkap, email, password, role) VALUES 
('Administrator', 'admin@wisata.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample destinasi
INSERT INTO destinasi (nama_destinasi, deskripsi, harga_dewasa, harga_anak, kuota_harian) VALUES 
('Taman Wisata Alam', 'Taman alam dengan berbagai flora dan fauna menarik', 50000, 25000, 200),
('Museum Sejarah', 'Museum yang menyimpan sejarah dan budaya daerah', 35000, 15000, 150),
('Air Terjun Seruni', 'Air terjun dengan pemandangan alam yang menakjubkan', 45000, 20000, 100);