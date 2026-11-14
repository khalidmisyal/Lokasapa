<?php
/**
 * LOKASAPA - Form Buat Ulasan
 * 
 * BAGIAN ORANG 4 - ULASAN
 * CRUD: CREATE (Insert ulasan baru ke database)
 * 
 * Fitur:
 * - Form ulasan dengan rating bintang
 * - Validasi user sudah pernah pesan aktivitas
 * - Cek aktivitas sudah selesai
 * - Prevent duplikasi ulasan
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
$id_pemesanan = isset($_GET['id_pemesanan']) ? intval($_GET['id_pemesanan']) : 0;

// Ambil data pemesanan
$query_pesanan = "SELECT p.*, a.nama_aktivitas, a.id_aktivitas, a.lokasi
                  FROM pemesanan p
                  JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
                  WHERE p.id_pemesanan = ? AND p.id_user = ? AND p.status = 'Selesai'";

$stmt_pesanan = mysqli_prepare($conn, $query_pesanan);
mysqli_stmt_bind_param($stmt_pesanan, "ii", $id_pemesanan, $user_id);
mysqli_stmt_execute($stmt_pesanan);
$result_pesanan = mysqli_stmt_get_result($stmt_pesanan);

if(mysqli_num_rows($result_pesanan) == 0) {
    $_SESSION['error_message'] = 'Pemesanan tidak ditemukan atau belum selesai';
    header('Location: ../pemesanan/index.php');
    exit;
}

$pesanan = mysqli_fetch_assoc($result_pesanan);
$id_aktivitas = $pesanan['id_aktivitas'];

// Cek apakah user sudah pernah memberikan ulasan untuk aktivitas ini
$query_check = "SELECT id_ulasan FROM ulasan WHERE id_user = ? AND id_aktivitas = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $user_id, $id_aktivitas);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if(mysqli_num_rows($result_check) > 0) {
    $_SESSION['error_message'] = 'Anda sudah memberikan ulasan untuk aktivitas ini';
    header('Location: ../pemesanan/detail.php?id=' . $id_pemesanan);
    exit;
}

$error = '';
$success = false;

// Proses form ulasan
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = intval($_POST['rating']);
    $komentar = trim($_POST['komentar']);
    
    // Validasi
    if($rating < 1 || $rating > 5) {
        $error = 'Rating harus antara 1-5';
    } elseif(empty($komentar)) {
        $error = 'Komentar tidak boleh kosong';
    } else {
        // Insert ulasan
        $query_insert = "INSERT INTO ulasan (id_user, id_aktivitas, rating, komentar) VALUES (?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiis", $user_id, $id_aktivitas, $rating, $komentar);
        
        if(mysqli_stmt_execute($stmt_insert)) {
            $success = true;
        } else {
            $error = 'Terjadi kesalahan, silakan coba lagi';
        }
    }
}

$page_title = "Beri Ulasan";
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
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2><i class="bi bi-star"></i> Beri Ulasan</h2>
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
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> 
                <strong>Terima kasih!</strong> Ulasan Anda berhasil disimpan.
                <div class="mt-3">
                    <a href="../aktivitas/detail.php?id=<?php echo $id_aktivitas; ?>" class="btn btn-sm btn-success">
                        Lihat Ulasan Saya
                    </a>
                    <a href="../pemesanan/index.php" class="btn btn-sm btn-outline-success">
                        Kembali ke Pesanan
                    </a>
                </div>
            </div>
            <?php else: ?>
            
            <div class="form-card">
                <div class="aktivitas-info">
                    <div class="aktivitas-name"><?php echo htmlspecialchars($pesanan['nama_aktivitas']); ?></div>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($pesanan['lokasi']); ?>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar"></i> Kunjungan: <?php echo date('d M Y', strtotime($pesanan['tgl_kunjungan'])); ?>
                    </p>
                </div>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label">Berikan Rating</label>
                        <div class="rating-stars">
                            <input type="radio" name="rating" id="star5" value="5" required>
                            <label for="star5"><i class="bi bi-star-fill"></i></label>
                            
                            <input type="radio" name="rating" id="star4" value="4">
                            <label for="star4"><i class="bi bi-star-fill"></i></label>
                            
                            <input type="radio" name="rating" id="star3" value="3">
                            <label for="star3"><i class="bi bi-star-fill"></i></label>
                            
                            <input type="radio" name="rating" id="star2" value="2">
                            <label for="star2"><i class="bi bi-star-fill"></i></label>
                            
                            <input type="radio" name="rating" id="star1" value="1">
                            <label for="star1"><i class="bi bi-star-fill"></i></label>
                        </div>
                        <small class="text-muted">Klik bintang untuk memberikan rating</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Ceritakan Pengalaman Anda</label>
                        <textarea class="form-control" name="komentar" rows="6" required 
                                  placeholder="Bagaimana pengalaman Anda mengikuti aktivitas ini? Apa yang Anda suka? Apa yang bisa ditingkatkan?"></textarea>
                        <small class="text-muted">Minimal 20 karakter</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-send"></i> Kirim Ulasan
                        </button>
                        <a href="../pemesanan/detail.php?id=<?php echo $id_pemesanan; ?>" class="btn btn-cancel">
                            <i class="bi bi-x-lg"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
include '../layouts/footer.php';
mysqli_close($conn); 
?>
