<?php
require '../config/koneksi.php';
require '../vendor/autoload.php';

use Mpdf\Mpdf;

if (!isset($_GET['id'])) die("ID tidak ditemukan");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id = ?");
$stmt->execute([$id]);
$pegawai = $stmt->fetch();

// --- 1. HITUNG GAJI BERSIH (LOGIKA UTAMA) ---
$gaji_pokok = $pegawai['gaji_pokok'];
$tunjangan  = $pegawai['tunjangan'];
$potongan   = $pegawai['potongan'];
$total_bersih = ($gaji_pokok + $tunjangan) - $potongan;

// Format Rupiah
$rp_gaji      = number_format($gaji_pokok, 0, ',', '.');
$rp_tunjangan = number_format($tunjangan, 0, ',', '.');
$rp_potongan  = number_format($potongan, 0, ',', '.');
$rp_total     = number_format($total_bersih, 0, ',', '.');

// --- 2. DESAIN PDF ---
$html = "
<div style='font-family: Arial, sans-serif; padding: 40px; border: 2px solid #333;'>
    <div style='text-align:center; border-bottom: 2px solid #000; padding-bottom: 10px;'>
        <h2 style='margin:0'>PT. MAJU MUNDUR SEJAHTERA</h2>
        <p style='margin:5px 0'>Jl. Sudirman No. 1, Jakarta Pusat</p>
    </div>
    
    <h3 style='text-align:center; margin-top:20px; text-decoration: underline;'>SLIP GAJI KARYAWAN</h3>
    
    <table width='100%' style='margin-bottom: 20px;'>
        <tr>
            <td width='20%'><b>NIP</b></td><td width='30%'>: {$pegawai['nip']}</td>
            <td width='20%'><b>Periode</b></td><td width='30%'>: " . date('F Y') . "</td>
        </tr>
        <tr>
            <td><b>Nama</b></td><td>: {$pegawai['nama']}</td>
            <td><b>Jabatan</b></td><td>: Staff</td>
        </tr>
    </table>

    <table width='100%' border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
        <tr style='background-color: #eee;'>
            <th align='left'>DESKRIPSI</th>
            <th align='right'>JUMLAH (Rp)</th>
        </tr>
        <tr>
            <td>Gaji Pokok</td>
            <td align='right'>$rp_gaji</td>
        </tr>
        <tr>
            <td>Tunjangan</td>
            <td align='right'>$rp_tunjangan</td>
        </tr>
        
        <tr>
            <td style='color:red'>Potongan (Telat/Kasbon)</td>
            <td align='right' style='color:red'>- $rp_potongan</td>
        </tr>
        
        <tr style='font-weight:bold; background-color: #f9f9f9;'>
            <td>TOTAL GAJI BERSIH</td>
            <td align='right'>Rp $rp_total</td>
        </tr>
    </table>
    
    <br><br>
    <table width='100%'>
        <tr>
            <td width='70%' valign='top'>
                <i>Catatan:<br>Slip gaji ini sah dan dicetak otomatis oleh sistem.</i>
            </td>
            <td align='center'>
                Jakarta, " . date('d F Y') . "<br>
                Manager Keuangan<br><br><br><br>
                ( ________________ )
            </td>
        </tr>
    </table>
</div>
";

// --- 3. RENDER PDF ---
try {
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('Slip_Gaji.pdf', 'I'); 
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}
?>