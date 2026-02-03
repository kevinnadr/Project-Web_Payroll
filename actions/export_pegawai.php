<?php
// MUNDUR KE CONFIG & VENDOR
require '../config/koneksi.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Security Check
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/index.php"); exit; }

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Excel
$sheet->setCellValue('A1', 'NIP')
      ->setCellValue('B1', 'NAMA')
      ->setCellValue('C1', 'EMAIL')
      ->setCellValue('D1', 'GAJI POKOK')
      ->setCellValue('E1', 'TUNJANGAN')
      ->setCellValue('F1', 'POTONGAN')
      ->setCellValue('G1', 'TOTAL TERIMA');

// Styling Header (Opsional: Biar Bold)
$sheet->getStyle('A1:G1')->getFont()->setBold(true);

// Ambil Data
$sql = $pdo->query("SELECT * FROM pegawai ORDER BY nama ASC");
$rowNum = 2;

while($row = $sql->fetch()){
    $tunjangan = $row['tunjangan'] ?? 0;
    $potongan  = $row['potongan'] ?? 0;
    $total     = ($row['gaji_pokok'] + $tunjangan) - $potongan;
    
    $sheet->setCellValue('A'.$rowNum, $row['nip']);
    $sheet->setCellValue('B'.$rowNum, $row['nama']);
    $sheet->setCellValue('C'.$rowNum, $row['email']);
    $sheet->setCellValue('D'.$rowNum, $row['gaji_pokok']);
    $sheet->setCellValue('E'.$rowNum, $tunjangan);
    $sheet->setCellValue('F'.$rowNum, $potongan);
    $sheet->setCellValue('G'.$rowNum, $total);
    $rowNum++;
}

// Download File
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Gaji_'.date('Y-m-d_H-i').'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>