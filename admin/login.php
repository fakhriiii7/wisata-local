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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="auth-form">
        <h2>Admin Login</h2>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email Admin" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <p><a href="../index.php">← Kembali ke Website</a></p>
    </div>
</body>
</html>