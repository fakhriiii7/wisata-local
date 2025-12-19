<?php
require_once 'config/config.php';

$kode = $_GET['kode'] ?? '';
if(!$kode) redirect('index.php');

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.*, d.nama_destinasi FROM pemesanan p LEFT JOIN destinasi d ON p.destinasi_id = d.id WHERE p.kode_booking = ?");
$stmt->execute([$kode]);
$pemesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$pemesanan) redirect('index.php');
?>

<?php include 'includes/header-public.php'; ?>

<div class="confirmation">
    <h1>Pemesanan Berhasil!</h1>
    
    <div class="booking-info">
        <h2>Detail Pemesanan</h2>
        <div class="info-grid">
            <div><strong>Kode Booking:</strong> <?php echo $pemesanan['kode_booking']; ?></div>
            <div><strong>Nama:</strong> <?php echo $pemesanan['nama_pemesan']; ?></div>
            <div><strong>Destinasi:</strong> <?php echo $pemesanan['nama_destinasi']; ?></div>
            <div><strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($pemesanan['tanggal_berkunjung'])); ?></div>
            <div><strong>Jumlah:</strong> <?php echo $pemesanan['jumlah_dewasa'] + $pemesanan['jumlah_anak']; ?> orang</div>
            <div><strong>Total:</strong> <?php echo formatRupiah($pemesanan['total_harga']); ?></div>
            <div><strong>Status:</strong> <span class="status <?php echo $pemesanan['status']; ?>"><?php echo $pemesanan['status']; ?></span></div>
        </div>
    </div>
    
    <!-- TAMBAH INSTRUKSI BERDASARKAN STATUS -->
    <div class="instruction-box">
        <h4 class="instruction-title">Instruksi Selanjutnya:</h4>
        <ol class="instruction-list">
            <li>Simpan kode booking Anda: <strong><?php echo $pemesanan['kode_booking']; ?></strong></li>
            <li>Tunjukkan kode booking saat tiba di lokasi wisata</li>
            <li>Lakukan pembayaran di lokasi sebelum masuk</li>
            <?php if($pemesanan['status'] == 'pending'): ?>
                <li class="status-note pending">● Menunggu konfirmasi admin - Anda bisa batalkan pesanan di dashboard</li>
            <?php elseif($pemesanan['status'] == 'confirmed'): ?>
                <li class="status-note confirmed">● Pesanan sudah dikonfirmasi - Tiket siap digunakan</li>
            <?php elseif($pemesanan['status'] == 'cancelled'): ?>
                <li class="status-note cancelled">● Pesanan telah dibatalkan - Tiket tidak dapat digunakan</li>
            <?php endif; ?>
        </ol>
    </div>
    
    <div class="actions">
        <?php if($pemesanan['status'] != 'cancelled'): ?>
            <a href="cetak_tiket.php?kode=<?php echo $pemesanan['kode_booking']; ?>" class="btn" target="_blank">Cetak Tiket</a>
        <?php endif; ?>
        
        <a href="index.php" class="btn">Kembali ke Beranda</a>
        
        <?php if(!isLoggedIn()): ?>
            <a href="user/register.php" class="btn">Daftar untuk Simpan Riwayat</a>
        <?php else: ?>
            <a href="user/index.php" class="btn">Lihat Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>