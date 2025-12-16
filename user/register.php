<?php
require_once '../config/config.php';

// Redirect if already logged in
if(isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Process registration
if($_POST) {
    $db = (new Database())->getConnection();
    
    $nama = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $telepon = sanitize($_POST['no_telepon']);
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->rowCount() > 0) {
        $error = "Email sudah terdaftar";
    } elseif(strlen($password) < 6) {
        $error = "Password minimal 6 karakter";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO users (nama_lengkap, email, password, no_telepon, role) VALUES (?, ?, ?, ?, 'user')");
        if($stmt->execute([$nama, $email, $hashed_password, $telepon])) {
            $success = "Registrasi berhasil! Silakan login.";
            echo "<script>setTimeout(() => window.location.href = 'login.php', 2000)</script>";
        } else {
            $error = "Gagal mendaftar";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="auth-form">
        <h2>Daftar Akun Baru</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="no_telepon" placeholder="No. Telepon">
            <input type="password" name="password" placeholder="Password (min. 6 karakter)" required>
            <button type="submit" class="btn">Daftar</button>
        </form>
        
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        <p><a href="../index.php">← Kembali ke Beranda</a></p>
    </div>
</body>
</html>