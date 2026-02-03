<?php
session_start();
// PERBAIKAN PATH: Mundur satu langkah (../) ke config
require '../config/koneksi.php';

// Cek Login: Jika belum login, lempar ke folder auth
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../auth/index.php"); 
    exit; 
}

// Variabel Sidebar (Supaya menu 'Data Pegawai' menyala)
$page = 'pegawai';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai - Payroll</title>
    
    <link rel="stylesheet" href="../assets/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="wrapper">
        
        <?php include '../components/sidebar.php'; ?>

        <div class="main-content">
            
            <div class="header">
                <h2>Data Pegawai</h2>
            </div>

            <div class="container">
                
                <div class="action-bar">
                    <a href="tambah_pegawai.php" class="btn btn-blue" style="background: orange;">
                         <i class="fa-solid fa-plus"></i> Tambah
                    </a>

                    <a href="../actions/import_pegawai.php" class="btn btn-blue">
                        <i class="fa-solid fa-file-import"></i> Import
                    </a>

                    <a href="../actions/export_pegawai.php" class="btn btn-green">
                         <i class="fa-solid fa-file-excel"></i> Excel
                    </a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Email</th> <th class="angka">Gaji Bersih</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM pegawai ORDER BY id DESC");
                            
                            // Cek jika data kosong
                            if ($stmt->rowCount() == 0) {
                                echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Data masih kosong. Silakan Tambah atau Import data.</td></tr>";
                            }

                            while ($row = $stmt->fetch()) {
                                // Hitung Total Gaji Bersih
                                $total = ($row['gaji_pokok'] + $row['tunjangan']) - $row['potongan'];
                                
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['nip']) . "</td>
                                    
                                    <td style='font-weight:500; color:#333;'>" . htmlspecialchars($row['nama']) . "</td>
                                    
                                    <td style='color:#666;'>" . htmlspecialchars($row['email']) . "</td>
                                    
                                    <td class='angka'>Rp " . number_format($total, 0, ',', '.') . "</td>
                                    
                                    <td style='text-align:center;'>
                                        <a href='../actions/cetak_slip.php?id={$row['id']}' class='btn btn-green' target='_blank' title='Cetak Slip'>
                                            <i class='fa-solid fa-print'></i>
                                        </a>
                                        
                                        <a href='../actions/kirim_email.php?id={$row['id']}' class='btn btn-blue' title='Kirim Slip ke Email' onclick='return confirm(\"Kirim slip gaji ke email ini?\")'>
                                            <i class='fa-solid fa-paper-plane'></i>
                                        </a>
                                        
                                        <a href='edit_pegawai.php?id={$row['id']}' class='btn btn-blue' title='Edit Data'>
                                            <i class='fa-solid fa-pen'></i>
                                        </a>
                                        
                                        <a href='../actions/hapus_pegawai.php?id={$row['id']}' class='btn btn-red' onclick='return confirm(\"Yakin hapus data pegawai ini?\")' title='Hapus'>
                                            <i class='fa-solid fa-trash'></i>
                                        </a>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div> </div>
        </div> 
    </div>

</body>
</html>