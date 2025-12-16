<?php
require_once 'config/config.php';

$pemesanan = null;
$error = '';

if($_POST && isset($_POST['kode_booking'])) {
    $db = (new Database())->getConnection();
    $kode = sanitize($_POST['kode_booking']);
    
    $stmt = $db->prepare("SELECT p.*, d.nama_destinasi FROM pemesanan p LEFT JOIN destinasi d ON p.destinasi_id = d.id WHERE p.kode_booking = ?");
    $stmt->execute([$kode]);
    $pemesanan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$pemesanan) {
        $error = "Kode booking tidak ditemukan";
    }
}
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Pemesanan - WisataLocal</title>
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
        <h1>Cek Status Pemesanan</h1>
        
        <div class="check-booking">
            <form method="POST">
                <input type="text" name="kode_booking" placeholder="Masukkan kode booking (contoh: WB20231201ABC123)" required>
                <button type="submit" class="btn">Cek Status</button>
            </form>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($pemesanan): ?>
            <div class="booking-details">
                <h2>Detail Pemesanan</h2>
                <div class="card">
                    <div class="info-grid">
                        <div><strong>Kode Booking:</strong> <?php echo $pemesanan['kode_booking']; ?></div>
                        <div><strong>Nama Pemesan:</strong> <?php echo $pemesanan['nama_pemesan']; ?></div>
                        <div><strong>Destinasi:</strong> <?php echo $pemesanan['nama_destinasi']; ?></div>
                        <div><strong>Tanggal Berkunjung:</strong> <?php echo date('d F Y', strtotime($pemesanan['tanggal_berkunjung'])); ?></div>
                        <div><strong>Jumlah Pengunjung:</strong> <?php echo $pemesanan['jumlah_dewasa'] + $pemesanan['jumlah_anak']; ?> orang</div>
                        <div><strong>Total Pembayaran:</strong> <?php echo formatRupiah($pemesanan['total_harga']); ?></div>
                        <div><strong>Status:</strong> <span class="status <?php echo $pemesanan['status']; ?>"><?php echo $pemesanan['status']; ?></span></div>
                    </div>
                    <div class="actions" style="margin-top: 1.5rem; text-align: center;">
                        <a href="cetak_tiket.php?kode=<?php echo $pemesanan['kode_booking']; ?>" class="btn" target="_blank">Cetak Tiket</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> WisataLocal - Sistem Pemesanan Tiket Wisata</p>
        </div>
    </footer>
</body>
</html>