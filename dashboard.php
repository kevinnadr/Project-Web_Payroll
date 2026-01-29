<?php
session_start();
require 'config/koneksi.php'; // Koneksi tetap sama karena file ini di luar
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

// --- LOGIC HITUNG STATISTIK (DASHBOARD) ---
// 1. Hitung Total Pegawai
$q1 = $pdo->query("SELECT COUNT(*) as total FROM pegawai");
$total_pegawai = $q1->fetch()['total'];

// 2. Hitung Total Pengeluaran Gaji
$q2 = $pdo->query("SELECT SUM(gaji_pokok) as total_gaji FROM pegawai");
$total_uang = $q2->fetch()['total_gaji'];

// --- LOGIC EXPORT EXCEL ---
if (isset($_POST['export_excel'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'NIP')->setCellValue('B1', 'NAMA')->setCellValue('C1', 'EMAIL')->setCellValue('D1', 'GAJI');
    
    $sql = $pdo->query("SELECT * FROM pegawai ORDER BY nama ASC");
    $rowNum = 2;
    while($row = $sql->fetch()){
        $sheet->setCellValue('A'.$rowNum, $row['nip']);
        $sheet->setCellValue('B'.$rowNum, $row['nama']);
        $sheet->setCellValue('C'.$rowNum, $row['email']);
        $sheet->setCellValue('D'.$rowNum, $row['gaji_pokok']);
        $rowNum++;
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Data_Pegawai.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #007bff; }
        .stat-number { font-size: 24px; font-weight: bold; color: #333; }
        .stat-label { color: #666; font-size: 14px; }
        .green-border { border-left-color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Dashboard Admin Payroll</h2>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Pegawai</div>
                <div class="stat-number"><?php echo $total_pegawai; ?> Orang</div>
            </div>
            <div class="stat-card green-border">
                <div class="stat-label">Estimasi Pengeluaran Gaji</div>
                <div class="stat-number">Rp <?php echo number_format($total_uang, 0, ',', '.'); ?></div>
            </div>
        </div>

        <h3>Manajemen Data Pegawai</h3>
        
        <div class="action-bar">
            <form method="POST" style="display:inline;">
                <button type="submit" name="export_excel" class="btn btn-green">⬇️ Download Excel</button>
            </form>
            
            <a href="pegawai/import.php" class="btn btn-blue">⬆️ Import Excel</a>

            <a href="pegawai/tambah.php" class="btn btn-blue" style="background-color: orange;">➕ Tambah Manual</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Gaji Pokok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM pegawai ORDER BY id DESC");
                while ($row = $stmt->fetch()) {
                    echo "<tr>
                        <td>{$row['nip']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['email']}</td>
                        <td>Rp " . number_format($row['gaji_pokok'],0,',','.') . "</td>
                        <td>
                            <a href='cetak_slip.php?id={$row['id']}' class='btn btn-green' target='_blank' title='Lihat PDF'>🖨️</a>
                            
                            <a href='kirim_email.php?id={$row['id']}' class='btn btn-blue' title='Kirim Email ke Pegawai' onclick='return confirm(\"Kirim Slip Gaji ke email ini?\")'>📧</a>
                            
                            <a href='pegawai/edit.php?id={$row['id']}' class='btn btn-blue' title='Edit'>✏️</a>
                            <a href='pegawai/hapus.php?id={$row['id']}' class='btn btn-red' title='Hapus' onclick='return confirm(\"Hapus data ini?\")'>🗑️</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>