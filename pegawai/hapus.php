<?php
session_start();
require '../config/koneksi.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM pegawai WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}
header("Location: ../dashboard.php"); // Balik ke dashboard
?>