<?php
session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Jika sudah login, redirect
if(isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '../index.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($user = mysqli_fetch_assoc($result)) {
        // Note: Untuk produksi, gunakan password_hash dan password_verify
        if($password == $user['password']) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Email atau password salah';
        }
    } else {
        $error = 'Email atau password salah';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - LOKASAPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-green: #2d5016;
            --secondary-green: #4a7c3c;
            --light-green: #7fa663;
            --cream: #f5f1e8;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            max-width: 450px;
            width: 100%;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-section h1 {
            color: var(--primary-green);
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .logo-section p {
            color: #666;
            margin: 0;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 60, 0.25);
        }
        
        .btn-login {
            background-color: var(--secondary-green);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s, transform 0.2s;
        }
        
        .btn-login:hover {
            background-color: var(--primary-green);
            transform: translateY(-2px);
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 0.9rem;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: var(--secondary-green);
            font-weight: 600;
            text-decoration: none;
        }
        
        .register-link a:hover {
            color: var(--primary-green);
            text-decoration: underline;
        }
        
        .back-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-home a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-home a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="logo-section">
                <h1><i class="bi bi-globe-americas"></i> LOKASAPA</h1>
                <p>Masuk ke akun Anda</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="nama@email.com">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required 
                           placeholder="Masukkan password">
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>
            
            <div class="divider">
                <span>Belum punya akun?</span>
            </div>
            
            <div class="register-link">
                <a href="register.php<?php echo $redirect != '../index.php' ? '?redirect='.$redirect : ''; ?>">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </a>
            </div>
        </div>
        
        <div class="back-home">
            <a href="../index.php">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>