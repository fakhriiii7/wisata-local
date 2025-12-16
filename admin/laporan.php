<?php
require_once '../config/config.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$db = (new Database())->getConnection();

// Get filter parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get report data
$stmt = $db->prepare("
    SELECT p.*, d.nama_destinasi 
    FROM pemesanan p 
    LEFT JOIN destinasi d ON p.destinasi_id = d.id 
    WHERE p.status = 'confirmed' AND DATE(p.created_at) BETWEEN ? AND ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$start_date, $end_date]);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_pendapatan = 0;
$total_pengunjung = 0;
foreach($laporan as $row) {
    $total_pendapatan += $row['total_harga'];
    $total_pengunjung += ($row['jumlah_dewasa'] + $row['jumlah_anak']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .filter-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .filter-container h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3498db;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 1rem;
            align-items: end;
        }
        
        .form-group {
            margin-bottom: 0;
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
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
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
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }
        
        .summary-card h3 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .summary-card p {
            color: #7f8c8d;
            font-weight: 600;
        }
        
        .report-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .total-row {
            background: #f8f9fa;
            font-weight: bold;
            border-top: 2px solid #3498db;
        }
        
        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="nav">
                <div class="logo">
                    <h2>WisataLocal - Admin</h2>
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
        <h1>Laporan Pemesanan</h1>
        
        <!-- Filter Section -->
        <div class="filter-container">
            <h2>Filter Laporan</h2>
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label class="form-label" for="start_date">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo $start_date; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="end_date">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo $end_date; ?>" required>
                </div>
                
                <button type="submit" class="btn-primary">🔍 Terapkan Filter</button>
                <a href="laporan.php" class="btn-secondary">🔄 Reset</a>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3><?php echo count($laporan); ?></h3>
                <p>Total Pesanan</p>
            </div>
            
            <div class="summary-card">
                <h3><?php echo $total_pengunjung; ?></h3>
                <p>Total Pengunjung</p>
            </div>
            
            <div class="summary-card">
                <h3><?php echo formatRupiah($total_pendapatan); ?></h3>
                <p>Total Pendapatan</p>
            </div>
            
            <div class="summary-card">
                <h3><?php echo date('d M Y', strtotime($start_date)); ?> - <?php echo date('d M Y', strtotime($end_date)); ?></h3>
                <p>Periode Laporan</p>
            </div>
        </div>

        <!-- Report Table -->
        <div class="report-section">
            <div class="section-header">
                <h2>Detail Laporan Pemesanan</h2>
                <div style="color: #7f8c8d;">
                    Menampilkan <?php echo count($laporan); ?> pesanan
                </div>
            </div>
            
            <?php if($laporan): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Pesan</th>
                                <th>Kode Booking</th>
                                <th>Nama Pemesan</th>
                                <th>Destinasi</th>
                                <th>Tanggal Kunjung</th>
                                <th>Pengunjung</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($laporan as $row): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <strong><?php echo $row['kode_booking']; ?></strong>
                                </td>
                                <td><?php echo $row['nama_pemesan']; ?></td>
                                <td><?php echo $row['nama_destinasi']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_berkunjung'])); ?></td>
                                <td>
                                    <?php echo $row['jumlah_dewasa'] + $row['jumlah_anak']; ?> orang
                                    <small style="display: block; color: #666; font-size: 0.8rem;">
                                        (<?php echo $row['jumlah_dewasa']; ?> dewasa, <?php echo $row['jumlah_anak']; ?> anak)
                                    </small>
                                </td>
                                <td>
                                    <strong><?php echo formatRupiah($row['total_harga']); ?></strong>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <!-- Total Row -->
                            <tr class="total-row">
                                <td colspan="5" style="text-align: right;">
                                    <strong>TOTAL:</strong>
                                </td>
                                <td>
                                    <strong><?php echo $total_pengunjung; ?> orang</strong>
                                </td>
                                <td>
                                    <strong><?php echo formatRupiah($total_pendapatan); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #7f8c8d;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📊</div>
                    <h3>Tidak ada data laporan</h3>
                    <p>Belum ada pemesanan yang dikonfirmasi pada periode yang dipilih</p>
                    <p>Silakan pilih periode tanggal yang berbeda</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Information Box -->
        <div style="background: #e8f4fd; padding: 1rem; border-radius: 8px; margin-top: 2rem; border-left: 4px solid #3498db;">
            <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">💡 Informasi Laporan</h4>
            <p style="color: #2c3e50; margin: 0; font-size: 0.9rem;">
                Laporan ini hanya menampilkan pemesanan dengan status <strong>"confirmed"</strong>. 
                Data diperbarui secara real-time berdasarkan filter tanggal yang dipilih.
            </p>
        </div>
    </main>
</body>
</html>