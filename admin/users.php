<?php
require_once '../config/config.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = (new Database())->getConnection();

// Handle actions
if(isset($_GET['action'])) {
    if($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $user_id = $_GET['id'];
        
        // Check if user has orders
        $stmt = $db->prepare("SELECT COUNT(*) as total_orders FROM pemesanan WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user_orders = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user_orders['total_orders'] > 0) {
            $error = "Tidak dapat menghapus user yang sudah memiliki pesanan";
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
            if($stmt->execute([$user_id])) {
                $success = "User berhasil dihapus";
            } else {
                $error = "Gagal menghapus user";
            }
        }
    }
}

// Search and Pagination
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query with search
if(!empty($search)) {
    $searchParam = "%{$search}%";
    // Get total count
    $countStmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM users u 
        WHERE u.role = 'user' 
        AND (u.nama_lengkap LIKE ? OR u.email LIKE ? OR u.no_telepon LIKE ?)
    ");
    $countStmt->execute([$searchParam, $searchParam, $searchParam]);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Get users with pagination
    $stmt = $db->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM pemesanan p WHERE p.user_id = u.id) as total_pesanan,
               (SELECT MAX(created_at) FROM pemesanan p WHERE p.user_id = u.id) as last_order
        FROM users u 
        WHERE u.role = 'user' 
        AND (u.nama_lengkap LIKE ? OR u.email LIKE ? OR u.no_telepon LIKE ?)
        ORDER BY u.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $searchParam, PDO::PARAM_STR);
    $stmt->bindValue(2, $searchParam, PDO::PARAM_STR);
    $stmt->bindValue(3, $searchParam, PDO::PARAM_STR);
    $stmt->bindValue(4, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(5, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Get total count
    $countStmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $perPage);
    
    // Get users with pagination
    $stmt = $db->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM pemesanan p WHERE p.user_id = u.id) as total_pesanan,
               (SELECT MAX(created_at) FROM pemesanan p WHERE p.user_id = u.id) as last_order
        FROM users u 
        WHERE u.role = 'user' 
        ORDER BY u.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get statistics (all users, not paginated)
$statsStmt = $db->query("
    SELECT 
        COUNT(*) as total_users,
        SUM((SELECT COUNT(*) FROM pemesanan p WHERE p.user_id = u.id)) as total_pesanan,
        COUNT(CASE WHEN (SELECT COUNT(*) FROM pemesanan p WHERE p.user_id = u.id) > 0 THEN 1 END) as user_aktif
    FROM users u 
    WHERE u.role = 'user'
");
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'header-admin.php'; ?>

    <main class="container">
        <h1>Kelola User</h1>

        <?php if(isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Search Form -->
        <div style="margin-bottom: 1.5rem;">
            <form method="GET" action="users.php" style="display: flex; gap: 0.5rem; align-items: center; max-width: 500px;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Cari user (nama, email, telepon)..." 
                       style="flex: 1; padding: 0.8rem; border: 2px solid #e9ecef; border-radius: 5px; font-size: 1rem;">
                <button type="submit" class="btn-primary" style="padding: 0.8rem 1.5rem;">
                    <i class="fa fa-search"></i> Cari
                </button>
                <?php if(!empty($search)): ?>
                    <a href="users.php" class="btn-secondary" style="padding: 0.8rem 1.5rem;">
                        <i class="fa fa-times"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
            <?php if(!empty($search)): ?>
                <p style="margin-top: 0.5rem; color: #666;">
                    Menampilkan <?php echo count($users); ?> dari <?php echo $totalRecords; ?> hasil untuk "<?php echo htmlspecialchars($search); ?>"
                </p>
            <?php endif; ?>
        </div>

        <!-- User Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="icon"><i class="fa fa-users"></i></div>
                <div>
                    <h3><?php echo $totalRecords; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-receipt"></i></div>
                <div>
                    <h3><?php echo $stats['total_pesanan'] ?? 0; ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-user-check"></i></div>
                <div>
                    <h3><?php echo $stats['user_aktif'] ?? 0; ?></h3>
                    <p>User Aktif</p>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama User</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Total Pesanan</th>
                        <th>Terdaftar</th>
                        <th>Pesanan Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td>
                            <strong><?php echo $user['nama_lengkap']; ?></strong>
                        </td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['no_telepon'] ?: '-'; ?></td>
                        <td>
                                <?php if($user['total_pesanan'] > 0): ?>
                                    <a class="btn btn-small btn-primary" href="pemesanan.php?user_id=<?php echo $user['id']; ?>">
                                        <i class="fa fa-list"></i>
                                        <span style="margin-left:6px; font-weight:600;"><?php echo $user['total_pesanan']; ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="muted">0 pesanan</span>
                                <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if($user['last_order']): ?>
                                <?php echo date('d/m/Y', strtotime($user['last_order'])); ?>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                                <?php if($user['total_pesanan'] == 0): ?>
                                    <a class="btn btn-small btn-cancel" href="users.php?action=delete&id=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Hapus user <?php echo $user['nama_lengkap']; ?>?')" title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="muted" style="font-size:0.9rem;">Tidak dapat dihapus</span>
                                <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                            Belum ada user terdaftar
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div style="margin-top: 2rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn-secondary" style="padding: 0.6rem 1rem;">
                    <i class="fa fa-chevron-left"></i> Sebelumnya
                </a>
            <?php endif; ?>
            
            <span style="padding: 0.6rem 1rem; color: #2c3e50;">
                Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?> 
                (Total: <?php echo $totalRecords; ?> user)
            </span>
            
            <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
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