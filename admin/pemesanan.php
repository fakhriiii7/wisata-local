<?php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = (new Database())->getConnection();

// Handle actions
if (isset($_GET['action']) && isset($_GET['kode'])) {
    if ($_GET['action'] == 'confirm') {
        $db->prepare("UPDATE pemesanan SET status = 'confirmed' WHERE kode_booking = ?")->execute([$_GET['kode']]);
    } elseif ($_GET['action'] == 'cancel') {
        $db->prepare("UPDATE pemesanan SET status = 'cancelled' WHERE kode_booking = ?")->execute([$_GET['kode']]);
    }
}

// Get all pemesanan
$pemesanan = $db->query("
    SELECT p.*, d.nama_destinasi 
    FROM pemesanan p 
    LEFT JOIN destinasi d ON p.destinasi_id = d.id 
    ORDER BY p.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Filter by user if specified
$user_filter = '';
$params = [];

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_filter = " WHERE p.user_id = ?";
    $params[] = $_GET['user_id'];
}

$query = "
    SELECT p.*, d.nama_destinasi, u.nama_lengkap 
    FROM pemesanan p 
    LEFT JOIN destinasi d ON p.destinasi_id = d.id 
    LEFT JOIN users u ON p.user_id = u.id 
    $user_filter
    ORDER BY p.created_at DESC
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$pemesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Kelola Pemesanan</title>
    <link rel="stylesheet" href="../css/style.css">
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
        <h1>Kelola Pemesanan</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Destinasi</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pemesanan as $p): ?>
                        <tr>
                            <td><?php echo $p['kode_booking']; ?></td>
                            <td><?php echo $p['nama_pemesan']; ?></td>
                            <td><?php echo $p['email']; ?></td>
                            <td><?php echo $p['nama_destinasi']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['tanggal_berkunjung'])); ?></td>
                            <td><?php echo $p['jumlah_dewasa'] + $p['jumlah_anak']; ?> orang</td>
                            <td><?php echo formatRupiah($p['total_harga']); ?></td>
                            <td>
                                <span class="status <?php echo $p['status']; ?>"><?php echo $p['status']; ?></span>
                            </td>
                            <td>
                                <?php if ($p['status'] == 'pending'): ?>
                                    <a href="pemesanan.php?action=confirm&kode=<?php echo $p['kode_booking']; ?>">Confirm</a>
                                    <a href="pemesanan.php?action=cancel&kode=<?php echo $p['kode_booking']; ?>">Cancel</a>
                                <?php endif; ?>
                                <a href="../cetak_tiket.php?kode=<?php echo $p['kode_booking']; ?>" target="_blank">Cetak</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>