<?php
/**
 * LOKASAPA - Halaman Edit Profil User
 * 
 * BAGIAN ORANG 1 - AUTH & USER
 * CRUD: UPDATE (Update data user di database)
 * 
 * Fitur:
 * - Form edit data profil
 * - Validasi input
 * - Update data ke database
 */

session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Set base URL
$base_url = '/lokasapa/';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user dari database
$query = "SELECT * FROM user WHERE id_user = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Proses update profil
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $domisili = trim($_POST['domisili']);
    $no_hp = trim($_POST['no_hp']);
    $password_baru = $_POST['password_baru'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validasi input
    if(empty($nama) || empty($email) || empty($domisili) || empty($no_hp)) {
        $error = 'Semua field harus diisi';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        // Cek apakah email sudah digunakan user lain
        $query_check = "SELECT id_user FROM user WHERE email = ? AND id_user != ?";
        $stmt_check = mysqli_prepare($conn, $query_check);
        mysqli_stmt_bind_param($stmt_check, "si", $email, $user_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if(mysqli_num_rows($result_check) > 0) {
            $error = 'Email sudah digunakan user lain';
        } else {
            // Jika password diisi, update dengan password baru
            if(!empty($password_baru)) {
                if(strlen($password_baru) < 6) {
                    $error = 'Password baru minimal 6 karakter';
                } elseif($password_baru !== $password_confirm) {
                    $error = 'Password baru dan konfirmasi tidak cocok';
                } else {
                    // Update dengan password baru
                    // NOTE: Untuk production, gunakan password_hash
                    $query_update = "UPDATE user SET nama = ?, email = ?, password = ?, domisili = ?, no_hp = ? WHERE id_user = ?";
                    $stmt_update = mysqli_prepare($conn, $query_update);
                    mysqli_stmt_bind_param($stmt_update, "sssssi", $nama, $email, $password_baru, $domisili, $no_hp, $user_id);
                }
            } else {
                // Update tanpa mengubah password
                $query_update = "UPDATE user SET nama = ?, email = ?, domisili = ?, no_hp = ? WHERE id_user = ?";
                $stmt_update = mysqli_prepare($conn, $query_update);
                mysqli_stmt_bind_param($stmt_update, "ssssi", $nama, $email, $domisili, $no_hp, $user_id);
            }
            
            if(!isset($error) || empty($error)) {
                if(mysqli_stmt_execute($stmt_update)) {
                    // Update session
                    $_SESSION['nama'] = $nama;
                    $_SESSION['email'] = $email;
                    
                    $success = 'Profil berhasil diperbarui';
                    
                    // Refresh data user
                    $user['nama'] = $nama;
                    $user['email'] = $email;
                    $user['domisili'] = $domisili;
                    $user['no_hp'] = $no_hp;
                } else {
                    $error = 'Terjadi kesalahan, silakan coba lagi';
                }
            }
        }
    }
}

$page_title = "Edit Profil";
include '../layouts/header.php';
?>

<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 40px;
    }
    
    .page-header h2 {
        margin: 0;
        font-weight: bold;
    }
    
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    
    .form-card h5 {
        color: var(--primary-green);
        font-weight: bold;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }
    
    .form-card h5 i {
        margin-right: 10px;
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
    
    .btn-save {
        background-color: var(--secondary-green);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-save:hover {
        background-color: var(--primary-green);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-cancel {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2><i class="bi bi-pencil-square"></i> Edit Profil</h2>
    </div>
</div>

<!-- Form Content -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="form-card">
                <h5><i class="bi bi-person-badge"></i> Informasi Pribadi</h5>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" required 
                               value="<?php echo htmlspecialchars($user['nama']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required 
                               value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Domisili</label>
                        <input type="text" class="form-control" name="domisili" required 
                               value="<?php echo htmlspecialchars($user['domisili']); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="tel" class="form-control" name="no_hp" required 
                               value="<?php echo htmlspecialchars($user['no_hp']); ?>">
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5><i class="bi bi-key"></i> Ubah Password (Opsional)</h5>
                    <p class="text-muted small">Kosongkan jika tidak ingin mengubah password</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password_baru" 
                               placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirm" 
                               placeholder="Ketik ulang password baru">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-save">
                            <i class="bi bi-check-lg"></i> Simpan Perubahan
                        </button>
                        <a href="profile.php" class="btn btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include '../layouts/footer.php';
mysqli_close($conn); 
?>
