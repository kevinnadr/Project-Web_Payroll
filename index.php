<?php
session_start();
// Arahkan ke folder config
require 'config/koneksi.php';

if(isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Login Gagal! Cek email & password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Payroll</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="background-color: #007bff;">
    <div class="login-box">
        <h2 style="text-align:center;">Login Admin</h2>
        <?php if(isset($error)) echo "<p style='color:red; text-align:center'>$error</p>"; ?>
        
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" value="admin@test.com" required>
            
            <label>Password</label>
            <input type="password" name="password" value="admin123" required>
            
            <button type="submit" name="login" class="btn btn-blue" style="width:100%">MASUK</button>
        </form>
    </div>
</body>
</html>