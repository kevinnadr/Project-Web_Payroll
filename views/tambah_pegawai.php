<?php
session_start();
// PERBAIKAN 1: Mundur satu langkah (../) ke config
require '../config/koneksi.php';

// Cek Login (Mundur ke auth)
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/index.php"); exit; }

$page = 'pegawai';

// --- LOGIC SIMPAN DATA ---
if (isset($_POST['simpan'])) {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $gaji = $_POST['gaji'];
    $tunjangan = $_POST['tunjangan'];
    $potongan = $_POST['potongan'];

    try {
        $stmt = $pdo->prepare("INSERT INTO pegawai (nip, nama, email, gaji_pokok, tunjangan, potongan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nip, $nama, $email, $gaji, $tunjangan, $potongan]);
        
        // Redirect aman karena sesama folder views
        header("Location: data_pegawai.php");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal! NIP mungkin sudah terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pegawai - Payroll</title>
    
    <link rel="stylesheet" href="../assets/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Form Grid */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; font-size: 14px; }
        .input-group input { 
            width: 100%; padding: 12px 15px; border: 1px solid #ddd; 
            border-radius: 8px; background-color: #f9f9f9; font-size: 14px; transition: all 0.3s;
            box-sizing: border-box;
        }
        .input-group input:focus { border-color: #4361ee; background-color: #fff; outline: none; }
        .section-title { 
            font-size: 16px; font-weight: 700; color: #4361ee; margin-bottom: 20px; 
            padding-bottom: 10px; border-bottom: 2px solid #eee; 
            display: flex; align-items: center; gap: 10px; 
        }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; gap: 0; } }
    </style>
</head>
<body>

    <div class="wrapper">
        <?php include '../components/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h2><i class="fa-solid fa-user-plus"></i> Tambah Pegawai Baru</h2>
            </div>

            <div class="container">
                
                <?php if(isset($error)): ?>
                    <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-grid">
                        
                        <div>
                            <div class="section-title">
                                <i class="fa-regular fa-id-card"></i> Data Diri
                            </div>
                            
                            <div class="input-group">
                                <label>Nomor Induk Pegawai (NIP)</label>
                                <input type="text" name="nip" placeholder="Contoh: PEG001" required>
                            </div>
                            
                            <div class="input-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" placeholder="Nama Karyawan" required>
                            </div>
                            
                            <div class="input-group">
                                <label>Email Aktif</label>
                                <input type="email" name="email" placeholder="email@kantor.com" required>
                            </div>
                        </div>

                        <div>
                            <div class="section-title" style="color: #06d6a0;">
                                <i class="fa-solid fa-sack-dollar"></i> Informasi Gaji
                            </div>

                            <div class="input-group">
                                <label>Gaji Pokok (Rp)</label>
                                <input type="number" name="gaji" placeholder="0" required style="font-weight:bold;">
                            </div>

                            <div class="input-group">
                                <label>Tunjangan (Rp) - Opsional</label>
                                <input type="number" name="tunjangan" placeholder="0" value="0">
                            </div>

                            <div class="input-group">
                                <label>Potongan (Rp) - Opsional</label>
                                <input type="number" name="potongan" placeholder="0" value="0">
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                    <div style="display: flex; justify-content: flex-end; gap: 15px;">
                        <a href="data_pegawai.php" class="btn btn-red" style="background-color: #64748b;">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                        <button type="submit" name="simpan" class="btn btn-green" style="padding: 12px 40px; font-size: 16px;">
                            <i class="fa-solid fa-save"></i> SIMPAN DATA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>