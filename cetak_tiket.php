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
        body { font-family: Arial, sans-serif; margin: 18px; background:#f6f7fb; }
        .ticket { background: #fff; border-radius: 12px; padding: 18px; max-width: 760px; margin: 20px auto; box-shadow: 0 12px 30px rgba(16,24,40,0.08); border: 1px solid rgba(0,0,0,0.06); }
        .header { display:flex; gap:1rem; align-items:center; padding:14px; border-radius:8px; background: linear-gradient(90deg, #f3dede 0%, #2f4f67 100%); color:#fff; margin-bottom:16px; }
        .header img { height:48px; display:block; }
        .header .brand { font-size:1.15rem; font-weight:700; }
        .header .tag { font-size:0.9rem; opacity:0.95; }
        .info-grid { display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:12px; }
        .info { padding:10px 12px; background:#fbfbfd; border-radius:8px; border:1px solid rgba(0,0,0,0.03); }
        .info strong { display:block; margin-bottom:6px; color:#333; }
        .right-col { display:flex; flex-direction:column; gap:10px; align-items:center; }
        .barcode { font-family: monospace; font-size:20px; padding:12px 16px; background:#f4f6fb; border-radius:8px; border:1px dashed rgba(0,0,0,0.06); }
        .qr { width:140px; height:140px; background:linear-gradient(135deg,#e9eefb,#f4f4ff); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#9aa6c7; font-size:12px; border:1px solid rgba(0,0,0,0.04); }
        .status { padding:6px 12px; border-radius:999px; font-weight:700; text-transform:capitalize; display:inline-block; }
        .status.confirmed { background:#d6f5e0; color:#0b7a3a; border:1px solid rgba(34,197,94,0.12); }
        .status.pending { background:#fff3cd; color:#7a4f2b; border:1px solid rgba(243,156,18,0.08); }
        .actions { display:flex; gap:10px; justify-content:center; margin-top:14px; }
        .print-btn, .close-btn { padding:10px 16px; border-radius:999px; border:none; cursor:pointer; font-weight:700; }
        .print-btn { background: linear-gradient(90deg,var(--brown-dark,#34495e),#976a3c); color:#fff; box-shadow:0 12px 30px rgba(151,106,60,0.12); }
        .close-btn { background:#f1f3f6; color:#34495e; }
        .note { text-align:center; color:#6b7280; font-size:0.95rem; margin-top:12px; }
        @media print { body { margin:0; background: #fff; } .no-print { display:none; } .ticket { box-shadow:none; border: none; max-width:100%; margin:0; border-radius:0; } }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <img src="assets/img/logo1.png" alt="logo">
            <div>
                <div class="brand">WisataLocal</div>
                <div class="tag">Tiket & Bukti Pemesanan</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info"><strong>Kode Booking</strong><?php echo $pemesanan['kode_booking']; ?></div>
            <div class="info"><strong>Nama</strong><?php echo $pemesanan['nama_pemesan']; ?></div>
            <div class="info"><strong>Destinasi</strong><?php echo $pemesanan['nama_destinasi']; ?></div>
            <div class="info"><strong>Tanggal</strong><?php echo date('d F Y', strtotime($pemesanan['tanggal_berkunjung'])); ?></div>
            <div class="info"><strong>Jumlah</strong><?php echo $pemesanan['jumlah_dewasa'] + $pemesanan['jumlah_anak']; ?> orang</div>
            <div class="info"><strong>Total</strong><?php echo formatRupiah($pemesanan['total_harga']); ?></div>
        </div>

        <div style="display:flex; gap:14px; align-items:flex-start;">
            <div style="flex:1">
                <div class="barcode"><?php echo $pemesanan['kode_booking']; ?></div>
                <div class="note">Tunjukkan tiket ini di lokasi wisata untuk validasi.</div>
            </div>
            <div class="right-col">
                <div class="qr">QR / Barcode</div>
                <div class="info" style="text-align:center;"><strong>Status</strong><div style="margin-top:8px;"><span class="status <?php echo $pemesanan['status']; ?>"><?php echo $pemesanan['status']; ?></span></div></div>
            </div>
        </div>

        <div class="actions no-print">
            <button class="print-btn" onclick="window.print()">Cetak Tiket</button>
            <button class="close-btn" onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>