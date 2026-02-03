<?php
session_start();
// PERBAIKAN 1: Mundur satu langkah ke config
require '../config/koneksi.php';

// Cek Login (Mundur ke auth)
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/index.php"); exit; }

// CEK ROLE (Security)
if ($_SESSION['role'] != 'admin') { 
    echo "<script>alert('Akses Ditolak! Anda bukan Admin.'); window.location='dashboard.php';</script>";
    exit;
}

$page = 'user'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - Payroll</title>
    
    <link rel="stylesheet" href="../assets/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        
        <?php include '../components/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h2><i class="fa-solid fa-user-shield"></i> Manajemen Akun Admin/Staff</h2>
            </div>

            <div class="container">
                <div class="action-bar">
                    <a href="tambah_user.php" class="btn btn-blue">
                        <i class="fa-solid fa-plus"></i> Tambah User Baru
                    </a>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Email Login</th>
                                <th>Role / Jabatan</th>
                                <th>Status</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM users ORDER BY role ASC");
                            while ($row = $stmt->fetch()) {
                                $badgeColor = ($row['role'] == 'admin') ? '#4361ee' : '#6c757d';
                                $badge = "<span style='background:$badgeColor; color:white; padding:4px 8px; border-radius:4px; font-size:12px; text-transform:uppercase;'>{$row['role']}</span>";
                                
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>$badge</td>
                                    <td style='color:green; font-weight:bold;'>Aktif</td>
                                    <td style='text-align:center;'>";
                                    
                                    if ($row['id'] != $_SESSION['user_id']) {
                                        // PERBAIKAN 4: Link Hapus mengarah ke folder ACTIONS
                                        echo "<a href='../actions/hapus_user.php?id={$row['id']}' class='btn btn-red' onclick='return confirm(\"Yakin hapus user ini?\")'>
                                                <i class='fa-solid fa-trash'></i> Hapus
                                              </a>";
                                    } else {
                                        echo "<span style='color:#ccc; font-size:12px;'>Sedang Login</span>";
                                    }
                                    
                                echo "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>