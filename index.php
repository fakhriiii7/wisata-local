<?php
require_once 'config/config.php';
$db = (new Database())->getConnection();

$stmt = $db->query("SELECT * FROM destinasi ORDER BY created_at DESC LIMIT 4");
$destinasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/img/logo1-1.png">
    <title>Wisata Lokal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dest-card { background: white; border-radius: 10px; overflow: hidden; transition: transform 0.25s ease, box-shadow 0.25s ease; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
        .dest-card img { width:100%; height:140px; object-fit:cover; display:block; transition: transform 0.25s ease; }
        .dest-card .dest-name { padding:0.75rem; text-align:center; font-weight:700; color:#222; }
        .dest-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: 0 14px 30px rgba(0,0,0,0.12); }
        .dest-card:hover img { transform: scale(1.06); }
        .dest-link { text-decoration:none; color:inherit; }
        /* Action button styles */
        .action-btn { display:inline-flex; align-items:center; gap:0.5rem; cursor:pointer; transition:transform .14s ease, box-shadow .14s ease; }
        .btn-primary.action-btn { background:#2b8ef6;color:#fff;padding:0.7rem 1rem;border-radius:8px;text-decoration:none;box-shadow:0 8px 20px rgba(43,142,246,0.12); }
        .btn-secondary.action-btn { background:#f0f0f0;color:#222;padding:0.7rem 1rem;border-radius:8px;text-decoration:none;box-shadow:none; }
        .action-btn:hover { transform: translateY(-4px); box-shadow:0 12px 28px rgba(0,0,0,0.12); }
        .action-btn:active { transform: translateY(-1px) scale(.995); box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        /* Make destination links keyboard-focus visible */
        .dest-link:focus { outline: 3px solid rgba(43,142,246,0.18); outline-offset:6px; }
        .dest-link .dest-card:active { transform: translateY(-2px) scale(0.995); }
    </style>
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
                    <?php if(isLoggedIn()): ?>
                        <?php if(isAdmin()): ?>
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

    <main>
        <section class="hero" style="padding:5rem 1rem;">
            <div class="container" style="display:flex;flex-direction:row;gap:2rem;align-items:center;justify-content:space-between;flex-wrap:wrap;">
                <div style="flex:1;min-width:300px;display:flex;flex-direction:column;align-items:flex-start;gap:1.25rem;">
                    <img src="assets/img/logo2-1.png" alt="WisataLocal" style="max-width:320px;height:auto;filter:drop-shadow(0 6px 18px rgba(0,0,0,0.08));">
                    <p style="color:#444;max-width:680px;line-height:1.7;font-size:1.05rem;margin:0;">Platform sederhana untuk menemukan dan memesan tiket destinasi wisata lokal. Kami mempermudah promosi tempat menarik di daerah Anda, menyediakan informasi lengkap, harga, dan kemudahan pemesanan online.</p>
                            <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
                                <a href="destinasi.php" class="btn-primary action-btn">Lihat Destinasi</a>
                                <a href="cek_pemesanan.php" class="btn-secondary action-btn">Cek Pesanan</a>
                            </div>
                </div>

                <div style="flex:1;min-width:320px;">
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;">
                        <?php $count=0; foreach($destinasi as $d): if($count++==4) break; ?>
                        <a href="pemesanan.php?destinasi_id=<?php echo $d['id']; ?>" class="dest-link action-btn">
                        <div class="dest-card">
                            <?php if(!empty($d['gambar'])): ?>
                                <img src="assets/img/destinasi/<?php echo $d['gambar']; ?>" alt="<?php echo htmlspecialchars($d['nama_destinasi']); ?>">
                            <?php else: ?>
                                <div style="width:100%;height:140px;background:#eef6ff;display:flex;align-items:center;justify-content:center;color:#7a9bd6;">No Image</div>
                            <?php endif; ?>
                            <div class="dest-name"><?php echo htmlspecialchars($d['nama_destinasi']); ?></div>
                        </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> WisataLocal - Sistem Pemesanan Tiket Wisata</p>
        </div>
    </footer>
</body>
</html>