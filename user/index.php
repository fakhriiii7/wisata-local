<?php
require_once '../config/config.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../index.php');
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Get user stats
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_pesanan,
        SUM(CASE WHEN status = 'confirmed' THEN total_harga ELSE 0 END) as total_pengeluaran,
        SUM(CASE WHEN status = 'confirmed' THEN (jumlah_dewasa + jumlah_anak) ELSE 0 END) as total_pengunjung,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
    FROM pemesanan 
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent orders
$stmt = $db->prepare("
    SELECT p.*, d.nama_destinasi 
    FROM pemesanan p 
    LEFT JOIN destinasi d ON p.destinasi_id = d.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header-user.php'; ?>

<section class="user-dashboard container">
    <div class="dashboard-top">
        <div class="dashboard-header-compact">
            <h1>Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?></h1>
            <p class="muted">Ringkasan singkat aktivitas Anda</p>
        </div>

        <div class="stats compact-stats">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <h3><?php echo $stats['total_pesanan'] ?? 0; ?></h3>
                <p>Total Pesanan</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <h3><?php echo $stats['total_pengunjung'] ?? 0; ?></h3>
                <p>Tiket Dikonfirmasi</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <h3><?php echo formatRupiah($stats['total_pengeluaran'] ?? 0); ?></h3>
                <p>Pengeluaran</p>
            </div>
        </div>
    </div>

    <div class="recent-orders compact">
        <div class="section-header">
            <h2>Pesanan Terbaru</h2>
            <a href="riwayat.php" class="view-all">Lihat Semua →</a>
        </div>

        <?php if ($pesanan): ?>
            <ul class="recent-list">
                <?php foreach ($pesanan as $p): ?>
                    <li class="recent-item">
                        <div class="left">
                            <strong><?php echo $p['kode_booking']; ?></strong>
                            <small><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></small>
                            <div class="muted"><?php echo $p['nama_destinasi']; ?> — <?php echo $p['jumlah_dewasa'] + $p['jumlah_anak']; ?> orang</div>
                        </div>
                        <div class="right">
                            <span class="status <?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span>
                            <?php if ($p['status'] != 'cancelled'): ?>
                                <a href="../cetak_tiket.php?kode=<?php echo $p['kode_booking']; ?>" class="btn-small" target="_blank">🖨️</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>Belum ada pemesanan</h3>
                <p>Mulai petualangan Anda dengan memesan tiket wisata pertama</p>
                <a href="../pemesanan.php" class="btn">Pesan Tiket Pertama</a>
            </div>
        <?php endif; ?>
    </div>
    </section>

<?php include '../includes/footer.php'; ?>