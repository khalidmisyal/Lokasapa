<?php
/**
 * LOKASAPA - Landing Page
 * Menampilkan kategori utama: Budaya, Alam, Kuliner, Konservasi
 */

session_start();
require_once 'config/db.php';
require_once 'config/helpers.php';

// Set base URL untuk navigasi
$base_url = '/lokasapa/';

// Ambil semua kategori dari database
$query = "SELECT * FROM kategori ORDER BY id_kategori";
$result = mysqli_query($conn, $query);

// Set page title untuk header
$page_title = "Beranda";

// Include header
include 'layouts/header.php';
?>

    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 60px;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.95;
        }
        
        .kategori-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            height: 100%;
        }
        
        .kategori-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        
        .kategori-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .kategori-body {
            padding: 25px;
        }
        
        .kategori-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-green);
            margin-bottom: 10px;
            text-transform: capitalize;
        }
        
        .kategori-desc {
            color: #666;
            font-size: 0.95rem;
        }
        
        .btn-explore {
            background-color: var(--secondary-green);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-explore:hover {
            background-color: var(--primary-green);
            color: white;
        }
        
        .section-title {
            color: var(--primary-green);
            font-weight: bold;
            margin-bottom: 40px;
            font-size: 2rem;
            text-align: center;
        }
        
        footer {
            background-color: var(--primary-green);
            color: white;
            padding: 30px 0;
            margin-top: 80px;
        }
        
        .icon-wrapper {
            width: 60px;
            height: 60px;
            background-color: var(--light-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .icon-wrapper i {
            font-size: 1.8rem;
            color: white;
        }
    </style>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1>Jelajahi Indonesia dengan Cara Berkelanjutan</h1>
            <p>Temukan pengalaman autentik bersama komunitas lokal dan dukung ekonomi mereka</p>
            <a href="#kategori" class="btn btn-light btn-lg mt-3">
                <i class="bi bi-compass"></i> Mulai Eksplorasi
            </a>
        </div>
    </div>

    <!-- Kategori Section -->
    <div class="container" id="kategori">
        <h2 class="section-title">Pilih Kategori Pengalaman</h2>
        
        <div class="row g-4">
            <?php
            // Deskripsi dan icon untuk setiap kategori
            $kategori_info = [
                'budaya' => [
                    'desc' => 'Pelajari seni, tari, dan tradisi dari komunitas lokal',
                    'icon' => 'bi-palette',
                    'img' => $base_url . 'assets/images/Budaya-home.jpg'
                ],
                'alam' => [
                    'desc' => 'Jelajahi keindahan alam dengan guide berpengalaman',
                    'icon' => 'bi-tree',
                    'img' => $base_url . 'assets/images/Alam-home.png'
                ],
                'kuliner' => [
                    'desc' => 'Nikmati cita rasa autentik kuliner nusantara',
                    'icon' => 'bi-cup-hot',
                    'img' => $base_url . 'assets/images/Kuliner-home.jpg'
                ],
                'konservasi' => [
                    'desc' => 'Ikut berkontribusi dalam pelestarian lingkungan',
                    'icon' => 'bi-recycle',
                    'img' => $base_url . 'assets/images/Konservasi-home.jpg'
                ]
            ];
            
            while($kategori = mysqli_fetch_assoc($result)):
                $nama = $kategori['nama_kategori'];
                $info = $kategori_info[$nama];
            ?>
            <div class="col-md-6 col-lg-3">
                <a href="kategori/aktivitas.php?kategori=<?php echo $nama; ?>" style="text-decoration: none;">
                    <div class="kategori-card">
                        <img src="<?php echo $info['img']; ?>" class="kategori-img" alt="<?php echo $nama; ?>">
                        <div class="kategori-body">
                            <div class="icon-wrapper">
                                <i class="bi <?php echo $info['icon']; ?>"></i>
                            </div>
                            <h3 class="kategori-title"><?php echo ucfirst($nama); ?></h3>
                            <p class="kategori-desc"><?php echo $info['desc']; ?></p>
                            <div class="text-center mt-3">
                                <button class="btn btn-explore">
                                    Jelajahi <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

<?php 
// Include footer
include 'layouts/footer.php';

// Close database connection
mysqli_close($conn); 
?>