<?php
session_start();
require '../config/koneksi.php'; // Mundur satu folder (../)

if (isset($_POST['simpan'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO pegawai (nip, nama, email, gaji_pokok) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['nip'], $_POST['nama'], $_POST['email'], $_POST['gaji']]);
        header("Location: ../dashboard.php"); // Balik ke luar (../)
        exit;
    } catch (PDOException $e) {
        $error = "Gagal! Mungkin NIP sudah terpakai.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pegawai</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <h2>Tambah Pegawai</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        
        <form method="POST">
            <label>NIP</label><input type="text" name="nip" required>
            <label>Nama</label><input type="text" name="nama" required>
            <label>Email</label><input type="email" name="email" required>
            <label>Gaji</label><input type="number" name="gaji" required>
            
            <button type="submit" name="simpan" class="btn btn-green">SIMPAN</button>
            <a href="../dashboard.php" class="btn btn-red">BATAL</a>
        </form>
    </div>
</body>
</html>