<?php
session_start();
require '../config/koneksi.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("UPDATE pegawai SET nip=?, nama=?, email=?, gaji_pokok=? WHERE id=?");
    $stmt->execute([$_POST['nip'], $_POST['nama'], $_POST['email'], $_POST['gaji'], $id]);
    header("Location: ../dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Pegawai</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <h2>Edit Pegawai</h2>
        <form method="POST">
            <label>NIP</label><input type="text" name="nip" value="<?= $data['nip'] ?>" required>
            <label>Nama</label><input type="text" name="nama" value="<?= $data['nama'] ?>" required>
            <label>Email</label><input type="email" name="email" value="<?= $data['email'] ?>" required>
            <label>Gaji</label><input type="number" name="gaji" value="<?= $data['gaji_pokok'] ?>" required>
            
            <button type="submit" name="update" class="btn btn-blue">UPDATE</button>
            <a href="../dashboard.php" class="btn btn-red">BATAL</a>
        </form>
    </div>
</body>
</html>