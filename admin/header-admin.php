<?php
// admin/header-admin.php
// Assumes the including file has already required config and performed auth checks.
// Handle logout from admin header
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}
?>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <a href="../index.php"><img src="../assets/img/logo2-1.png" alt="WisataLocal" style="height:48px; display:block;"></a>
                </div>
                <div class="nav-links">
                    <a href="index.php" class="nav-btn">Dashboard</a>
                    <a href="destinasi.php" class="nav-btn">Destinasi</a>
                    <a href="pemesanan.php" class="nav-btn">Pemesanan</a>
                    <a href="users.php" class="nav-btn">Users</a>
                    <a href="laporan.php" class="nav-btn">Laporan</a>
                    <a href="../index.php" class="nav-btn">Website</a>
                    <a href="login.php?logout=1" class="nav-btn">Logout</a>
                </div>
            </div>
        </div>
    </header>
