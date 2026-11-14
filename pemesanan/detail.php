<?php
/**
 * LOKASAPA - Detail Pemesanan
 * CRUD: READ (Menampilkan detail 1 pemesanan)
 * 
 * Fitur:
 * - Menampilkan detail lengkap 1 pemesanan
 * - Informasi aktivitas yang dipesan
 * - Status pembayaran dan kunjungan
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
$id_pemesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail pemesanan (pastikan milik user yang login)
$query = "SELECT p.*, a.nama_aktivitas, a.lokasi, a.deskripsi, a.foto, a.kuota,
          k.nama_kategori, kom.nama_komunitas, kom.tipe_komunitas
          FROM pemesanan p
          JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
          JOIN kategori k ON a.id_kategori = k.id_kategori
          JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
          WHERE p.id_pemesanan = ? AND p.id_user = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_pemesanan, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit;
}

$pesanan = mysqli_fetch_assoc($result);

$page_title = "Detail Pesanan #" . $pesanan['id_pemesanan'];
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
    
    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .section-title {
        color: var(--primary-green);
        font-weight: bold;
        margin-bottom: 20px;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 10px;
    }
    
    .aktivitas-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .aktivitas-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 15px;
    }
    
    .info-table {
        width: 100%;
    }
    
    .info-table tr {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-table tr:last-child {
        border-bottom: none;
    }
    
    .info-table td {
        padding: 12px 0;
    }
    
    .info-label {
        font-weight: 600;
        color: #666;
        width: 200px;
    }
    
    .info-value {
        color: #333;
    }
    
    .status-badge {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
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
    
    .total-box {
        background: linear-gradient(135deg, var(--secondary-green) 0%, var(--primary-green) 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        text-align: center;
    }
    
    .total-box .label {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-bottom: 10px;
    }
    
    .total-box .amount {
        font-size: 2.5rem;
        font-weight: bold;
    }
    
    .btn-back {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-back:hover {
        background-color: #5a6268;
        color: white;
    }
    
    .btn-cancel {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-cancel:hover {
        background-color: #c82333;
        color: white;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h2><i class="bi bi-receipt"></i> Detail Pesanan #<?php echo $pesanan['id_pemesanan']; ?></h2>
    </div>
</div>

<!-- Content -->
<div class="container">
    <div class="row">
        <!-- Kolom Kiri: Detail Pemesanan -->
        <div class="col-lg-8">
            <div class="detail-card">
                <h5 class="section-title"><i class="bi bi-card-list"></i> Informasi Pemesanan</h5>
                
                <table class="info-table">
                    <tr>
                        <td class="info-label">ID Pemesanan</td>
                        <td class="info-value"><strong>#<?php echo $pesanan['id_pemesanan']; ?></strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Pemesanan</td>
                        <td class="info-value"><?php echo date('d F Y', strtotime($pesanan['tgl_pesan'])); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Kunjungan</td>
                        <td class="info-value"><strong><?php echo date('d F Y', strtotime($pesanan['tgl_kunjungan'])); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">Jumlah Peserta</td>
                        <td class="info-value"><?php echo $pesanan['jumlah_orang']; ?> orang</td>
                    </tr>
                    <tr>
                        <td class="info-label">Metode Pembayaran</td>
                        <td class="info-value"><?php echo htmlspecialchars($pesanan['metode_pembayaran']); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Status</td>
                        <td class="info-value">
                            <?php 
                            $status_class = '';
                            if($pesanan['status'] == 'Menunggu Pembayaran') $status_class = 'status-menunggu';
                            elseif($pesanan['status'] == 'Diproses') $status_class = 'status-diproses';
                            elseif($pesanan['status'] == 'Selesai') $status_class = 'status-selesai';
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $pesanan['status']; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="detail-card">
                <h5 class="section-title"><i class="bi bi-star"></i> Detail Aktivitas</h5>
                
                <?php $img_url = get_image_path($pesanan['foto'], 'aktivitas', $base_url); ?>
                <img src="<?php echo $img_url; ?>" 
                     class="aktivitas-img" 
                     alt="<?php echo htmlspecialchars($pesanan['nama_aktivitas']); ?>">
                
                <div class="aktivitas-name"><?php echo htmlspecialchars($pesanan['nama_aktivitas']); ?></div>
                
                <table class="info-table">
                    <tr>
                        <td class="info-label">Kategori</td>
                        <td class="info-value"><?php echo ucfirst($pesanan['nama_kategori']); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Lokasi</td>
                        <td class="info-value"><?php echo htmlspecialchars($pesanan['lokasi']); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Komunitas</td>
                        <td class="info-value"><?php echo htmlspecialchars($pesanan['nama_komunitas']); ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Tipe Komunitas</td>
                        <td class="info-value"><?php echo htmlspecialchars($pesanan['tipe_komunitas']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Kolom Kanan: Total & Actions -->
        <div class="col-lg-4">
            <div class="total-box mb-4">
                <div class="label">Total Pembayaran</div>
                <div class="amount">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></div>
            </div>
            
            <div class="d-grid gap-2">
                <a href="index.php" class="btn btn-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan
                </a>
                
                <?php if($pesanan['status'] == 'Menunggu Pembayaran'): ?>
                <a href="cancel.php?id=<?php echo $pesanan['id_pemesanan']; ?>" 
                   class="btn btn-cancel"
                   onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                    <i class="bi bi-x-circle"></i> Batalkan Pesanan
                </a>
                <?php endif; ?>
                
                <?php if($pesanan['status'] == 'Selesai'): ?>
                <?php
                // Cek apakah sudah ada ulasan
                $query_cek = "SELECT id_ulasan FROM ulasan WHERE id_user = ? AND id_aktivitas = ?";
                $stmt_cek = mysqli_prepare($conn, $query_cek);
                mysqli_stmt_bind_param($stmt_cek, "ii", $user_id, $pesanan['id_aktivitas']);
                mysqli_stmt_execute($stmt_cek);
                $result_cek = mysqli_stmt_get_result($stmt_cek);
                
                if(mysqli_num_rows($result_cek) == 0):
                ?>
                <a href="../ulasan/create.php?id_pemesanan=<?php echo $pesanan['id_pemesanan']; ?>" 
                   class="btn" style="background-color: #ffc107; color: #333; font-weight: 600;">
                    <i class="bi bi-star"></i> Beri Ulasan
                </a>
                <?php else: ?>
                <button class="btn btn-success" disabled>
                    <i class="bi bi-check-circle"></i> Sudah Diulas
                </button>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <?php if($pesanan['status'] == 'Menunggu Pembayaran'): ?>
            <div class="alert alert-warning mt-3" style="border-radius: 10px;">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Perhatian!</strong><br>
                Segera lakukan pembayaran untuk mengkonfirmasi pesanan Anda.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
include '../layouts/footer.php';
mysqli_close($conn); 
?>
