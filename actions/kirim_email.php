<?php
// PERBAIKAN 1: Mundur satu langkah (../)
require '../config/koneksi.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

if (!isset($_GET['id'])) {
    // PERBAIKAN 2: Redirect mundur ke views
    header("Location: ../views/data_pegawai.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) { die("Data tidak ditemukan"); }

// Hitung Gaji
$tunjangan = $p['tunjangan'] ?? 0;
$potongan = $p['potongan'] ?? 0;
$total = ($p['gaji_pokok'] + $tunjangan) - $potongan;

// 1. GENERATE PDF (MPDF)
$mpdf = new Mpdf();
$html = '
    <h2 style="text-align:center;">SLIP GAJI KARYAWAN</h2>
    <hr>
    <table cellpadding="5">
        <tr><td>Nama</td><td>: ' . $p['nama'] . '</td></tr>
        <tr><td>NIP</td><td>: ' . $p['nip'] . '</td></tr>
    </table>
    <br>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>Keterangan</th>
            <th style="text-align:right;">Jumlah</th>
        </tr>
        <tr>
            <td>Gaji Pokok</td>
            <td style="text-align:right;">Rp ' . number_format($p['gaji_pokok'],0,',','.') . '</td>
        </tr>
        <tr>
            <td>Tunjangan</td>
            <td style="text-align:right;">Rp ' . number_format($tunjangan,0,',','.') . '</td>
        </tr>
        <tr>
            <td>Potongan</td>
            <td style="text-align:right; color:red;">- Rp ' . number_format($potongan,0,',','.') . '</td>
        </tr>
        <tr>
            <th>TOTAL TERIMA</th>
            <th style="text-align:right;">Rp ' . number_format($total,0,',','.') . '</th>
        </tr>
    </table>
';
$mpdf->WriteHTML($html);
$pdfContent = $mpdf->Output('', 'S'); // Output ke string (S) untuk dilampirkan

// 2. KIRIM EMAIL (PHPMailer)
$mail = new PHPMailer(true);

try {
    // Setting Server Gmail (Sesuaikan email & app password kamu di sini)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kevin19305.ib@gmail.com'; // Ganti Email Kamu
    $mail->Password   = 'sxkl vipy bfsx ljfe'; // Ganti App Password Kamu
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Penerima
    $mail->setFrom('no-reply@payroll.com', 'HRD');
    $mail->addAddress($p['email'], $p['nama']);

    // Lampiran PDF
    $mail->addStringAttachment($pdfContent, 'Slip_Gaji_'.$p['nip'].'.pdf');

    // Konten Email
    $mail->isHTML(true);
    $mail->Subject = 'Slip Gaji Bulan Ini';
    $mail->Body    = 'Halo ' . $p['nama'] . ',<br><br>Berikut terlampir slip gaji Anda bulan ini.<br>Terima kasih.';

    $mail->send();
    echo "<script>alert('Email Berhasil Terkirim!'); window.location='../views/data_pegawai.php';</script>";

} catch (Exception $e) {
    echo "Gagal kirim email. Error: {$mail->ErrorInfo}";
}
?>