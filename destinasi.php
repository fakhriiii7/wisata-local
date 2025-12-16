<?php
require_once 'config/config.php';
$db = (new Database())->getConnection();

// Get all destinasi
$stmt = $db->query("SELECT * FROM destinasi ORDER BY nama_destinasi");
$destinasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header-public.php'; ?>

<style>
    :root{--brown:#976a3c;--brown-600:#7f5231;}
    .dest-page { padding:2.5rem 1rem; }
    .dest-hero { text-align:center; margin-bottom:1.5rem; }
    .dest-hero h1 { color:var(--brown); margin:0; font-size:2rem; }
    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem; }
    .card { background:#fff; border-radius:10px; padding:0.75rem; box-shadow:0 8px 24px rgba(151,106,60,0.06); border:1px solid rgba(151,106,60,0.06); display:flex; flex-direction:column; gap:0.5rem; }
    .card-image img{ border-radius:8px; display:block; }
    .card h3{ margin:0; color:var(--brown-600); font-size:1.05rem; }
    .card p{ color:#6b5a50; font-size:0.95rem; margin:0.25rem 0 0.5rem 0; }
    .price-info{ gap:1rem; justify-content:space-between; color:#5a4538; font-weight:600; font-size:0.95rem; }
    .btn { display:inline-block; margin-top:0.5rem; padding:0.6rem 0.9rem; background:var(--brown); color:#fff; text-decoration:none; border-radius:8px; box-shadow:0 8px 20px rgba(151,106,60,0.12); transition:transform .18s ease, box-shadow .18s ease; }
    .btn:hover{ transform:translateY(-3px); box-shadow:0 16px 36px rgba(151,106,60,0.16); }
    @media(max-width:600px){ .price-info{flex-direction:column;align-items:flex-start;gap:0.25rem;} }
</style>

<section class="dest-page">
    <div class="dest-hero">
        <h1>Destinasi Wisata</h1>
        <p style="color:#6b5a50;">Jelajahi pilihan destinasi terbaik kami dan pesan tiket online dengan mudah.</p>
    </div>

<div class="grid">
    <?php foreach($destinasi as $d): ?>
    <div class="card">
        <?php if(!empty($d['gambar'])): ?>
        <div class="card-image">
            <img src="assets/img/destinasi/<?php echo $d['gambar']; ?>" alt="<?php echo htmlspecialchars($d['nama_destinasi']); ?>" style="width:100%; height:auto; max-height:200px; object-fit:cover; border-radius:6px;">
        </div>
        <?php endif; ?>
        <h3><?php echo $d['nama_destinasi']; ?></h3>
        <p><?php echo $d['deskripsi']; ?></p>
        <div class="price-info">
            <div>Dewasa: <?php echo formatRupiah($d['harga_dewasa']); ?></div>
            <div>Anak: <?php echo formatRupiah($d['harga_anak']); ?></div>
            <div>Kuota: <?php echo $d['kuota_harian']; ?> orang/hari</div>
        </div>
        <a href="pemesanan.php?destinasi_id=<?php echo $d['id']; ?>" class="btn">Pesan Tiket</a>
    </div>
    <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>