<?php
/**
 * LOKASAPA - List Aktivitas per Kategori
 * 
 * BAGIAN ORANG 2 - KATEGORI & AKTIVITAS
 * CRUD: READ (Menampilkan aktivitas berdasarkan kategori)
 * 
 * Fitur:
 * - Menampilkan semua aktivitas dalam kategori tertentu
 * - Filter berdasarkan kategori dari parameter URL
 * - Link ke detail aktivitas
 */

session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Set base URL
$base_url = '/lokasapa/';

// Ambil kategori dari parameter URL
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Validasi kategori
$valid_kategori = ['budaya', 'alam', 'kuliner', 'konservasi'];
if(!in_array($kategori, $valid_kategori)) {
    header('Location: ../index.php');
    exit;
}

// Ambil data aktivitas berdasarkan kategori
$query = "SELECT a.*, k.nama_kategori, kom.nama_komunitas 
          FROM aktivitas a
          JOIN kategori k ON a.id_kategori = k.id_kategori
          JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
          WHERE k.nama_kategori = ?
          ORDER BY a.id_aktivitas DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $kategori);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Informasi kategori untuk display
$kategori_info = [
    'budaya' => [
        'title' => 'Aktivitas Budaya',
        'desc' => 'Pelajari seni, tari, dan tradisi dari komunitas lokal',
        'icon' => 'bi-palette',
        'color' => '#8b4513'
    ],
    'alam' => [
        'title' => 'Aktivitas Alam',
        'desc' => 'Jelajahi keindahan alam dengan guide berpengalaman',
        'icon' => 'bi-tree',
        'color' => '#2d5016'
    ],
    'kuliner' => [
        'title' => 'Aktivitas Kuliner',
        'desc' => 'Nikmati cita rasa autentik kuliner nusantara',
        'icon' => 'bi-cup-hot',
        'color' => '#d2691e'
    ],
    'konservasi' => [
        'title' => 'Aktivitas Konservasi',
        'desc' => 'Ikut berkontribusi dalam pelestarian lingkungan',
        'icon' => 'bi-recycle',
        'color' => '#4a7c3c'
    ]
];

$info = $kategori_info[$kategori];
$page_title = $info['title'];
include '../layouts/header.php';
?>

<style>
    .kategori-header {
        background: linear-gradient(135deg, <?php echo $info['color']; ?> 0%, var(--secondary-green) 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }
    
    .kategori-header h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .kategori-header p {
        font-size: 1.2rem;
        opacity: 0.95;
    }
    
    .kategori-header .icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    
    .aktivitas-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 30px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .aktivitas-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    
    .aktivitas-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .aktivitas-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .aktivitas-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--primary-green);
        margin-bottom: 10px;
    }
    
    .aktivitas-komunitas {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    
    .aktivitas-komunitas i {
        color: var(--secondary-green);
    }
    
    .aktivitas-desc {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 15px;
        flex: 1;
    }
    
    .aktivitas-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }
    
    .aktivitas-lokasi {
        color: #666;
        font-size: 0.9rem;
    }
    
    .aktivitas-lokasi i {
        color: var(--secondary-green);
    }
    
    .aktivitas-harga {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--secondary-green);
    }
    
    .aktivitas-harga small {
        font-size: 0.7rem;
        font-weight: normal;
        color: #999;
    }
    
    .btn-detail {
        background-color: var(--secondary-green);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        width: 100%;
        transition: background-color 0.3s;
    }
    
    .btn-detail:hover {
        background-color: var(--primary-green);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
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

<!-- Kategori Header -->
<div class="kategori-header">
    <div class="container text-center">
        <div class="icon">
            <i class="bi <?php echo $info['icon']; ?>"></i>
        </div>
        <h1><?php echo $info['title']; ?></h1>
        <p><?php echo $info['desc']; ?></p>
    </div>
</div>

<!-- Aktivitas List -->
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-list-ul"></i> 
            Tersedia <?php echo mysqli_num_rows($result); ?> Aktivitas
        </h4>
        <a href="../index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while($aktivitas = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="aktivitas-card">
                    <?php
                    // Gunakan gambar lokal dari folder assets/images/aktivitas/
                    $img_url = get_image_path($aktivitas['foto'], 'aktivitas', $base_url);
                    ?>
                    <img src="<?php echo $img_url; ?>" class="aktivitas-img" alt="<?php echo htmlspecialchars($aktivitas['nama_aktivitas']); ?>">
                    
                    <div class="aktivitas-body">
                        <h5 class="aktivitas-title"><?php echo htmlspecialchars($aktivitas['nama_aktivitas']); ?></h5>
                        
                        <p class="aktivitas-komunitas">
                            <i class="bi bi-people"></i> <?php echo htmlspecialchars($aktivitas['nama_komunitas']); ?>
                        </p>
                        
                        <p class="aktivitas-desc">
                            <?php 
                            $desc = htmlspecialchars($aktivitas['deskripsi']);
                            echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                            ?>
                        </p>
                        
                        <div class="aktivitas-meta">
                            <span class="aktivitas-lokasi">
                                <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($aktivitas['lokasi']); ?>
                            </span>
                            <span class="aktivitas-harga">
                                Rp <?php echo number_format($aktivitas['harga'], 0, ',', '.'); ?>
                                <small>/orang</small>
                            </span>
                        </div>
                        
                        <div class="mt-3">
                            <a href="../aktivitas/detail.php?id=<?php echo $aktivitas['id_aktivitas']; ?>" class="btn btn-detail">
                                Lihat Detail <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>Belum Ada Aktivitas</h3>
            <p>Belum ada aktivitas dalam kategori ini. Silakan cek kategori lain.</p>
            <a href="../index.php" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    <?php endif; ?>
</div>

<?php 
include '../layouts/footer.php';
mysqli_close($conn); 
?>
