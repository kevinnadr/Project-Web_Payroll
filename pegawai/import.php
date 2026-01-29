<?php
session_start();

// PENTING: Karena file ini ada di dalam folder 'pegawai',
// kita harus mundur satu langkah (../) untuk mencari folder config & vendor
require '../config/koneksi.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

// 1. Cek Login (Keamanan)
if (!isset($_SESSION['user_id'])) { header("Location: ../index.php"); exit; }

$pesan_sukses = "";
$pesan_error = "";

// 2. LOGIC IMPORT SAAT TOMBOL DITEKAN
if (isset($_POST['import'])) {
    
    // Ambil ekstensi file
    $file_name = $_FILES['file_excel']['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $allowed_ext = ['xls', 'xlsx', 'csv'];

    if (in_array($file_ext, $allowed_ext)) {
        try {
            // Tentukan Pembaca File (Reader)
            if ('csv' == $file_ext) {
                $reader = new Csv();
            } else {
                $reader = new Xlsx();
            }

            // Baca file sementara yang diupload
            $spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $jumlah_sukses = 0;
            $jumlah_skip = 0;

            // Loop Data (Mulai dari baris ke-1, karena baris 0 adalah Judul/Header)
            for ($i = 1; $i < count($sheetData); $i++) {
                // Petakan Kolom Excel ke Variabel
                $nip   = $sheetData[$i][0]; // Kolom A
                $nama  = $sheetData[$i][1]; // Kolom B
                $email = $sheetData[$i][2]; // Kolom C
                $gaji  = $sheetData[$i][3]; // Kolom D (Pastikan angka saja)

                // Bersihkan format uang jika ada (misal user tulis "5.000.000" jadi "5000000")
                $gaji = str_replace(['.', ',', 'Rp', ' '], '', $gaji);

                // Validasi: NIP tidak boleh kosong
                if (!empty($nip)) {
                    // Cek apakah NIP sudah ada di database?
                    $cek = $pdo->prepare("SELECT id FROM pegawai WHERE nip = ?");
                    $cek->execute([$nip]);

                    if ($cek->rowCount() == 0) {
                        // Jika belum ada, Insert Baru
                        $sql = "INSERT INTO pegawai (nip, nama, email, gaji_pokok) VALUES (?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$nip, $nama, $email, $gaji]);
                        $jumlah_sukses++;
                    } else {
                        // Jika sudah ada, lewati (Skip)
                        $jumlah_skip++;
                    }
                }
            }

            $pesan_sukses = "Sukses! $jumlah_sukses data berhasil masuk. ($jumlah_skip data dilewati karena NIP duplikat)";

        } catch (Exception $e) {
            $pesan_error = "Terjadi error saat membaca file: " . $e->getMessage();
        }
    } else {
        $pesan_error = "Format file salah! Harap upload file .xlsx atau .csv";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Import Pegawai</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-green { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-red { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info-box { background: #fff3cd; padding: 15px; border-left: 5px solid #ffc107; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Import Data Pegawai</h2>
            <a href="../dashboard.php" class="btn btn-red">Kembali ke Dashboard</a>
        </div>

        <?php if($pesan_sukses) echo "<div class='alert alert-green'>$pesan_sukses</div>"; ?>
        <?php if($pesan_error) echo "<div class='alert alert-red'>$pesan_error</div>"; ?>

        <div class="info-box">
            <b>PENTING - Format Excel:</b><br>
            <ol>
                <li>Baris pertama harus <b>HEADER/JUDUL</b> (Tidak akan dimasukkan ke database).</li>
                <li>Pastikan urutan kolom sesuai contoh di bawah.</li>
                <li>Format file wajib <b>.xlsx</b> (Excel) atau <b>.csv</b>.</li>
                <li>Jika NIP sudah ada di database, data tersebut akan dilewati (tidak diduplikat).</li>
            </ol>
        </div>

        <form method="POST" enctype="multipart/form-data" style="border: 2px dashed #ccc; padding: 30px; text-align: center;">
            <p>Pilih file Excel Anda:</p>
            <input type="file" name="file_excel" required accept=".xlsx, .xls, .csv">
            <br><br>
            <button type="submit" name="import" class="btn btn-green" style="padding: 10px 30px; font-size: 16px;">
                📂 MULAI UPLOAD & IMPORT
            </button>
        </form>
        
        <br><br>
        <hr>
        
        <h3>Contoh Susunan Tabel Excel:</h3>
        <table border="1">
            <tr style="background: #eee;">
                <th>Kolom A (NIP)</th>
                <th>Kolom B (Nama)</th>
                <th>Kolom C (Email)</th>
                <th>Kolom D (Gaji)</th>
            </tr>
            <tr>
                <td>PEG001</td>
                <td>Budi Santoso</td>
                <td>budi@gmail.com</td>
                <td>5000000</td>
            </tr>
            <tr>
                <td>PEG002</td>
                <td>Siti Aminah</td>
                <td>siti@yahoo.com</td>
                <td>4500000</td>
            </tr>
        </table>
    </div>
</body>
</html>