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

        <!-- User Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="icon"><i class="fa fa-users"></i></div>
                <div>
                    <h3><?php echo count($users); ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-receipt"></i></div>
                <div>
                    <h3><?php echo array_sum(array_column($users, 'total_pesanan')); ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fa fa-user-check"></i></div>
                <div>
                    <h3><?php echo count(array_filter($users, function($user) { return $user['total_pesanan'] > 0; })); ?></h3>
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
    </main>
</body>
</html>