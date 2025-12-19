<?php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = (new Database())->getConnection();
autoBackupIfNeeded($db);

function autoBackupIfNeeded($db) {

    // interval backup (contoh: 1 hari)
    $interval = 60 * 60 * 24; // 24 jam

    $lastBackup = $db->query(
        "SELECT backup_time FROM backup_log ORDER BY backup_time DESC LIMIT 1"
    )->fetchColumn();

    if ($lastBackup && (time() - strtotime($lastBackup)) < $interval) {
        return; // belum waktunya backup
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filename = "auto_backup_{$timestamp}.sql";
    $backupDir = __DIR__ . '/../backups';
    $filepath = $backupDir . '/' . $filename;

    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';

    $command = sprintf(
        '"%s" --host=%s --user=%s --password=%s --single-transaction --routines --events --triggers %s > "%s"',
        $mysqldump,
        escapeshellarg(DB_HOST),
        escapeshellarg(DB_USER),
        escapeshellarg(DB_PASS),
        escapeshellarg(DB_NAME),
        $filepath
    );

    exec($command, $output, $result);

    if ($result === 0 && file_exists($filepath) && filesize($filepath) > 1000) {

        // disable FK di file sql
        $sql = file_get_contents($filepath);
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n\n" . $sql . "\n\nSET FOREIGN_KEY_CHECKS=1;";
        file_put_contents($filepath, $sql);

        // simpan log backup
        $stmt = $db->prepare("INSERT INTO backup_log (backup_time) VALUES (NOW())");
        $stmt->execute();
    }
}

// Handle backup action
if (isset($_GET['action']) && $_GET['action'] === 'backup') {

    $timestamp = date('Y-m-d_H-i-s');
    $filename = "backup_{$timestamp}.sql";
    $backupDir = __DIR__ . '/../backups';
    $filepath = $backupDir . '/' . $filename;

    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }

    $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';

    // Dump database ke file sementara
    $command = sprintf(
        '"%s" --host=%s --user=%s --password=%s --single-transaction --routines --events --triggers %s > "%s"',
        $mysqldump,
        escapeshellarg(DB_HOST),
        escapeshellarg(DB_USER),
        escapeshellarg(DB_PASS),
        escapeshellarg(DB_NAME),
        $filepath
    );

    exec($command, $output, $result);

    // Validasi dump
    if ($result !== 0 || !file_exists($filepath) || filesize($filepath) < 1000) {
        if (file_exists($filepath)) unlink($filepath);
        die('Backup gagal');
    }

    // 🔴 TAMBAHKAN FOREIGN KEY CHECK HANDLER
    $sql = file_get_contents($filepath);

    $sql = "SET FOREIGN_KEY_CHECKS=0;\n\n" . $sql . "\n\nSET FOREIGN_KEY_CHECKS=1;";

    file_put_contents($filepath, $sql);

    // Download file
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);

    unlink($filepath);
    exit;
}

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
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            margin: 0;
        }

        .backup-btn {
            margin-left: auto;
        }
    </style>
</head>

<body>
    <?php include 'header-admin.php'; ?>

    <main class="container">
        <div class="dashboard-header">
            <h1>Dashboard Admin</h1>
            <a href="?action=backup" class="btn btn-primary backup-btn">
                <i class="fa fa-download"></i> Backup Database
            </a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="icon"><i class="fa fa-map-marker-alt"></i></div>
                <div>
                    <h3><?php echo $stats['total_destinasi']; ?></h3>
                    <p>Destinasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-ticket-alt"></i></div>
                <div>
                    <h3><?php echo $stats['total_pemesanan']; ?></h3>
                    <p>Total Pemesanan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-calendar-day"></i></div>
                <div>
                    <h3><?php echo $stats['pemesanan_hari_ini']; ?></h3>
                    <p>Pemesanan Hari Ini</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-wallet"></i></div>
                <div>
                    <h3><?php echo formatRupiah($stats['total_pendapatan']); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
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
                                <?php if ($order['status'] == 'pending'): ?>
                                    <a class="btn btn-primary btn-small" title="Confirm" href="pemesanan.php?action=confirm&kode=<?php echo $order['kode_booking']; ?>">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a class="btn btn-cancel btn-small" title="Cancel" href="pemesanan.php?action=cancel&kode=<?php echo $order['kode_booking']; ?>">
                                        <i class="fa fa-times"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>