<?php
session_start();

// 1. PANGGIL KONEKSI & CONFIG (Mundur satu folder ../)
require '../config/koneksi.php';
require '../config/google_auth.php';

if (isset($_GET['code'])) {
    // 2. TUKAR KODE DENGAN TOKEN
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if(!isset($token['error'])){
        $client->setAccessToken($token['access_token']);
        
        // 3. AMBIL DATA USER DARI GOOGLE
        $google_oauth = new Google\Service\Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $google_id = $google_account_info->id;
        $picture = $google_account_info->picture;

        // 4. CEK DATABASE: APAKAH EMAIL INI SUDAH DIDAFTARKAN ADMIN?
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // --- SKENARIO A: EMAIL TERDAFTAR (User Valid) ---
            
            // Update Google ID & Foto terbaru biar sinkron
            $update = $pdo->prepare("UPDATE users SET google_id = ?, avatar = ? WHERE id = ?");
            $update->execute([$google_id, $picture, $user['id']]);
            
            // Set Session Login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            
            // Gunakan nama dari Google jika di database belum ada nama
            $_SESSION['nama'] = $name; 

            // Redirect Sukses ke Dashboard
            header("Location: ../views/dashboard.php");
            exit;

        } else {
            // --- SKENARIO B: EMAIL TIDAK DIKENAL (Orang Asing) ---
            // Tolak akses dan kembalikan ke halaman login
            
            echo "<script>
                alert('AKSES DITOLAK! Email Google ($email) belum terdaftar di sistem Payroll. Silakan hubungi Admin/HRD untuk pendaftaran akun.');
                window.location = '../auth/index.php';
            </script>";
            exit;
        }

    } else {
        // Error dari Google (misal batal login)
        echo "<script>
            alert('Gagal login ke Google. Silakan coba lagi.');
            window.location = '../auth/index.php';
        </script>";
        exit;
    }
} else {
    // Jika file ini dibuka paksa tanpa login
    header("Location: ../auth/index.php");
    exit;
}
?>