<?php
/**
 * LOKASAPA - Riwayat Pemesanan User
 * CRUD: READ (Menampilkan semua pemesanan user)
 * 
 * Fitur:
 * - Menampilkan list semua pemesanan user
 * - Status pemesanan (Menunggu Pembayaran, Diproses, Selesai)
 * - Link ke detail pemesanan
 * - Link untuk memberi ulasan (jika sudah selesai)
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

// Ambil semua pemesanan user
$query = "SELECT p.*, a.nama_aktivitas, a.lokasi, a.foto
          FROM pemesanan p
          JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
          WHERE p.id_user = ?
          ORDER BY p.tgl_pesan DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$page_title = "Pesanan Saya";
include '../layouts/header.php';
?>

<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        color: white;
        padding: 50px 0;
        margin-bottom: 40px;
    }
    
    .page-header h2 {
        margin: 0;
        font-weight: bold;
        font-size: 2rem;
    }
    
    .pesanan-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: transform 0.3s;
    }
    
    .pesanan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .pesanan-header {
        background: #f8f9fa;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e9ecef;
    }
    
    .pesanan-id {
        font-weight: 600;
        color: var(--primary-green);
    }
    
    .pesanan-date {
        color: #666;
        font-size: 0.9rem;
    }
    
    .pesanan-body {
        padding: 20px;
    }
    
    .aktivitas-info {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .aktivitas-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
    }
    
    .aktivitas-details {
        flex: 1;
    }
    
    .aktivitas-name {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 8px;
    }
    
    .info-row {
        display: flex;
        gap: 5px;
        margin-bottom: 5px;
        color: #666;
        font-size: 0.95rem;
    }
    
    .info-row i {
        color: var(--secondary-green);
        width: 20px;
    }
    
    .pesanan-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }
    
    .total-harga {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--secondary-green);
    }
    
    .total-harga small {
        font-size: 0.8rem;
        font-weight: normal;
        color: #999;
    }
    
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .status-menunggu {
        background-color: #ffc107;
        color: #333;
    }
    
    .status-diproses {
        background-color: #17a2b8;
        color: white;
    }
    
    .status-selesai {
        background-color: #28a745;
        color: white;
    }
    
    .btn-detail {
        background-color: var(--secondary-green);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s;
    }
    
    .btn-detail:hover {
        background-color: var(--primary-green);
        color: white;
    }
    
    .btn-ulasan {
        background-color: #ffc107;
        color: #333;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s;
    }
    
    .btn-ulasan:hover {
        background-color: #ffb300;
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }
    
    .empty-state i {
        font-size: 5rem;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        color: #666;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #999;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2><i class="bi bi-calendar-check"></i> Pesanan Saya</h2>
        <p class="mb-0 mt-2" style="opacity: 0.9;">Kelola dan pantau semua pemesanan aktivitas Anda</p>
    </div>
</div>

<!-- Content -->
<div class="container">
    <?php 
    // Tampilkan pesan sukses/error dari session
    if(isset($_SESSION['success_message'])): 
    ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
        unset($_SESSION['success_message']);
    endif; 
    
    if(isset($_SESSION['error_message'])): 
    ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle"></i> <?php echo $_SESSION['error_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
        unset($_SESSION['error_message']);
    endif; 
    ?>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="mb-4">
            <p class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Anda memiliki <strong><?php echo mysqli_num_rows($result); ?></strong> pemesanan
            </p>
        </div>
        
        <?php while($pesanan = mysqli_fetch_assoc($result)): 
            // Tentukan class status
            $status_class = '';
            if($pesanan['status'] == 'Menunggu Pembayaran') $status_class = 'status-menunggu';
            elseif($pesanan['status'] == 'Diproses') $status_class = 'status-diproses';
            elseif($pesanan['status'] == 'Selesai') $status_class = 'status-selesai';
            
            // Cek apakah user sudah memberikan ulasan
            $query_cek_ulasan = "SELECT id_ulasan FROM ulasan WHERE id_user = ? AND id_aktivitas = ?";
            $stmt_cek = mysqli_prepare($conn, $query_cek_ulasan);
            mysqli_stmt_bind_param($stmt_cek, "ii", $user_id, $pesanan['id_aktivitas']);
            mysqli_stmt_execute($stmt_cek);
            $result_cek = mysqli_stmt_get_result($stmt_cek);
            $sudah_ulasan = mysqli_num_rows($result_cek) > 0;
        ?>
        
        <div class="pesanan-card">
            <div class="pesanan-header">
                <div class="pesanan-id">
                    <i class="bi bi-receipt"></i> Pesanan #<?php echo $pesanan['id_pemesanan']; ?>
                </div>
                <div class="pesanan-date">
                    <i class="bi bi-calendar3"></i> <?php echo date('d M Y', strtotime($pesanan['tgl_pesan'])); ?>
                </div>
            </div>
            
            <div class="pesanan-body">
                <div class="aktivitas-info">
                    <?php $img_url = get_image_path($pesanan['foto'], 'aktivitas', $base_url); ?>
                    <img src="<?php echo $img_url; ?>" 
                         class="aktivitas-img" 
                         alt="<?php echo htmlspecialchars($pesanan['nama_aktivitas']); ?>">
                    
                    <div class="aktivitas-details">
                        <div class="aktivitas-name"><?php echo htmlspecialchars($pesanan['nama_aktivitas']); ?></div>
                        
                        <div class="info-row">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span><?php echo htmlspecialchars($pesanan['lokasi']); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <i class="bi bi-calendar-event"></i>
                            <span>Tanggal Kunjungan: <?php echo date('d M Y', strtotime($pesanan['tgl_kunjungan'])); ?></span>
                        </div>
                        
                        <div class="info-row">
                            <i class="bi bi-people-fill"></i>
                            <span><?php echo $pesanan['jumlah_orang']; ?> orang</span>
                        </div>
                        
                        <div class="info-row">
                            <i class="bi bi-credit-card"></i>
                            <span><?php echo htmlspecialchars($pesanan['metode_pembayaran']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="pesanan-footer">
                    <div>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php echo $pesanan['status']; ?>
                        </span>
                    </div>
                    
                    <div class="total-harga">
                        Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                        <small>Total</small>
                    </div>
                </div>
                
                <div class="mt-3 d-flex gap-2">
                    <a href="detail.php?id=<?php echo $pesanan['id_pemesanan']; ?>" class="btn btn-detail">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </a>
                    
                    <?php if($pesanan['status'] == 'Selesai' && !$sudah_ulasan): ?>
                    <a href="../ulasan/create.php?id_pemesanan=<?php echo $pesanan['id_pemesanan']; ?>" class="btn btn-ulasan">
                        <i class="bi bi-star"></i> Beri Ulasan
                    </a>
                    <?php endif; ?>
                    
                    <?php if($pesanan['status'] == 'Menunggu Pembayaran'): ?>
                    <a href="cancel.php?id=<?php echo $pesanan['id_pemesanan']; ?>" 
                       class="btn btn-outline-danger"
                       onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                        <i class="bi bi-x-circle"></i> Batalkan
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php endwhile; ?>
        
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>Belum Ada Pesanan</h3>
            <p>Anda belum memiliki pesanan aktivitas. Mulai jelajahi dan pesan sekarang!</p>
            <a href="../index.php" class="btn btn-detail mt-3" style="padding: 12px 30px;">
                <i class="bi bi-compass"></i> Jelajahi Aktivitas
            </a>
        </div>
    <?php endif; ?>
</div>

<?php 
include '../layouts/footer.php';
mysqli_close($conn); 
?>
