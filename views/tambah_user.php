<?php
session_start();
// PERBAIKAN PATH
require '../config/koneksi.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("Location: dashboard.php"); exit; 
}

$page = 'user';

if (isset($_POST['simpan'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $cek = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $cek->execute([$email]);
    
    if ($cek->rowCount() > 0) {
        $error = "Email sudah terdaftar! Gunakan email lain.";
    } else {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        if ($stmt->execute([$email, $hashed_pass, $role])) {
            header("Location: manajemen_user.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User - Payroll</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        .input-group input, .input-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; box-sizing: border-box;}
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; gap: 0; } }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../components/sidebar.php'; ?>

        <div class="main-content">
            <div class="header">
                <h2><i class="fa-solid fa-user-plus"></i> Tambah User Sistem</h2>
            </div>

            <div class="container" style="max-width: 800px;">
                <?php if(isset($error)): ?>
                    <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:8px; margin-bottom:20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-grid">
                        <div>
                            <div class="input-group">
                                <label>Email Login</label>
                                <input type="email" name="email" placeholder="nama@perusahaan.com" required>
                            </div>
                            <div class="input-group">
                                <label>Password</label>
                                <input type="text" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                            </div>
                        </div>
                        
                        <div>
                            <div class="input-group">
                                <label>Role / Jabatan</label>
                                <select name="role" required>
                                    <option value="staff">Staff (Hanya Data Pegawai)</option>
                                    <option value="admin">Admin (Akses Penuh)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

                    <div style="display: flex; justify-content: flex-end; gap: 15px;">
                        <a href="manajemen_user.php" class="btn btn-red" style="background-color: #64748b;">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-green">SIMPAN USER</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>