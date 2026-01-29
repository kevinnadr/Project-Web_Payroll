<?php
session_start();
require 'config/koneksi.php';
require 'vendor/autoload.php';

use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cek Login & ID
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
if (!isset($_GET['id'])) { header("Location: dashboard.php"); exit; }

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id = ?");
$stmt->execute([$id]);
$pegawai = $stmt->fetch();

if (!$pegawai) die("Data tidak ditemukan.");

// --- 1. BUAT PDF (Di Memory) ---
$bulan = date('F Y');
$gaji_bersih = number_format($pegawai['gaji_pokok'], 0, ',', '.');

$html = "
<div style='font-family: Arial; padding: 30px; border: 1px solid #333;'>
    <h2 style='text-align:center'>SLIP GAJI KARYAWAN</h2>
    <p style='text-align:center'>Periode: $bulan</p>
    <hr>
    <table width='100%'>
        <tr><td>Nama</td><td>: <b>{$pegawai['nama']}</b></td></tr>
        <tr><td>NIP</td><td>: {$pegawai['nip']}</td></tr>
        <tr><td>Email Penerima</td><td>: {$pegawai['email']}</td></tr>
    </table>
    <br>
    <table border='1' cellspacing='0' cellpadding='10' width='100%'>
        <tr style='background:#eee'><th>Keterangan</th><th align='right'>Jumlah</th></tr>
        <tr><td>Gaji Pokok</td><td align='right'>Rp $gaji_bersih</td></tr>
        <tr style='font-weight:bold'><td>TOTAL DITERIMA</td><td align='right'>Rp $gaji_bersih</td></tr>
    </table>
</div>
";

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$pdfContent = $mpdf->Output('', 'S'); // Simpan PDF sebagai String (Text)

// --- 2. KIRIM EMAIL ---
$mail = new PHPMailer(true);

try {
    // --- SETTING PENTING DISINI ---
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    
    // GANTI DUA BARIS INI:
    $mail->Username   = 'kevin19305.ib@gmail.com';  // <--- GANTI EMAIL GMAIL KAMU (YANG BUAT APP PASSWORD TADI)
    $mail->Password   = 'sxkl vipy bfsx ljfe';   // <--- TEMPEL 16 HURUF DISINI (TANPA SPASI LEBIH AMAN)
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Pengirim & Penerima
    $mail->setFrom('hrd@payroll.com', 'HRD Payroll System'); // Boleh ganti nama pengirim
    $mail->addAddress($pegawai['email'], $pegawai['nama']);  // Email Pegawai (Otomatis dari Database)

    // Isi Email
    $mail->isHTML(true);
    $mail->Subject = "Slip Gaji - $bulan - {$pegawai['nama']}";
    $mail->Body    = "Halo <b>{$pegawai['nama']}</b>,<br><br>Berikut terlampir Slip Gaji Anda bulan ini.<br>Terima Kasih.";

    // Lampirkan PDF
    $mail->addStringAttachment($pdfContent, "Slip_Gaji_$bulan.pdf");

    $mail->send();
    
    // Pesan Sukses
    echo "<script>
            alert('SUKSES! Slip Gaji berhasil dikirim ke {$pegawai['email']}');
            window.location.href='dashboard.php';
          </script>";

} catch (Exception $e) {
    echo "<h1>GAGAL KIRIM EMAIL!</h1>";
    echo "Pesan Error: " . $mail->ErrorInfo;
}
?>