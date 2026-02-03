<?php
session_start();
require 'config/koneksi.php';

// Security Check (Wajib Admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: dashboard.php"); exit; 
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Cegah hapus diri sendiri (Backup security)
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='manajemen_user.php';</script>";
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: manajemen_user.php");
exit;
?>