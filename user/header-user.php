<?php
// user/header-user.php
if(!isLoggedIn() || isAdmin()) {
    redirect('../index.php');
}

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    redirect('../index.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - WisataLocal</title>
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
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
                    <a href="index.php" class="nav-btn">Dashboard</a>
                    <a href="riwayat.php" class="nav-btn">Riwayat</a>
                    <a href="../pemesanan.php" class="nav-btn">Pesan Baru</a>
                    <a href="../index.php" class="nav-btn">Kembali ke Website</a>
                    <a href="login.php?action=logout" class="nav-btn">Logout (<?php echo $_SESSION['user_nama']; ?>)</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">