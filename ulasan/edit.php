<?php
/**
 * LOKASAPA - Edit Ulasan
 * CRUD: UPDATE (Update ulasan yang sudah ada)
 * 
 * Fitur:
 * - Form edit ulasan
 * - Validasi kepemilikan ulasan (hanya pemilik yang bisa edit)
 * - Update rating dan komentar
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
$id_ulasan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data ulasan (pastikan milik user yang login)
$query = "SELECT u.*, a.nama_aktivitas, a.lokasi
          FROM ulasan u
          JOIN aktivitas a ON u.id_aktivitas = a.id_aktivitas
          WHERE u.id_ulasan = ? AND u.id_user = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_ulasan, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    $_SESSION['error_message'] = 'Ulasan tidak ditemukan';
    header('Location: ../pemesanan/index.php');
    exit;
}

$ulasan = mysqli_fetch_assoc($result);

$error = '';
$success = false;

// Proses update ulasan
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = intval($_POST['rating']);
    $komentar = trim($_POST['komentar']);
    
    // Validasi
    if($rating < 1 || $rating > 5) {
        $error = 'Rating harus antara 1-5';
    } elseif(empty($komentar)) {
        $error = 'Komentar tidak boleh kosong';
    } else {
        // Update ulasan
        $query_update = "UPDATE ulasan SET rating = ?, komentar = ? WHERE id_ulasan = ?";
        $stmt_update = mysqli_prepare($conn, $query_update);
        mysqli_stmt_bind_param($stmt_update, "isi", $rating, $komentar, $id_ulasan);
        
        if(mysqli_stmt_execute($stmt_update)) {
            $success = true;
            // Update data lokal
            $ulasan['rating'] = $rating;
            $ulasan['komentar'] = $komentar;
        } else {
            $error = 'Terjadi kesalahan, silakan coba lagi';
        }
    }
}

$page_title = "Edit Ulasan";
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
    
    .aktivitas-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .aktivitas-name {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    .rating-stars {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
    }
    
    .rating-stars input[type="radio"] {
        display: none;
    }
    
    .rating-stars label {
        font-size: 2.5rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .rating-stars input[type="radio"]:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #ffc107;
    }
    
    .rating-stars {
        flex-direction: row-reverse;
        justify-content: flex-end;
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
    
    .btn-submit {
        background-color: var(--secondary-green);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
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
    }
    
    .btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
    }
    
    .btn-delete:hover {
        background-color: #c82333;
        color: white;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2><i class="bi bi-pencil-square"></i> Edit Ulasan</h2>
    </div>
</div>

<!-- Content -->
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
                <i class="bi bi-check-circle"></i> Ulasan berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="form-card">
                <div class="aktivitas-info">
                    <div class="aktivitas-name"><?php echo htmlspecialchars($ulasan['nama_aktivitas']); ?></div>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($ulasan['lokasi']); ?>
                    </p>
                </div>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label">Rating</label>
                        <div class="rating-stars">
                            <?php for($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" 
                                   <?php echo ($ulasan['rating'] == $i) ? 'checked' : ''; ?> required>
                            <label for="star<?php echo $i; ?>"><i class="bi bi-star-fill"></i></label>
                            <?php endfor; ?>
                        </div>
                        <small class="text-muted">Klik bintang untuk mengubah rating</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Komentar</label>
                        <textarea class="form-control" name="komentar" rows="6" required 
                                  placeholder="Ceritakan pengalaman Anda..."><?php echo htmlspecialchars($ulasan['komentar']); ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2 justify-content-between">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-submit">
                                <i class="bi bi-check-lg"></i> Simpan Perubahan
                            </button>
                            <a href="../aktivitas/detail.php?id=<?php echo $ulasan['id_aktivitas']; ?>" class="btn btn-cancel">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                        </div>
                        <a href="delete.php?id=<?php echo $id_ulasan; ?>" 
                           class="btn btn-delete"
                           onclick="return confirm('Yakin ingin menghapus ulasan ini?')">
                            <i class="bi bi-trash"></i> Hapus Ulasan
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
