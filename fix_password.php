<?php
require 'config/koneksi.php';

// 1. Password yang dimau
$password_baru = 'admin123';

// 2. Enkripsi password (Hashing)
$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

try {
    // 3. Update ke database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@test.com'");
    $stmt->execute([$password_hash]);
    
    echo "<h1>SUKSES!</h1>";
    echo "Password untuk <b>admin@test.com</b> sudah di-reset menjadi: <b>admin123</b><br>";
    echo "Silakan <a href='index.php'>LOGIN DISINI</a>";
    
} catch (PDOException $e) {
    echo "Gagal update: " . $e->getMessage();
}
?>