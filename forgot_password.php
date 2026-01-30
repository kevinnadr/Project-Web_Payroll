<?php
session_start();
require 'config/koneksi.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Jika sudah login, redirect ke dashboard
if(isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }

$msg = "";
$msg_type = "";

if(isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);
    
    // Cek email di database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user) {
        // Generate Token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token berlaku 1 jam
        
        // Simpan token ke database (Pakai waktu server MySQL agar sinkron)
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        if($update->execute([$token, $email])) {
            
            // Kirim Email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'kevin19305.ib@gmail.com'; 
                $mail->Password   = 'sxkl vipy bfsx ljfe';     
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('no-reply@payroll.com', 'Payroll System Termux/Web');
                $mail->addAddress($email);

                // Link Reset
                // Pastikan URL disesuaikan dengan environment kamu (localhost atau domain)
                $resetLink = "http://localhost/Project-Web_Payroll/reset_password.php?token=" . $token;

                $mail->isHTML(true);
                $mail->Subject = 'Reset Password - Payroll System';
                $mail->Body    = "
                    <h3>Permintaan Reset Password</h3>
                    <p>Klik link di bawah ini untuk mereset password akun Anda:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>Link ini akan kadaluarsa dalam 1 jam.</p>
                    <p>Jika Anda tidak meminta ini, abaikan saja email ini.</p>
                ";

                $mail->send();
                $msg = "Link reset password telah dikirim ke email Anda. Cek inbox/spam.";
                $msg_type = "success";
            } catch (Exception $e) {
                $msg = "Gagal mengirim email: " . $mail->ErrorInfo;
                $msg_type = "error";
            }
        } else {
            $msg = "Gagal mengupdate database.";
            $msg_type = "error";
        }
    } else {
        // Agar aman, jangan beritahu jika email tidak ditemukan, tapi untuk UX user friendly di internal apps boleh ditampilkan.
        // Di sini kita kasih tau aja biar jelas.
        $msg = "Email tidak ditemukan!";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password - Payroll</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background-color: #f4f6f9;">

<div class="login-box" style="margin-top: 50px;">
    <h2 style="text-align:center;">Lupa Password</h2>
    <p style="text-align:center; font-size:14px; color:#666;">Masukkan email Anda untuk menerima link reset password.</p>
    
    <?php if($msg): ?>
        <p style="text-align:center; color: <?php echo $msg_type == 'error' ? 'red' : 'green'; ?>;">
            <?php echo $msg; ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Email Terdaftar</label>
        <input type="email" name="email" required placeholder="Contoh: admin@test.com">
        
        <button type="submit" name="reset_request" class="btn btn-blue" style="width:100%">KIRIM LINK RESET</button>
    </form>
    
    <div style="text-align:center; margin-top: 15px;">
        <a href="index.php" style="text-decoration:none; color:#007bff;">Kembali ke Login</a>
    </div>
</div>

</body>
</html>
