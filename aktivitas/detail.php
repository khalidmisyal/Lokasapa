<?php
/**
 * LOKASAPA - Detail Aktivitas
 * CRUD: READ (Menampilkan detail aktivitas lengkap)
 * 
 * Fitur:
 * - Menampilkan detail lengkap aktivitas
 * - Menampilkan informasi komunitas
 * - Menampilkan ulasan dari user lain
 * - Tombol pesan aktivitas
 */

session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Set base URL
$base_url = '/lokasapa/';

// Ambil id aktivitas
$id_aktivitas = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query detail aktivitas
$query = "SELECT a.*, k.nama_kategori, kom.* 
          FROM aktivitas a
          JOIN kategori k ON a.id_kategori = k.id_kategori
          JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
          WHERE a.id_aktivitas = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_aktivitas);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header('Location: ../index.php');
    exit;
}

$aktivitas = mysqli_fetch_assoc($result);

// Query ulasan untuk aktivitas ini
$query_ulasan = "SELECT u.*, us.nama, us.domisili 
                 FROM ulasan u
                 JOIN user us ON u.id_user = us.id_user
                 WHERE u.id_aktivitas = ?
                 ORDER BY u.id_ulasan DESC";

$stmt_ulasan = mysqli_prepare($conn, $query_ulasan);
mysqli_stmt_bind_param($stmt_ulasan, "i", $id_aktivitas);
mysqli_stmt_execute($stmt_ulasan);
$result_ulasan = mysqli_stmt_get_result($stmt_ulasan);

// Hitung rata-rata rating
$query_avg = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ulasan 
              FROM ulasan WHERE id_aktivitas = ?";
$stmt_avg = mysqli_prepare($conn, $query_avg);
mysqli_stmt_bind_param($stmt_avg, "i", $id_aktivitas);
mysqli_stmt_execute($stmt_avg);
$result_avg = mysqli_stmt_get_result($stmt_avg);
$avg_data = mysqli_fetch_assoc($result_avg);

$page_title = $aktivitas['nama_aktivitas'];
include '../layouts/header.php';
?>

<style>
    .detail-header {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 30px;
        margin-bottom: 30px;
    }
    
    .detail-img {
        width: 100%;
        height: 450px;
        object-fit: cover;
    }
    
    .detail-body {
        padding: 30px;
    }
    
    .kategori-badge {
        background-color: var(--light-green);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-block;
        margin-bottom: 15px;
    }
    
    .aktivitas-title {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 20px;
    }
    
    .rating-section {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .stars {
        color: #ffc107;
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    .rating-text {
        color: #666;
        font-size: 0.95rem;
    }
    
    .info-item {
        display: flex;
        align-items: start;
        margin-bottom: 15px;
        padding: 12px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .info-item i {
        color: var(--secondary-green);
        font-size: 1.3rem;
        margin-right: 15px;
        margin-top: 3px;
    }
    
    .info-content h6 {
        margin: 0 0 5px 0;
        color: var(--primary-green);
        font-weight: 600;
    }
    
    .info-content p {
        margin: 0;
        color: #666;
    }
    
    .harga-box {
        background: linear-gradient(135deg, var(--secondary-green) 0%, var(--primary-green) 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .harga-box .harga {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 15px 0;
    }
    
    .harga-box .per-orang {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .btn-pesan {
        background-color: white;
        color: var(--primary-green);
        border: none;
        padding: 15px 30px;
        border-radius: 10px;
        font-weight: bold;
        font-size: 1.1rem;
        width: 100%;
        transition: all 0.3s;
    }
    
    .btn-pesan:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .section-title {
        color: var(--primary-green);
        font-weight: bold;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }
    
    .komunitas-box {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .ulasan-item {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .ulasan-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }
    
    .user-info {
        display: flex;
        align-items: center;
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        background-color: var(--light-green);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
    }
    
    .user-name {
        font-weight: 600;
        color: var(--primary-green);
    }
    
    .user-location {
        font-size: 0.85rem;
        color: #999;
    }
    
    .ulasan-rating {
        color: #ffc107;
    }
    
    .ulasan-komentar {
        color: #666;
        line-height: 1.6;
    }
</style>

<!-- Detail Content -->
<div class="container">
    <div class="row">
        <!-- Kolom Kiri: Detail Aktivitas -->
        <div class="col-lg-8">
            <div class="detail-header">
                <?php
                // Gunakan gambar lokal dari folder assets/images/aktivitas/
                $img_url = get_image_path($aktivitas['foto'], 'aktivitas', $base_url);
                ?>
                <img src="<?php echo $img_url; ?>" class="detail-img" alt="<?php echo htmlspecialchars($aktivitas['nama_aktivitas']); ?>">
                
                <div class="detail-body">
                    <span class="kategori-badge">
                        <i class="bi bi-tag"></i> <?php echo ucfirst($aktivitas['nama_kategori']); ?>
                    </span>
                    
                    <h1 class="aktivitas-title"><?php echo htmlspecialchars($aktivitas['nama_aktivitas']); ?></h1>
                    
                    <?php if($avg_data['total_ulasan'] > 0): ?>
                    <div class="rating-section">
                        <div class="stars">
                            <?php 
                            $avg_rating = round($avg_data['avg_rating'], 1);
                            for($i = 1; $i <= 5; $i++): 
                                if($i <= $avg_rating):
                                    echo '<i class="bi bi-star-fill"></i>';
                                else:
                                    echo '<i class="bi bi-star"></i>';
                                endif;
                            endfor;
                            ?>
                        </div>
                        <span class="rating-text">
                            <?php echo $avg_rating; ?> dari 5 (<?php echo $avg_data['total_ulasan']; ?> ulasan)
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-item">
                        <i class="bi bi-align-left"></i>
                        <div class="info-content">
                            <h6>Deskripsi</h6>
                            <p><?php echo nl2br(htmlspecialchars($aktivitas['deskripsi'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div class="info-content">
                            <h6>Lokasi</h6>
                            <p><?php echo htmlspecialchars($aktivitas['lokasi']); ?></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="bi bi-people"></i>
                        <div class="info-content">
                            <h6>Kuota</h6>
                            <p><?php echo $aktivitas['kuota']; ?> orang per kegiatan</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Komunitas -->
            <div class="komunitas-box mb-4">
                <h3 class="section-title"><i class="bi bi-building"></i> Tentang Komunitas</h3>
                <h5 class="text-primary"><?php echo htmlspecialchars($aktivitas['nama_komunitas']); ?></h5>
                <p class="mb-2">
                    <i class="bi bi-geo-alt text-success"></i> <?php echo htmlspecialchars($aktivitas['lokasi']); ?>
                </p>
                <p class="mb-2">
                    <i class="bi bi-tag text-success"></i> <?php echo htmlspecialchars($aktivitas['tipe_komunitas']); ?>
                </p>
                <p class="text-muted"><?php echo htmlspecialchars($aktivitas['deskripsi']); ?></p>
            </div>
            
            <!-- Ulasan -->
            <div class="mb-4">
                <h3 class="section-title"><i class="bi bi-chat-quote"></i> Ulasan Peserta</h3>
                
                <?php if(mysqli_num_rows($result_ulasan) > 0): ?>
                    <?php while($ulasan = mysqli_fetch_assoc($result_ulasan)): ?>
                    <div class="ulasan-item">
                        <div class="ulasan-header">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="user-name"><?php echo htmlspecialchars($ulasan['nama']); ?></div>
                                    <div class="user-location">
                                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($ulasan['domisili']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="ulasan-rating">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= $ulasan['rating']): ?>
                                        <i class="bi bi-star-fill"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="ulasan-komentar"><?php echo nl2br(htmlspecialchars($ulasan['komentar'])); ?></p>
                        
                        <?php 
                        // Jika ulasan milik user yang sedang login, tampilkan tombol edit
                        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ulasan['id_user']): 
                        ?>
                        <div class="mt-2">
                            <a href="../ulasan/edit.php?id=<?php echo $ulasan['id_ulasan']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit Ulasan Saya
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-quote" style="font-size: 3rem;"></i>
                        <p class="mt-3">Belum ada ulasan untuk aktivitas ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Kolom Kanan: Harga & Pemesanan -->
        <div class="col-lg-4">
            <div class="harga-box sticky-top" style="top: 20px;">
                <h5>Harga</h5>
                <div class="harga">Rp <?php echo number_format($aktivitas['harga'], 0, ',', '.'); ?></div>
                <p class="per-orang">per orang</p>
                
                <hr style="border-color: rgba(255,255,255,0.3); margin: 20px 0;">
                
                <p class="mb-3"><i class="bi bi-people"></i> Kuota: <?php echo $aktivitas['kuota']; ?> orang</p>
                
                <a href="../pemesanan/create.php?id=<?php echo $aktivitas['id_aktivitas']; ?>" class="btn btn-pesan">
                    <i class="bi bi-calendar-check"></i> Pesan Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
include '../layouts/footer.php';

mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt_ulasan);
mysqli_stmt_close($stmt_avg);
mysqli_close($conn); 
?>
