<?php
// Tanda '../' artinya mundur satu langkah untuk mencari folder vendor
require __DIR__ . '/../vendor/autoload.php'; 

$host = 'localhost';
$db   = 'Web_Payroll';
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi Gagal: " . $e->getMessage());
}
?>