<?php
require_once '../config/config.php';

// Redirect if already logged in
if(isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Get data dari session atau URL untuk pre-fill form
$prefill_data = [
    'nama_lengkap' => '',
    'email' => '',
    'no_telepon' => ''
];

// Cek session untuk data pemesanan pending
if(isset($_SESSION['pending_order_data'])) {
    $prefill_data = $_SESSION['pending_order_data'];
}

// Cek URL parameter email
if(isset($_GET['email']) && !empty($_GET['email'])) {
    $prefill_data['email'] = sanitize($_GET['email']);
}

// Process registration
if($_POST) {
    $db = (new Database())->getConnection();
    
    $nama = sanitize($_POST['nama_lengkap']);
    $email = trim(strtolower(sanitize($_POST['email']))); // Normalisasi email ke lowercase
    $password = $_POST['password'];
    $telepon = sanitize($_POST['no_telepon']);
    
    // Validasi format email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid";
    } else {
        // Check if email exists (case-insensitive)
        $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = ?");
        $stmt->execute([$email]);
        
        if($stmt->rowCount() > 0) {
            $error = "Email sudah terdaftar. Silakan gunakan email lain atau <a href='login.php'>login di sini</a>.";
        } elseif(strlen($password) < 6) {
            $error = "Password minimal 6 karakter";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("INSERT INTO users (nama_lengkap, email, password, no_telepon, role) VALUES (?, ?, ?, ?, 'user')");
            if($stmt->execute([$nama, $email, $hashed_password, $telepon])) {
                // Dapatkan user_id yang baru dibuat
                $new_user_id = $db->lastInsertId();
                
                // Link semua pesanan dengan email yang sama ke user_id baru (case-insensitive)
                $stmt = $db->prepare("UPDATE pemesanan SET user_id = ? WHERE LOWER(email) = ? AND user_id IS NULL");
                $stmt->execute([$new_user_id, $email]);
                
                // Hapus session pending order data
                if(isset($_SESSION['pending_order_data'])) {
                    unset($_SESSION['pending_order_data']);
                }
                
                $success = "Registrasi berhasil! Riwayat pesanan Anda telah tersimpan. Silakan login.";
                echo "<script>setTimeout(() => window.location.href = 'login.php', 2000)</script>";
            } else {
                $error = "Gagal mendaftar";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar User</title>
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: linear-gradient(180deg,#f4f8ff 0%, #ffffff 100%); }
        .auth-wrap { min-height:80vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
        .auth-form { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 12px 30px rgba(25,118,210,0.08); max-width:520px; width:100%; }
        .auth-logo { display:block; margin:0 auto; max-width:140px; }
        .auth-form h2 { margin-top:0.75rem; color:#0b3560; font-size:1.25rem; }
        .auth-form input { width:100%; padding:0.75rem 0.9rem; margin:0.6rem 0; border-radius:8px; border:1px solid #e6eefb; }
        .auth-form .btn { background:#1976d2; color:#fff; border:none; padding:0.75rem; border-radius:8px; width:100%; cursor:pointer; box-shadow:0 8px 20px rgba(25,118,210,0.12); }
        .auth-form .btn:hover { transform:translateY(-3px); }
        .auth-links { margin-top:0.8rem; text-align:center; }
        .auth-links a { color:#1976d2; text-decoration:none; }
        .success { background:#e9f8ef; color:#0b6b3a; padding:0.6rem; border-radius:6px; }
        .error { background:#fff1f0; color:#b00020; padding:0.6rem; border-radius:6px; }
    </style>
</head>
<body>
    <div class="auth-wrap">
    <div class="auth-form">
        <img src="../assets/img/logo1.png" alt="Logo" class="auth-logo">
        <h2>Daftar Akun Baru</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($prefill_data['nama_lengkap']); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($prefill_data['email']); ?>" required>
            <input type="tel" name="no_telepon" placeholder="No. Telepon" value="<?php echo htmlspecialchars($prefill_data['no_telepon']); ?>">
            <input type="password" name="password" placeholder="Password (min. 6 karakter)" required>
            <button type="submit" class="btn">Daftar</button>
        </form>
        
        <div class="auth-links">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            <p><a href="../index.php">← Kembali ke Beranda</a></p>
        </div>
    </div>
    </div>
</body>
</html>