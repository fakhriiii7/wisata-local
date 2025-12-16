<?php
require_once 'config/config.php';
$db = (new Database())->getConnection();

$stmt = $db->query("SELECT * FROM destinasi ORDER BY created_at DESC LIMIT 3");
$destinasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/logo1-1.png">
    <title>Wisata Lokal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <h2>WisataLocal</h2>
                </div>
                <nav class="nav-links">
                    <a href="index.php">Beranda</a>
                    <a href="destinasi.php">Destinasi Wisata</a>
                    <a href="cek_pemesanan.php">Cek Pesanan</a>
                    <?php if(isLoggedIn()): ?>
                        <?php if(isAdmin()): ?>
                            <a href="admin/index.php">Admin</a>
                            <a href="admin/login.php?logout=1">Logout</a>
                        <?php else: ?>
                            <a href="user/index.php">Dashboard</a>
                            <a href="user/login.php?action=logout">Logout</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="user/login.php">Login</a>
                        <a href="user/register.php">Daftar</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="hero">
            <h1>Wisata Lokal</h1>
            <p>Jelajahi keindahan wisata daerah kami</p>
            <a href="destinasi.php" class="btn">Lihat Destinasi</a>
        </div>

        <div class="features">
            <h2>Destinasi Populer</h2>
            <div class="grid">
                <?php foreach($destinasi as $d): ?>
                <div class="card">
                    <h3><?php echo $d['nama_destinasi']; ?></h3>
                    <p><?php echo substr($d['deskripsi'], 0, 100); ?>...</p>
                    <div class="price"><?php echo formatRupiah($d['harga_dewasa']); ?></div>
                    <a href="destinasi.php?pesan=<?php echo $d['id']; ?>" class="btn">Pesan Sekarang</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> WisataLocal - Sistem Pemesanan Tiket Wisata</p>
        </div>
    </footer>
</body>
</html>