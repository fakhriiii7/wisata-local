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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <h2>WisataLocal - User</h2>
                </div>
                <div class="nav-links">
                    <a href="index.php">Dashboard</a>
                    <a href="riwayat.php">Riwayat</a>
                    <a href="../pemesanan.php">Pesan Baru</a>
                    <a href="../index.php">Kembali ke Website</a>
                    <a href="login.php?action=logout">Logout (<?php echo $_SESSION['user_nama']; ?>)</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">