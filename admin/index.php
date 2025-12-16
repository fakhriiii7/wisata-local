<?php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = (new Database())->getConnection();

// Get statistics
$stats = [
    'total_destinasi' => $db->query("SELECT COUNT(*) FROM destinasi")->fetchColumn(),
    'total_pemesanan' => $db->query("SELECT COUNT(*) FROM pemesanan")->fetchColumn(),
    'pemesanan_hari_ini' => $db->query("SELECT COUNT(*) FROM pemesanan WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'total_pendapatan' => $db->query("SELECT SUM(total_harga) FROM pemesanan WHERE status = 'confirmed'")->fetchColumn() ?? 0
];

// Recent orders
$stmt = $db->query("SELECT p.*, d.nama_destinasi FROM pemesanan p LEFT JOIN destinasi d ON p.destinasi_id = d.id ORDER BY p.created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../assets/favicon.png">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-nav {
            background: #34495e;
        }

        .admin-nav a {
            color: white;
            padding: 1rem;
            display: inline-block;
        }

        .admin-nav a:hover {
            background: #2c3e50;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <h2>WisataLocal - Admin</h2>
                </div>
                <div class="nav-links">
                    <a href="index.php">Dashboard</a>
                    <a href="destinasi.php">Destinasi</a>
                    <a href="pemesanan.php">Pemesanan</a>
                    <a href="users.php">Users</a>
                    <a href="laporan.php">Laporan</a>
                    <a href="../index.php">Website</a>
                    <a href="login.php?logout=1">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <h1>Dashboard Admin</h1>

        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $stats['total_destinasi']; ?></h3>
                <p>Destinasi</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['total_pemesanan']; ?></h3>
                <p>Total Pemesanan</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['pemesanan_hari_ini']; ?></h3>
                <p>Pemesanan Hari Ini</p>
            </div>
            <div class="stat-card">
                <h3><?php echo formatRupiah($stats['total_pendapatan']); ?></h3>
                <p>Total Pendapatan</p>
            </div>
        </div>

        <h2>Pemesanan Terbaru</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Destinasi</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td><?php echo $order['kode_booking']; ?></td>
                            <td><?php echo $order['nama_pemesan']; ?></td>
                            <td><?php echo $order['nama_destinasi']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($order['tanggal_berkunjung'])); ?></td>
                            <td><?php echo formatRupiah($order['total_harga']); ?></td>
                            <td>
                                <span class="status <?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
                            </td>
                            <td>
                                <a href="pemesanan.php?action=confirm&kode=<?php echo $order['kode_booking']; ?>">Confirm</a>
                                <a href="pemesanan.php?action=cancel&kode=<?php echo $order['kode_booking']; ?>">Cancel</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>