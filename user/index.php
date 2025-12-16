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

<div class="dashboard-header">
    <h1>Dashboard User</h1>
    <p>Selamat datang, <strong><?php echo $_SESSION['user_nama']; ?></strong>!</p>
</div>

<!-- Statistics Cards -->
<div class="stats">
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <h3><?php echo $stats['total_pesanan'] ?? 0; ?></h3>
        <p>Total Pesanan</p>
    </div>

    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <h3><?php echo $stats['total_pengunjung'] ?? 0; ?></h3>
        <p>Jumlah Tiket Dikonfirmasi</p>
    </div>

    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <h3><?php echo formatRupiah($stats['total_pengeluaran'] ?? 0); ?></h3>
        <p>Total Pengeluaran</p>
    </div>

    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <h3><?php echo $stats['pending_count'] ?? 0; ?></h3>
        <p>Menunggu</p>
    </div>
</div>

<!-- Status Summary -->
<div class="status-summary">
    <h2>Ringkasan Status</h2>
    <div class="status-cards">
        <div class="status-card pending">
            <span class="count"><?php echo $stats['pending_count'] ?? 0; ?></span>
            <span class="label">Pending</span>
        </div>
        <div class="status-card confirmed">
            <span class="count"><?php echo $stats['confirmed_count'] ?? 0; ?></span>
            <span class="label">Confirmed</span>
        </div>
        <div class="status-card cancelled">
            <span class="count"><?php echo $stats['cancelled_count'] ?? 0; ?></span>
            <span class="label">Cancelled</span>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h2>Aksi Cepat</h2>
    <div class="action-buttons">
        <a href="../pemesanan.php" class="action-btn primary">
            <span class="icon">🎫</span>
            <span class="text">Pesan Tiket Baru</span>
        </a>
        <a href="riwayat.php" class="action-btn secondary">
            <span class="icon">📋</span>
            <span class="text">Lihat Riwayat</span>
        </a>
        <a href="../destinasi.php" class="action-btn secondary">
            <span class="icon">🏞️</span>
            <span class="text">Jelajahi Destinasi</span>
        </a>
    </div>
</div>

<!-- Recent Orders -->
<div class="recent-orders">
    <div class="section-header">
        <h2>Pesanan Terbaru</h2>
        <a href="riwayat.php" class="view-all">Lihat Semua →</a>
    </div>

    <?php if ($pesanan): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode Booking</th>
                        <th>Destinasi</th>
                        <th>Tanggal Kunjung</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesanan as $p): ?>
                        <tr>
                            <td>
                                <strong><?php echo $p['kode_booking']; ?></strong>
                                <small><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></small>
                            </td>
                            <td><?php echo $p['nama_destinasi']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['tanggal_berkunjung'])); ?></td>
                            <td><?php echo $p['jumlah_dewasa'] + $p['jumlah_anak']; ?> orang</td>
                            <td><?php echo formatRupiah($p['total_harga']); ?></td>
                            <td>
                                <span class="status <?php echo $p['status']; ?>"><?php echo $p['status']; ?></span>
                            </td>
                            <td>
                                <div class="action-buttons-small">
                                    <a href="../konfirmasi.php?kode=<?php echo $p['kode_booking']; ?>"
                                        class="btn-small" title="Lihat Detail">
                                        👁️
                                    </a>
                                    <?php if ($p['status'] != 'cancelled'): ?>
                                        <a href="../cetak_tiket.php?kode=<?php echo $p['kode_booking']; ?>"
                                            class="btn-small" target="_blank" title="Cetak Tiket">
                                            🖨️
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #95a5a6; font-size: 0.8rem;">Tidak dapat dicetak</span>
                                    <?php endif; ?>

                                    <?php if ($p['status'] == 'pending'): ?>
                                        <a href="riwayat.php?cancel=1&kode=<?php echo $p['kode_booking']; ?>"
                                            class="btn-small" style="background: #e74c3c;"
                                            onclick="return confirm('Batalkan pesanan <?php echo $p['kode_booking']; ?>?')"
                                            title="Batalkan Pesanan">
                                            ❌
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h3>Belum ada pemesanan</h3>
            <p>Mulai petualangan Anda dengan memesan tiket wisata pertama</p>
            <a href="../pemesanan.php" class="btn">Pesan Tiket Pertama</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<style>
    .dashboard-header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 2rem;
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border-radius: 10px;
    }

    .dashboard-header h1 {
        margin-bottom: 0.5rem;
        font-size: 2.5rem;
    }

    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    /* Stats Improvement */
    .stat-card {
        position: relative;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-card h3 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    /* Status Summary */
    .status-summary {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin: 2rem 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .status-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .status-card {
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        color: white;
        font-weight: bold;
    }

    .status-card.pending {
        background: #f39c12;
    }

    .status-card.confirmed {
        background: #27ae60;
    }

    .status-card.cancelled {
        background: #e74c3c;
    }

    .status-card .count {
        display: block;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    /* Quick Actions */
    .quick-actions {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin: 2rem 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .action-btn.secondary {
        background: #f8f9fa;
        color: #333;
        border: 2px solid #e9ecef;
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .action-btn .icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .action-btn .text {
        font-weight: bold;
    }

    /* Recent Orders */
    .recent-orders {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .view-all {
        color: #3498db;
        text-decoration: none;
        font-weight: bold;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    /* Table Improvements */
    table small {
        display: block;
        color: #666;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .action-buttons-small {
        display: flex;
        gap: 0.5rem;
    }

    .btn-small {
        padding: 0.3rem 0.6rem;
        background: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.8rem;
    }

    .btn-small:hover {
        background: #2980b9;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #666;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        margin-bottom: 1rem;
        color: #333;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header h1 {
            font-size: 2rem;
        }

        .stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .action-buttons {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .table-container {
            overflow-x: auto;
        }
    }
</style>