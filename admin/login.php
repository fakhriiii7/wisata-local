<?php
require_once '../config/config.php';

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

if(isLoggedIn() && isAdmin()) {
    redirect('index.php');
}

$error = '';

if($_POST) {
    $db = (new Database())->getConnection();
    
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_nama'] = $admin['nama_lengkap'];
        $_SESSION['user_email'] = $admin['email'];
        $_SESSION['user_role'] = $admin['role'];
        redirect('index.php');
    } else {
        $error = "Email atau password salah";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="icon" type="image/png" href="../assets/img/logo1.png">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: linear-gradient(180deg,#f4f8ff 0%, #ffffff 100%); }
        .auth-wrap { min-height:80vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
        .auth-form { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 12px 30px rgba(25,118,210,0.08); max-width:420px; width:100%; }
        .auth-logo { display:block; margin:0 auto; max-width:140px; }
        .auth-form h2 { margin-top:0.75rem; color:#0b3560; font-size:1.25rem; }
        .auth-form input { width:100%; padding:0.75rem 0.9rem; margin:0.6rem 0; border-radius:8px; border:1px solid #e6eefb; }
        .auth-form button { background:#1976d2; color:#fff; border:none; padding:0.75rem; border-radius:8px; width:100%; cursor:pointer; box-shadow:0 8px 20px rgba(25,118,210,0.12); }
        .auth-form button:hover { transform:translateY(-3px); }
    </style>
</head>
<body>
    <div class="auth-wrap">
    <div class="auth-form">
        <img src="../assets/img/logo1.png" alt="Logo" class="auth-logo">
        <h2>Admin Login</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email Admin" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <p style="text-align:center;margin-top:0.8rem;"><a href="../index.php">← Kembali ke Website</a></p>
    </div>
    </div>
</body>
</html>