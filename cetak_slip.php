<?php
require 'config/koneksi.php';
use Mpdf\Mpdf;

if (!isset($_GET['id'])) die("ID tidak ditemukan");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id = ?");
$stmt->execute([$id]);
$pegawai = $stmt->fetch();

// --- HTML SLIP GAJI ---
$html = "
<div style='font-family: Arial, sans-serif; padding: 30px; border: 1px solid #333;'>
    <h1 style='text-align:center; color: #333;'>PT. CONTOH SEJAHTERA</h1>
    <h3 style='text-align:center;'>SLIP GAJI KARYAWAN</h3>
    <hr>
    <table width='100%' style='margin-bottom: 20px;'>
        <tr>
            <td width='20%'><b>Nama</b></td><td>: {$pegawai['nama']}</td>
            <td width='20%'><b>Bulan</b></td><td>: Februari 2026</td>
        </tr>
        <tr>
            <td><b>NIP</b></td><td>: {$pegawai['nip']}</td>
            <td><b>Jabatan</b></td><td>: Staff</td>
        </tr>
    </table>

    <table width='100%' border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
        <tr style='background-color: #f2f2f2;'>
            <th>KETERANGAN</th>
            <th style='text-align:right'>JUMLAH (Rp)</th>
        </tr>
        <tr>
            <td>Gaji Pokok</td>
            <td style='text-align:right'>" . number_format($pegawai['gaji_pokok'],0,',','.') . "</td>
        </tr>
        <tr>
            <td>Tunjangan Transport</td>
            <td style='text-align:right'>0</td>
        </tr>
        <tr style='font-weight:bold; background-color: #eee;'>
            <td>TOTAL DITERIMA</td>
            <td style='text-align:right'>" . number_format($pegawai['gaji_pokok'],0,',','.') . "</td>
        </tr>
    </table>
    
    <br><br><br>
    <table width='100%'>
        <tr>
            <td width='70%'></td>
            <td align='center'>
                Jakarta, " . date('d F Y') . "<br>
                Manager HRD<br><br><br><br>
                ( ....................... )
            </td>
        </tr>
    </table>
</div>
";

// --- RENDER PDF ---
try {
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Slip_Gaji.pdf', 'I'); // I = Preview di browser
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}
?>