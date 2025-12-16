<?php
require_once 'config/config.php';

$kode = $_GET['kode'] ?? '';
if(!$kode) die("Kode booking tidak valid");

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.*, d.nama_destinasi FROM pemesanan p LEFT JOIN destinasi d ON p.destinasi_id = d.id WHERE p.kode_booking = ?");
$stmt->execute([$kode]);
$pemesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$pemesanan) die("Data tidak ditemukan");

// Cek status - jika cancelled, tidak bisa cetak
if($pemesanan['status'] == 'cancelled') {
    die("Tiket tidak dapat dicetak karena pesanan telah dibatalkan");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tiket Wisata - <?php echo $pemesanan['kode_booking']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .ticket { border: 2px solid #333; padding: 20px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; background: #f4f4f4; padding: 10px; margin-bottom: 20px; }
        .info { margin-bottom: 10px; }
        .barcode { text-align: center; font-family: monospace; font-size: 18px; margin: 20px 0; }
        .status { 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-weight: bold; 
            text-transform: uppercase;
        }
        .status.confirmed { background: #d4edda; color: #155724; }
        .status.pending { background: #fff3cd; color: #856404; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>TIKET WISATA</h1>
            <p>Wisata Local</p>
        </div>
        
        <div class="info"><strong>Kode Booking:</strong> <?php echo $pemesanan['kode_booking']; ?></div>
        <div class="info"><strong>Nama:</strong> <?php echo $pemesanan['nama_pemesan']; ?></div>
        <div class="info"><strong>Destinasi:</strong> <?php echo $pemesanan['nama_destinasi']; ?></div>
        <div class="info"><strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($pemesanan['tanggal_berkunjung'])); ?></div>
        <div class="info"><strong>Jumlah:</strong> <?php echo $pemesanan['jumlah_dewasa'] + $pemesanan['jumlah_anak']; ?> orang</div>
        <div class="info"><strong>Total:</strong> <?php echo formatRupiah($pemesanan['total_harga']); ?></div>
        <div class="info">
            <strong>Status:</strong> 
            <span class="status <?php echo $pemesanan['status']; ?>"><?php echo $pemesanan['status']; ?></span>
        </div>
        
        <div class="barcode">
            <?php echo $pemesanan['kode_booking']; ?>
        </div>
        
        <div style="text-align: center; font-size: 12px; color: #666;">
            <p>Tunjukkan tiket ini di lokasi wisata</p>
            <?php if($pemesanan['status'] == 'pending'): ?>
                <p style="color: #f39c12; font-weight: bold;">● Menunggu konfirmasi</p>
            <?php elseif($pemesanan['status'] == 'confirmed'): ?>
                <p style="color: #27ae60; font-weight: bold;">● Tiket aktif</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Cetak Tiket</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>