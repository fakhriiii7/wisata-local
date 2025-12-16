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

// Get all users (exclude admin)
$users = $db->query("
    SELECT u.*, 
           (SELECT COUNT(*) FROM pemesanan p WHERE p.user_id = u.id) as total_pesanan,
           (SELECT MAX(created_at) FROM pemesanan p WHERE p.user_id = u.id) as last_order
    FROM users u 
    WHERE u.role = 'user' 
    ORDER BY u.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <a href="../index.php"><img src="../assets/img/logo2-1.png" alt="WisataLocal" style="height:48px; display:block;"></a>
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
        <h1>Kelola User</h1>

        <?php if(isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- User Statistics -->
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo count($users); ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo array_sum(array_column($users, 'total_pesanan')); ?></h3>
                <p>Total Pesanan</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_filter($users, function($user) { return $user['total_pesanan'] > 0; })); ?></h3>
                <p>User Aktif</p>
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
                                <a href="pemesanan.php?user_id=<?php echo $user['id']; ?>" 
                                   style="color: #3498db; text-decoration: none;">
                                    <?php echo $user['total_pesanan']; ?> pesanan
                                </a>
                            <?php else: ?>
                                <span style="color: #95a5a6;">0 pesanan</span>
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
                                <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" 
                                   onclick="return confirm('Hapus user <?php echo $user['nama_lengkap']; ?>?')"
                                   style="color: #e74c3c;">Hapus</a>
                            <?php else: ?>
                                <span style="color: #95a5a6; font-size: 0.8rem;">Tidak dapat dihapus</span>
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
    </main>
</body>
</html>