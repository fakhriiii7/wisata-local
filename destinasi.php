<?php
require_once 'config/config.php';
$db = (new Database())->getConnection();

// Get all destinasi
$stmt = $db->query("SELECT * FROM destinasi ORDER BY nama_destinasi");
$destinasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header-public.php'; ?>

<h1>Destinasi Wisata</h1>

<div class="grid">
    <?php foreach($destinasi as $d): ?>
    <div class="card">
        <h3><?php echo $d['nama_destinasi']; ?></h3>
        <p><?php echo $d['deskripsi']; ?></p>
        <div class="price-info">
            <div>Dewasa: <?php echo formatRupiah($d['harga_dewasa']); ?></div>
            <div>Anak: <?php echo formatRupiah($d['harga_anak']); ?></div>
            <div>Kuota: <?php echo $d['kuota_harian']; ?> orang/hari</div>
        </div>
        <a href="pemesanan.php?destinasi_id=<?php echo $d['id']; ?>" class="btn">Pesan Tiket</a>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>