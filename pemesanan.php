<?php
require_once 'config/config.php';
$db = (new Database())->getConnection();

$destinasi_id = $_GET['destinasi_id'] ?? '';

// Get destinasi
$stmt = $db->prepare("SELECT * FROM destinasi WHERE id = ?");
$stmt->execute([$destinasi_id]);
$destinasi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$destinasi) {
    redirect('destinasi.php');
}

// Process form
if ($_POST) {
    $nama = sanitize($_POST['nama_pemesan']);
    $email = sanitize($_POST['email']);
    $telepon = sanitize($_POST['no_telepon']);
    $tanggal = sanitize($_POST['tanggal_berkunjung']);
    $dewasa = intval($_POST['jumlah_dewasa']);
    $anak = intval($_POST['jumlah_anak']);

    // total
    $total = ($dewasa * $destinasi['harga_dewasa']) + ($anak * $destinasi['harga_anak']);
    $kode_booking = generateKodeBooking();

    // Insert pemesanan
    $stmt = $db->prepare("INSERT INTO pemesanan (kode_booking, user_id, destinasi_id, nama_pemesan, email, no_telepon, tanggal_berkunjung, jumlah_dewasa, jumlah_anak, total_harga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $user_id = isLoggedIn() ? $_SESSION['user_id'] : NULL;

    if ($stmt->execute([$kode_booking, $user_id, $destinasi_id, $nama, $email, $telepon, $tanggal, $dewasa, $anak, $total])) {
        redirect("konfirmasi.php?kode=$kode_booking");
    }
}
?>

<?php include 'includes/header-public.php'; ?>

<h1>Pesan Tiket - <?php echo $destinasi['nama_destinasi']; ?></h1>

<form method="POST" class="booking-form">
    <div class="form-group">
        <label>Nama Pemesan</label>
        <input type="text" name="nama_pemesan" value="<?php echo isLoggedIn() ? $_SESSION['user_nama'] : ''; ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo isLoggedIn() ? $_SESSION['user_email'] : ''; ?>" required>
    </div>

    <div class="form-group">
        <label>No. Telepon</label>
        <input type="tel" name="no_telepon"
            value="<?php echo isLoggedIn() && isset($_SESSION['user_telepon']) ? $_SESSION['user_telepon'] : ''; ?>"
            required>
    </div>

    <div class="form-group">
        <label>Tanggal Berkunjung</label>
        <input type="date" name="tanggal_berkunjung" min="<?php echo date('Y-m-d'); ?>" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Jumlah Dewasa</label>
            <input type="number" name="jumlah_dewasa" value="1" min="1" required>
        </div>

        <div class="form-group">
            <label>Jumlah Anak</label>
            <input type="number" name="jumlah_anak" value="0" min="0">
        </div>
    </div>

    <div class="price-summary">
        <h3>Ringkasan Biaya</h3>
        <div>Dewasa: <span id="total-dewasa"><?php echo formatRupiah($destinasi['harga_dewasa']); ?></span></div>
        <div>Anak: <span id="total-anak">Rp 0</span></div>
        <div class="total">Total: <span id="total-harga"><?php echo formatRupiah($destinasi['harga_dewasa']); ?></span></div>
    </div>

    <button type="submit" class="btn">Pesan Tiket</button>
</form>

<script>
    const hargaDewasa = <?php echo $destinasi['harga_dewasa']; ?>;
    const hargaAnak = <?php echo $destinasi['harga_anak']; ?>;

    function calculateTotal() {
        const dewasa = parseInt(document.querySelector('[name="jumlah_dewasa"]').value) || 0;
        const anak = parseInt(document.querySelector('[name="jumlah_anak"]').value) || 0;

        const totalDewasa = dewasa * hargaDewasa;
        const totalAnak = anak * hargaAnak;
        const total = totalDewasa + totalAnak;

        document.getElementById('total-dewasa').textContent = 'Rp ' + totalDewasa.toLocaleString('id-ID');
        document.getElementById('total-anak').textContent = 'Rp ' + totalAnak.toLocaleString('id-ID');
        document.getElementById('total-harga').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.querySelector('[name="jumlah_dewasa"]').addEventListener('change', calculateTotal);
    document.querySelector('[name="jumlah_anak"]').addEventListener('change', calculateTotal);
</script>

<?php include 'includes/footer.php'; ?>