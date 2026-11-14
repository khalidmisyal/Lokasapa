<?php
/**
 * LOKASAPA - Halaman Profil User
 * 
 * BAGIAN ORANG 1 - AUTH & USER
 * CRUD: READ (Menampilkan data user dari database)
 * 
 * Fitur:
 * - Menampilkan data profil user yang sedang login
 * - Menampilkan riwayat pemesanan singkat
 * - Link ke halaman edit profil
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

// Ambil data user dari database
$query = "SELECT * FROM user WHERE id_user = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Hitung total pemesanan user
$query_count = "SELECT COUNT(*) as total FROM pemesanan WHERE id_user = ?";
$stmt_count = mysqli_prepare($conn, $query_count);
mysqli_stmt_bind_param($stmt_count, "i", $user_id);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$count_data = mysqli_fetch_assoc($result_count);
$total_pemesanan = $count_data['total'];

// Hitung total ulasan user
$query_ulasan = "SELECT COUNT(*) as total FROM ulasan WHERE id_user = ?";
$stmt_ulasan = mysqli_prepare($conn, $query_ulasan);
mysqli_stmt_bind_param($stmt_ulasan, "i", $user_id);
mysqli_stmt_execute($stmt_ulasan);
$result_ulasan = mysqli_stmt_get_result($stmt_ulasan);
$ulasan_data = mysqli_fetch_assoc($result_ulasan);
$total_ulasan = $ulasan_data['total'];

$page_title = "Profil Saya";
include '../layouts/header.php';
?>

<style>
    .profile-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        color: white;
        padding: 50px 0;
        margin-bottom: 40px;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 3rem;
        color: var(--primary-green);
    }
    
    .profile-name {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .profile-email {
        opacity: 0.9;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .info-card h5 {
        color: var(--primary-green);
        font-weight: bold;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    
    .info-card h5 i {
        margin-right: 10px;
    }
    
    .info-row {
        display: flex;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #666;
        width: 150px;
    }
    
    .info-value {
        color: #333;
        flex: 1;
    }
    
    .stat-box {
        background: linear-gradient(135deg, var(--light-green) 0%, var(--secondary-green) 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .stat-box i {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    
    .stat-box .number {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 10px 0;
    }
    
    .stat-box .label {
        opacity: 0.9;
        font-size: 0.95rem;
    }
    
    .btn-edit {
        background-color: var(--secondary-green);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-edit:hover {
        background-color: var(--primary-green);
        color: white;
        transform: translateY(-2px);
    }
</style>

<!-- Profile Header -->
<div class="profile-header">
    <div class="container text-center">
        <div class="profile-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <h1 class="profile-name"><?php echo htmlspecialchars($user['nama']); ?></h1>
        <p class="profile-email">
            <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($user['email']); ?>
        </p>
    </div>
</div>

<!-- Profile Content -->
<div class="container">
    <div class="row">
        <!-- Statistik -->
        <div class="col-md-4">
            <div class="stat-box">
                <i class="bi bi-calendar-check"></i>
                <div class="number"><?php echo $total_pemesanan; ?></div>
                <div class="label">Total Pemesanan</div>
            </div>
            
            <div class="stat-box">
                <i class="bi bi-star-fill"></i>
                <div class="number"><?php echo $total_ulasan; ?></div>
                <div class="label">Ulasan Diberikan</div>
            </div>
            
            <div class="text-center mt-3">
                <a href="edit.php" class="btn btn-edit">
                    <i class="bi bi-pencil"></i> Edit Profil
                </a>
            </div>
        </div>
        
        <!-- Informasi Detail -->
        <div class="col-md-8">
            <div class="info-card">
                <h5><i class="bi bi-person-badge"></i> Informasi Pribadi</h5>
                
                <div class="info-row">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['nama']); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Domisili</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['domisili']); ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Nomor HP</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['no_hp']); ?></div>
                </div>
            </div>
            
            <div class="info-card">
                <h5><i class="bi bi-clock-history"></i> Aktivitas Terkini</h5>
                
                <div class="d-grid gap-2">
                    <a href="../pemesanan/index.php" class="btn btn-outline-success">
                        <i class="bi bi-calendar-check"></i> Lihat Riwayat Pemesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include '../layouts/footer.php';
mysqli_close($conn); 
?>
