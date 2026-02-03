<?php
session_start();
// PERBAIKAN PATH: Tambahkan '../' di depan
require '../config/koneksi.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Jika sudah login, redirect ke dashboard (Mundur dulu ke views)
if(isset($_SESSION['user_id'])) { header("Location: ../views/dashboard.php"); exit; }

$msg = "";
$msg_type = "";

if(isset($_POST['reset_request'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user) {
        $token = bin2hex(random_bytes(32));
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        
        if($update->execute([$token, $email])) {
            $mail = new PHPMailer(true);
            try {
                // SETTING SMTP (Sesuaikan dengan yang lama)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'kevin19305.ib@gmail.com'; // Ganti Email Kamu
                $mail->Password   = 'sxkl vipy bfsx ljfe';     // Ganti App Password Kamu
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('no-reply@payroll.com', 'Payroll System Admin');
                $mail->addAddress($email);

                // PERBAIKAN LINK: Arahkan ke folder auth/reset_password.php
                $resetLink = "http://localhost/Web_Payroll/auth/reset_password.php?token=" . $token;

                $mail->isHTML(true);
                $mail->Subject = 'Reset Password - Payroll System';
                $mail->Body    = "
                    <h3>Permintaan Reset Password</h3>
                    <p>Klik tombol di bawah ini untuk reset password:</p>
                    <p><a href='$resetLink' style='background:#4361ee; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;'>RESET PASSWORD</a></p>
                    <small>Link valid selama 1 jam.</small>
                ";

                $mail->send();
                $msg = "Link reset telah dikirim ke email Anda.";
                $msg_type = "success";
            } catch (Exception $e) {
                $msg = "Gagal kirim email: " . $mail->ErrorInfo;
                $msg_type = "error";
            }
        }
    } else {
        $msg = "Email tidak ditemukan!";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lupa Password</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); height: 100vh; display: flex; justify-content: center; align-items: center; font-family: sans-serif; }
        .login-card { background: white; width: 100%; max-width: 400px; padding: 40px; border-radius: 20px; text-align: center; }
        .brand-logo { width: 60px; height: 60px; background: #eef2ff; color: #4361ee; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 28px; margin: 0 auto 20px; }
        .input-group input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        .btn-login { width: 100%; padding: 12px; background: #4361ee; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
        .alert-success { background: #d1e7dd; color: #0f5132; }
        .alert-error { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-logo"><i class="fa-solid fa-key"></i></div>
        <h2>Lupa Password</h2>
        
        <?php if($msg): ?>
            <div class="alert <?php echo ($msg_type == 'success') ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Masukkan Email Anda" required>
            </div>
            <button type="submit" name="reset_request" class="btn-login">KIRIM LINK</button>
        </form>
        
        <br>
        <a href="index.php" style="color: #666; text-decoration: none;">Kembali ke Login</a>
    </div>
</body>
</html>