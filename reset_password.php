<?php
session_start();
require 'config/koneksi.php';

$msg = "";
$msg_type = "";
$token = $_GET['token'] ?? '';

// Validasi Token Awal
if (empty($token)) {
    die("Token tidak valid!");
}

// Cek Token di Database (Debug Mode)
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("<h3>Error: Token tidak ditemukan!</h3><p>Pastikan Anda menyalin link secara lengkap.</p><a href='forgot_password.php'>Minta link baru</a>");
}

// Cek Expiry secara manual di PHP untuk debugging yang lebih jelas
// Kita ambil waktu server MySQL lewat query terpisah atau bandingkan di PHP jika timezone sinkron
// Tapi untuk aman, kita query lagi status validitasnya
$checkTime = $pdo->prepare("SELECT (reset_expiry > NOW()) as is_still_valid, reset_expiry, NOW() as server_time FROM users WHERE id = ?");
$checkTime->execute([$user['id']]);
$timeData = $checkTime->fetch();

if ($timeData['is_still_valid'] == 0) {
    die("<h3>Error: Token sudah kadaluarsa!</h3>
         <p>Token Expired: {$timeData['reset_expiry']}</p>
         <p>Server Time: {$timeData['server_time']}</p>
         <a href='forgot_password.php'>Minta link baru</a>");
}

if(isset($_POST['reset_password'])) {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    
    if($pass1 !== $pass2) {
        $msg = "Password konfirmasi tidak sama!";
        $msg_type = "error";
    } else {
        // Hash Password Baru
        $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);
        
        // Update Password & Hapus Token
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        if($update->execute([$hashed_password, $user['id']])) {
            echo "<script>
                    alert('Password berhasil direset! Silakan login.');
                    window.location.href='index.php';
                  </script>";
            exit;
        } else {
            $msg = "Gagal mereset password.";
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Payroll</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background-color: #f4f6f9;">

<div class="login-box" style="margin-top: 50px;">
    <h2 style="text-align:center;">Reset Password</h2>
    <p style="text-align:center; font-size:14px; color:#666;">Masukkan password baru Anda.</p>
    
    <?php if($msg): ?>
        <p style="text-align:center; color:red;"><?php echo $msg; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Password Baru</label>
        <input type="password" name="pass1" required minlength="4">
        
        <label>Konfirmasi Password Baru</label>
        <input type="password" name="pass2" required minlength="4">
        
        <button type="submit" name="reset_password" class="btn btn-blue" style="width:100%">UBAH PASSWORD</button>
    </form>
</div>

</body>
</html>
