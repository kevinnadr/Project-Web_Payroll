<?php
session_start();

// 1. PANGGIL FILE KONEKSI & CONFIG (Mundur satu folder ../)
require_once '../config/koneksi.php';
require_once '../config/google_auth.php';

// 2. CEK STATUS LOGIN
if (isset($_SESSION['user_id'])) {
    header("Location: ../views/dashboard.php");
    exit;
}

// 3. LOGIKA LOGIN MANUAL
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['nama'] ?? "User"; 
            header("Location: ../views/dashboard.php");
            exit;
        } else {
            $error = "Email atau Password salah!";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan sistem.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Payroll System</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- CSS MODERN LOGIN --- */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 1);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Hiasan Header */
        .login-header { margin-bottom: 30px; }
        .logo-icon {
            font-size: 50px;
            color: #4361ee;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 6px rgba(67, 97, 238, 0.3));
        }
        .login-header h2 { color: #333; font-weight: 700; font-size: 24px; }
        .login-header p { color: #888; font-size: 14px; margin-top: 5px; }

        /* Input Group dengan Ikon */
        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }
        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 16px;
            transition: 0.3s;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px; /* Padding kiri besar buat ikon */
            border: 2px solid #eee;
            border-radius: 12px;
            font-size: 14px;
            transition: 0.3s;
            background: #f9f9f9;
        }
        .form-control:focus {
            border-color: #4361ee;
            background: #fff;
            outline: none;
        }
        .form-control:focus + i { color: #4361ee; }

        /* Tombol Login */
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #4361ee, #3f37c9);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        /* Divider */
        .divider {
            margin: 25px 0;
            position: relative;
        }
        .divider::before {
            content: "";
            position: absolute;
            left: 0; top: 50%;
            width: 100%; height: 1px;
            background: #eee;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            color: #999;
            font-size: 12px;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }

        /* Tombol Google */
        .btn-google {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            background: white;
            border: 2px solid #eee;
            border-radius: 12px;
            color: #444;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: 0.3s;
            gap: 10px;
        }
        .btn-google:hover {
            background: #f8f9fa;
            border-color: #ddd;
        }
        .btn-google img { width: 22px; height: 22px; }

        /* Error Message */
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #fca5a5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-sack-dollar logo-icon"></i>
            <h2>Selamat Datang</h2>
            <p>Silakan login untuk mengakses Payroll</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" required>
                    <i class="fa-regular fa-envelope"></i>
                </div>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" class="form-control" placeholder="Masukan password anda" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-primary">
                MASUK SEKARANG
            </button>
        </form>

        <div class="divider"><span>ATAU MASUK DENGAN</span></div>

        <a href="<?php echo $google_login_url; ?>" class="btn-google">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Logo">
            Google Account
        </a>

        <div style="margin-top: 25px;">
            <a href="forgot_password.php" style="color: #4361ee; text-decoration: none; font-size: 13px; font-weight: 500;">
                Lupa Password Anda?
            </a>
        </div>
    </div>

</body>
</html>