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

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filter by user if specified
$user_filter = '';
$whereClause = '';
$params = [];
$countParams = [];

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $whereClause = " WHERE p.user_id = ?";
    $params[] = $_GET['user_id'];
    $countParams[] = $_GET['user_id'];
}

// Get total count for pagination
$countQuery = "
    SELECT COUNT(*) as total 
    FROM pemesanan p 
    {$whereClause}
";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($countParams);
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $perPage);

// Get pemesanan with pagination
if (!empty($params)) {
    // With user filter
    $query = "
        SELECT p.*, d.nama_destinasi, u.nama_lengkap 
        FROM pemesanan p 
        LEFT JOIN destinasi d ON p.destinasi_id = d.id 
        LEFT JOIN users u ON p.user_id = u.id 
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $params[0], PDO::PARAM_INT);
    $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    // Without user filter
    $query = "
        SELECT p.*, d.nama_destinasi, u.nama_lengkap 
        FROM pemesanan p 
        LEFT JOIN destinasi d ON p.destinasi_id = d.id 
        LEFT JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$pemesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
    <title>Kelola Pemesanan</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'header-admin.php'; ?>

    <main class="container">
        <h1>Kelola Pemesanan</h1>
        
        <?php if(isset($_GET['user_id']) && !empty($_GET['user_id'])): ?>
            <p style="margin-bottom: 1rem; color: #666;">
                Menampilkan pemesanan untuk user tertentu. 
                <a href="pemesanan.php" style="color: #976a3c; text-decoration: underline;">Tampilkan semua pemesanan</a>
            </p>
        <?php endif; ?>

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
                                    <a class="btn btn-primary btn-small" title="Confirm" href="pemesanan.php?action=confirm&kode=<?php echo $p['kode_booking']; ?>">
                                        <i class="fa fa-check"></i>
                                    </a>
                                    <a class="btn btn-cancel btn-small" title="Cancel" href="pemesanan.php?action=cancel&kode=<?php echo $p['kode_booking']; ?>">
                                        <i class="fa fa-times"></i>
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-small" title="Cetak" href="../cetak_tiket.php?kode=<?php echo $p['kode_booking']; ?>" target="_blank">
                                    <i class="fa fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div style="margin-top: 2rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['user_id']) ? '&user_id=' . urlencode($_GET['user_id']) : ''; ?>" 
                   class="btn-secondary" style="padding: 0.6rem 1rem;">
                    <i class="fa fa-chevron-left"></i> Sebelumnya
                </a>
            <?php endif; ?>
            
            <span style="padding: 0.6rem 1rem; color: #2c3e50;">
                Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?> 
                (Total: <?php echo $totalRecords; ?> pemesanan)
            </span>
            
            <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['user_id']) ? '&user_id=' . urlencode($_GET['user_id']) : ''; ?>" 
                   class="btn-secondary" style="padding: 0.6rem 1rem;">
                    Selanjutnya <i class="fa fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>
</body>
<?php
include '../includes/footer.php';
?>
</html>