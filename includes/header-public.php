<?php
// includes/header-public.php

// Handle logout from header
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Lokal</title>
    <link rel="icon" type="image/png" href="assets/img/logo1-1.png">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <a href="index.php"><img src="assets/img/logo2-1.png" alt="WisataLocal" style="height:48px; display:block;"></a>
                </div>
                <nav class="nav-links">
                    <a href="index.php">Beranda</a>
                    <a href="destinasi.php">Destinasi Wisata</a>
                    <a href="cek_pemesanan.php">Cek Pesanan</a>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <a href="admin/index.php">Admin</a>
                            <a href="admin/login.php?logout=1">Logout</a>
                        <?php else: ?>
                            <a href="user/index.php">Dashboard</a>
                            <a href="user/login.php?action=logout">Logout</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="user/login.php">Login</a>
                        <a href="user/register.php">Daftar</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">