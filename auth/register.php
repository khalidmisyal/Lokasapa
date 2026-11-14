<?php
/**
 * LOKASAPA - Halaman Registrasi
 * 
 * BAGIAN ORANG 1 - AUTH & USER
 * CRUD: CREATE (Insert user baru ke database)
 * 
 * Fitur:
 * - Form registrasi dengan validasi
 * - Password minimal 6 karakter (di production harus gunakan password_hash)
 * - Email harus unik
 * - Auto login setelah registrasi berhasil
 */

session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Set base URL
$base_url = '/lokasapa/';

// Jika sudah login, redirect ke beranda
if(isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

// Proses form registrasi
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan bersihkan input
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $domisili = trim($_POST['domisili']);
    $no_hp = trim($_POST['no_hp']);
    
    // Validasi input
    if(empty($nama) || empty($email) || empty($password) || empty($domisili) || empty($no_hp)) {
        $error = 'Semua field harus diisi';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif(strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif($password !== $password_confirm) {
        $error = 'Password dan konfirmasi password tidak cocok';
    } else {
        // Cek apakah email sudah terdaftar
        $query_check = "SELECT id_user FROM user WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $query_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if(mysqli_num_rows($result_check) > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Insert user baru
            // NOTE: Untuk production, gunakan password_hash($password, PASSWORD_DEFAULT)
            $query_insert = "INSERT INTO user (nama, email, password, domisili, no_hp) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $query_insert);
            mysqli_stmt_bind_param($stmt_insert, "sssss", $nama, $email, $password, $domisili, $no_hp);
            
            if(mysqli_stmt_execute($stmt_insert)) {
                // Registrasi berhasil, auto login
                $user_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $user_id;
                $_SESSION['nama'] = $nama;
                $_SESSION['email'] = $email;
                
                // Redirect ke beranda
                header('Location: ../index.php?register=success');
                exit;
            } else {
                $error = 'Terjadi kesalahan, silakan coba lagi';
            }
        }
    }
}

$page_title = "Daftar Akun";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - LOKASAPA</title>
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
            max-width: 500px;
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
        
        .btn-register {
            background-color: var(--secondary-green);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s, transform 0.2s;
        }
        
        .btn-register:hover {
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: var(--secondary-green);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
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
                <p>Daftar akun baru</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" required 
                           value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="nama@email.com">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required 
                           placeholder="Minimal 6 karakter">
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirm" required 
                           placeholder="Ketik ulang password">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Domisili</label>
                    <input type="text" class="form-control" name="domisili" required 
                           value="<?php echo isset($_POST['domisili']) ? htmlspecialchars($_POST['domisili']) : ''; ?>"
                           placeholder="Kota/Kabupaten">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="tel" class="form-control" name="no_hp" required 
                           value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>"
                           placeholder="08xxxxxxxxxx">
                </div>
                
                <button type="submit" class="btn btn-register">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </button>
            </form>
            
            <div class="divider">
                <span>Sudah punya akun?</span>
            </div>
            
            <div class="login-link">
                <a href="login.php">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk di sini
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
