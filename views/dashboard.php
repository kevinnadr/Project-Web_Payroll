<?php
session_start();
// Mundur satu langkah (../) untuk mencari koneksi
require '../config/koneksi.php';

// Cek Login: Jika belum login, lempar ke folder auth
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../auth/index.php"); 
    exit; 
}

// Variabel untuk Sidebar
$page = 'dashboard';

// --- LOGIC STATISTIK ---
// 1. Hitung Total Pegawai
$q1 = $pdo->query("SELECT COUNT(*) as total FROM pegawai");
$total_pegawai = $q1->fetch()['total'];

// 2. Hitung Estimasi Pengeluaran
$q2 = $pdo->query("SELECT SUM(gaji_pokok + IFNULL(tunjangan,0) - IFNULL(potongan,0)) as total_gaji FROM pegawai");
$total_uang = $q2->fetch()['total_gaji'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Payroll</title>
    
    <link rel="stylesheet" href="../assets/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="wrapper">
        
        <?php include '../components/sidebar.php'; ?>

        <div class="main-content">
            
            <div class="header">
                <div>
                    <h2>Overview Statistik</h2>
                    <span>Selamat Datang, Admin!</span>
                </div>
            </div>

            <div class="stats-grid">
                
                <div class="stat-card">
                    <div class="stat-label">Total Pegawai</div>
                    <div class="stat-number"><?php echo $total_pegawai; ?></div>
                    <div class="stat-desc">
                        <i class="fa-solid fa-user-check"></i> Karyawan Aktif
                    </div>
                </div>
                
                <div class="stat-card green-border">
                    <div class="stat-label">Estimasi Pengeluaran</div>
                    <div class="stat-number" style="color: #27ae60;">
                        Rp <?php echo number_format($total_uang, 0, ',', '.'); ?>
                    </div>
                    <div class="stat-desc">
                        <i class="fa-solid fa-coins"></i> Bulan Ini
                    </div>
                </div>

                <!-- <div class="stat-card orange-border">
                    <div class="stat-label">Status Sistem</div>
                    <div class="stat-number" style="color: var(--primary);">ONLINE</div>
                    <div class="stat-desc" style="color: var(--success);">
                        <i class="fa-solid fa-signal"></i> Server Normal
                    </div>
                </div> -->

            </div>
            
            <div class="container">
                <h3><i class="fa-solid fa-rocket" style="color: orange;"></i> Menu Cepat</h3>
                <p>Akses fitur pengelolaan data dengan cepat:</p>
                
                <div class="action-bar">
                    <a href="tambah_pegawai.php" class="btn btn-blue" style="background-color: orange;">
                        <i class="fa-solid fa-user-plus"></i> Tambah Manual
                    </a>

                    <a href="../actions/import_pegawai.php" class="btn btn-blue">
                        <i class="fa-solid fa-file-import"></i> Import Excel
                    </a>

                    <a href="data_pegawai.php" class="btn btn-green">
                        <i class="fa-solid fa-table"></i> Lihat Semua Data
                    </a>
                </div>
            </div>

        </div> 
    </div>

</body>
</html>