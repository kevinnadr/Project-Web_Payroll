<?php
session_start();

// 1. MUNDUR SATU LANGKAH KE CONFIG (PENTING!)
require '../config/koneksi.php';

// 2. Cek apakah ada ID yang dikirim
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 3. Hapus data berdasarkan ID
        $stmt = $pdo->prepare("DELETE FROM pegawai WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Jika gagal, diam saja atau bisa log error
    }
}

// 4. KEMBALI KE FOLDER VIEWS
header("Location: ../views/data_pegawai.php");
exit;
?>