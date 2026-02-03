<?php
session_start();
// PERBAIKAN PATH
require '../config/koneksi.php';

$msg = "";
$token = $_GET['token'] ?? '';

// Cek Token
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("<h3 style='text-align:center; font-family:sans-serif;'>Error: Link tidak valid atau sudah kadaluarsa.<br><a href='forgot_password.php'>Minta Link Baru</a></h3>");
}

if(isset($_POST['reset_password'])) {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    
    if($pass1 !== $pass2) {
        $msg = "Password konfirmasi tidak sama!";
    } else {
        $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        if($update->execute([$hashed_password, $user['id']])) {
            echo "<script>alert('Password Berhasil Direset! Silakan Login.'); window.location='index.php';</script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); height: 100vh; display: flex; justify-content: center; align-items: center; font-family: sans-serif; }
        .login-card { background: white; padding: 40px; border-radius: 20px; width: 350px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Atur Password Baru</h2>
        <?php if($msg) echo "<p style='color:red'>$msg</p>"; ?>
        <form method="POST">
            <input type="password" name="pass1" placeholder="Password Baru" required minlength="4">
            <input type="password" name="pass2" placeholder="Ulangi Password" required minlength="4">
            <button type="submit" name="reset_password">SIMPAN PASSWORD</button>
        </form>
    </div>
</body>
</html>