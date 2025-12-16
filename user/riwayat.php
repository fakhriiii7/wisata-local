<?php
require_once '../config/config.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../index.php');
}

$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Handle cancel action
if (isset($_GET['cancel']) && isset($_GET['kode'])) {
    $kode = sanitize($_GET['kode']);

    // Verify ownership and status
    $stmt = $db->prepare("SELECT status FROM pemesanan WHERE kode_booking = ? AND user_id = ?");
    $stmt->execute([$kode, $user_id]);
    $pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pesanan && $pesanan['status'] == 'pending') {
        $stmt = $db->prepare("UPDATE pemesanan SET status = 'cancelled' WHERE kode_booking = ? AND user_id = ?");
        $stmt->execute([$kode, $user_id]);
        $success = "Pesanan berhasil dibatalkan";
    }
}

// Get user's orders
$stmt = $db->prepare("
    SELECT p.*, d.nama_destinasi 
    FROM pemesanan p 
    LEFT JOIN destinasi d ON p.destinasi_id = d.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header-user.php'; ?>

<h1>Riwayat Pemesanan</h1>

<?php if (isset($success)): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

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
                    <td><?php echo $p['kode_booking']; ?></td>
                    <td><?php echo $p['nama_destinasi']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($p['tanggal_berkunjung'])); ?></td>
                    <td><?php echo $p['jumlah_dewasa'] + $p['jumlah_anak']; ?> orang</td>
                    <td><?php echo formatRupiah($p['total_harga']); ?></td>
                    <td>
                        <span class="status <?php echo $p['status']; ?>"><?php echo $p['status']; ?></span>
                    </td>
                    <td>
                        <a href="../konfirmasi.php?kode=<?php echo $p['kode_booking']; ?>">Detail</a>
                        <?php if ($p['status'] != 'cancelled'): ?>
                            <a href="../cetak_tiket.php?kode=<?php echo $p['kode_booking']; ?>" target="_blank">Cetak</a>
                        <?php else: ?>
                            <span style="color: #95a5a6; font-style: italic;">Tidak dapat dicetak</span>
                        <?php endif; ?>

                        <?php if ($p['status'] == 'pending'): ?>
                            <a href="riwayat.php?cancel=1&kode=<?php echo $p['kode_booking']; ?>"
                                onclick="return confirm('Yakin ingin membatalkan pesanan <?php echo $p['kode_booking']; ?>?')"
                                style="color: #e74c3c;">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>