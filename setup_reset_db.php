<?php
require 'config/koneksi.php';

try {
    // Cek apakah kolom reset_token sudah ada
    $check = $pdo->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    
    if ($check->rowCount() == 0) {
        // Tambahkan kolom jika belum ada
        $sql = "ALTER TABLE users 
                ADD COLUMN reset_token VARCHAR(255) NULL AFTER password,
                ADD COLUMN reset_expiry DATETIME NULL AFTER reset_token";
        
        $pdo->exec($sql);
        echo "Berhasil menambahkan kolom reset_token dan reset_expiry ke tabel users.";
    } else {
        echo "Kolom reset_token sudah ada. Tidak perlu perubahan.";
    }
} catch (PDOException $e) {
    echo "Gagal: " . $e->getMessage();
}
?>
