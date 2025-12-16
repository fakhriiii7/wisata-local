<?php
require_once '../config/config.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = (new Database())->getConnection();

// Handle actions
if(isset($_GET['action'])) {
    if($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $db->prepare("DELETE FROM destinasi WHERE id = ?")->execute([$_GET['id']]);
    }
}

// Add/edit destinasi
if($_POST) {
    $id = $_POST['id'] ?? '';
    $nama = sanitize($_POST['nama_destinasi']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $harga_dewasa = floatval($_POST['harga_dewasa']);
    $harga_anak = floatval($_POST['harga_anak']);
    $kuota = intval($_POST['kuota_harian']);
    $uploadedFilename = null;

    // Handle file upload if provided
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        if($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg','image/png','image/gif'];
            $tmpPath = $_FILES['foto']['tmp_name'];
            $type = mime_content_type($tmpPath);
            $size = $_FILES['foto']['size'];
            if(!in_array($type, $allowed)) {
                // ignore invalid types for now (could set flash message)
                $uploadedFilename = null;
            } elseif($size > 2 * 1024 * 1024) {
                $uploadedFilename = null;
            } else {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $newname = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = __DIR__ . '/../assets/img/destinasi/' . $newname;
                if(move_uploaded_file($tmpPath, $dest)) {
                    $uploadedFilename = $newname;
                }
            }
        }
    }
    
    if($id) {
        // Update (include foto only if uploaded)
        if($uploadedFilename) {
            $stmt = $db->prepare("UPDATE destinasi SET nama_destinasi=?, deskripsi=?, harga_dewasa=?, harga_anak=?, kuota_harian=?, gambar=? WHERE id=?");
            $stmt->execute([$nama, $deskripsi, $harga_dewasa, $harga_anak, $kuota, $uploadedFilename, $id]);
        } else {
            $stmt = $db->prepare("UPDATE destinasi SET nama_destinasi=?, deskripsi=?, harga_dewasa=?, harga_anak=?, kuota_harian=? WHERE id=?");
            $stmt->execute([$nama, $deskripsi, $harga_dewasa, $harga_anak, $kuota, $id]);
        }
    } else {
        // Insert (may include foto)
        if($uploadedFilename) {
            $stmt = $db->prepare("INSERT INTO destinasi (nama_destinasi, deskripsi, harga_dewasa, harga_anak, kuota_harian, gambar) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $deskripsi, $harga_dewasa, $harga_anak, $kuota, $uploadedFilename]);
        } else {
            $stmt = $db->prepare("INSERT INTO destinasi (nama_destinasi, deskripsi, harga_dewasa, harga_anak, kuota_harian) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $deskripsi, $harga_dewasa, $harga_anak, $kuota]);
        }
    }
    
    redirect('destinasi.php');
}

// Get all destinasi
$destinasi = $db->query("SELECT * FROM destinasi ORDER BY nama_destinasi")->fetchAll(PDO::FETCH_ASSOC);

// Get destinasi for edit
$edit_destinasi = null;
if(isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM destinasi WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_destinasi = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="../assets/img/logo1-1.png">
    <title>Kelola Destinasi</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-container h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3498db;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            margin-top: 1rem;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-secondary {
            background: #95a5a6;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        
        .price-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-prefix {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-weight: bold;
        }
        
        .input-group .form-control {
            padding-left: 2.5rem;
        }
        
        .kuota-input {
            max-width: 200px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <a href="../index.php"><img src="../assets/img/logo2-1.png" alt="WisataLocal" style="height:48px; display:block;"></a>
                </div>
                <div class="nav-links">
                    <a href="index.php">Dashboard</a>
                    <a href="destinasi.php">Destinasi</a>
                    <a href="pemesanan.php">Pemesanan</a>
                    <a href="users.php">Users</a>
                    <a href="laporan.php">Laporan</a>
                    <a href="../index.php">Website</a>
                    <a href="login.php?logout=1">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <h1>Kelola Destinasi Wisata</h1>
        
        <!-- Add/Edit Form -->
        <div class="form-container">
            <h2><?php echo $edit_destinasi ? 'Edit Destinasi' : 'Tambah Destinasi Baru'; ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <?php if($edit_destinasi): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_destinasi['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label" for="nama_destinasi">Nama Destinasi *</label>
                        <input type="text" class="form-control" id="nama_destinasi" name="nama_destinasi" 
                               placeholder="Masukkan nama destinasi wisata"
                               value="<?php echo $edit_destinasi['nama_destinasi'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label class="form-label" for="deskripsi">Deskripsi *</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                  placeholder="Deskripsikan destinasi wisata ini..."
                                  required><?php echo $edit_destinasi['deskripsi'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label class="form-label">Harga Tiket</label>
                        <div class="price-inputs">
                            <div class="input-group">
                                <span class="input-prefix">Rp</span>
                                <input type="number" class="form-control" name="harga_dewasa" 
                                       placeholder="Harga dewasa"
                                       value="<?php echo $edit_destinasi['harga_dewasa'] ?? ''; ?>" required min="0">
                            </div>
                            
                            <div class="input-group">
                                <span class="input-prefix">Rp</span>
                                <input type="number" class="form-control" name="harga_anak" 
                                       placeholder="Harga anak-anak"
                                       value="<?php echo $edit_destinasi['harga_anak'] ?? ''; ?>" required min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="kuota_harian">Kuota Harian *</label>
                        <input type="number" class="form-control kuota-input" id="kuota_harian" name="kuota_harian" 
                               placeholder="Jumlah pengunjung per hari"
                               value="<?php echo $edit_destinasi['kuota_harian'] ?? ''; ?>" required min="1">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="foto">Foto Destinasi</label>
                        <?php if(!empty($edit_destinasi['gambar'])): ?>
                            <div style="margin-bottom:0.5rem;">
                                <img src="../assets/img/destinasi/<?php echo $edit_destinasi['gambar']; ?>" alt="foto" style="max-width:200px; height:auto; border-radius:6px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <small style="color:#666;">Biarkan kosong jika tidak ingin mengubah foto. Maks 2MB.</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <?php echo $edit_destinasi ? '💾 Update Destinasi' : '➕ Tambah Destinasi'; ?>
                    </button>
                    <?php if($edit_destinasi): ?>
                        <a href="destinasi.php" class="btn-secondary">❌ Batal Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Destinasi List -->
        <h2>Daftar Destinasi Wisata</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Destinasi</th>
                        <th>Harga Dewasa</th>
                        <th>Harga Anak</th>
                        <th>Kuota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($destinasi as $d): ?>
                    <tr>
                        <td>
                            <strong><?php echo $d['nama_destinasi']; ?></strong>
                            <p style="margin: 0.25rem 0 0 0; color: #666; font-size: 0.9rem;">
                                <?php echo substr($d['deskripsi'], 0, 100); ?>...
                            </p>
                        </td>
                        <td><?php echo formatRupiah($d['harga_dewasa']); ?></td>
                        <td><?php echo formatRupiah($d['harga_anak']); ?></td>
                        <td><?php echo $d['kuota_harian']; ?> orang/hari</td>
                        <td>
                            <a href="destinasi.php?edit=<?php echo $d['id']; ?>" class="btn-primary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">
                                ✏️ Edit
                            </a>
                            <a href="destinasi.php?action=delete&id=<?php echo $d['id']; ?>" 
                               class="btn-secondary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;"
                               onclick="return confirm('Yakin ingin menghapus destinasi <?php echo $d['nama_destinasi']; ?>?')">
                                🗑️ Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>