<?php
/**
 * LOKASAPA - Form Pemesanan Aktivitas
 * CRUD: CREATE (Insert pemesanan baru ke database)
 * 
 * Fitur:
 * - Form pemesanan dengan validasi
 * - Cek login user
 * - Kalkulasi total harga otomatis
 * - Pilihan metode pembayaran
 */

session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Set base URL
$base_url = '/lokasapa/';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=../pemesanan/create.php?id='.$_GET['id']);
    exit;
}

$id_aktivitas = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail aktivitas
$query = "SELECT a.*, k.nama_kategori, kom.nama_komunitas 
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

$error = '';
$success = false;

// Proses form pemesanan
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah_orang = intval($_POST['jumlah_orang']);
    $tgl_kunjungan = $_POST['tgl_kunjungan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $id_user = $_SESSION['user_id'];
    $tgl_pesan = date('Y-m-d');
    $total_harga = $jumlah_orang * $aktivitas['harga'];
    
    // Validasi
    if($jumlah_orang < 1) {
        $error = 'Jumlah orang minimal 1';
    } elseif($jumlah_orang > $aktivitas['kuota']) {
        $error = 'Jumlah orang melebihi kuota tersedia';
    } elseif(strtotime($tgl_kunjungan) < strtotime('today')) {
        $error = 'Tanggal kunjungan tidak valid';
    } elseif(empty($metode_pembayaran)) {
        $error = 'Pilih metode pembayaran';
    } else {
        // Insert pemesanan
        $query_insert = "INSERT INTO pemesanan (id_user, id_aktivitas, tgl_pesan, tgl_kunjungan, jumlah_orang, metode_pembayaran, total_harga, status) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 'Menunggu Pembayaran')";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "iissisi", $id_user, $id_aktivitas, $tgl_pesan, $tgl_kunjungan, $jumlah_orang, $metode_pembayaran, $total_harga);
        
        if(mysqli_stmt_execute($stmt_insert)) {
            $id_pemesanan = mysqli_insert_id($conn);
            $success = true;
        } else {
            $error = 'Terjadi kesalahan, silakan coba lagi';
        }
    }
}

$page_title = "Pemesanan Aktivitas";
include '../layouts/header.php';
?>

    <!-- Content -->
    <main>
    <div class="container">
        <h2 class="page-title"><i class="bi bi-calendar-check"></i> Pemesanan Aktivitas</h2>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informasi Aktivitas -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i> Informasi Aktivitas
                    </div>
                    <div class="card-body">
                        <div class="aktivitas-info">
                            <img src="../assets/images/<?php echo $aktivitas['foto']; ?>" 
                                 class="aktivitas-img" 
                                 alt="<?php echo $aktivitas['nama_aktivitas']; ?>"
                                 onerror="this.src='https://via.placeholder.com/120'">
                            <div class="aktivitas-details">
                                <h5><?php echo $aktivitas['nama_aktivitas']; ?></h5>
                                <p><i class="bi bi-tag"></i> <?php echo ucfirst($aktivitas['nama_kategori']); ?></p>
                                <p><i class="bi bi-geo-alt"></i> <?php echo $aktivitas['lokasi']; ?></p>
                                <p><i class="bi bi-people"></i> <?php echo $aktivitas['nama_komunitas']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Pemesanan -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-clipboard-check"></i> Detail Pemesanan
                    </div>
                    <div class="card-body">
                        <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="formPemesanan">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Orang</label>
                                <input type="number" name="jumlah_orang" id="jumlah_orang" 
                                       class="form-control" min="1" max="<?php echo $aktivitas['kuota']; ?>" 
                                       value="1" required>
                                <small class="text-muted">Maksimal: <?php echo $aktivitas['kuota']; ?> orang</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kunjungan</label>
                                <input type="date" name="tgl_kunjungan" class="form-control" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <div>
                                    <label class="payment-option">
                                        <input type="radio" name="metode_pembayaran" value="QRIS" required>
                                        <i class="bi bi-qr-code"></i> QRIS
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="metode_pembayaran" value="GoPay">
                                        <i class="bi bi-wallet2"></i> GoPay
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="metode_pembayaran" value="OVO">
                                        <i class="bi bi-wallet2"></i> OVO
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="metode_pembayaran" value="Dana">
                                        <i class="bi bi-wallet2"></i> Dana
                                    </label>
                                    <label class="payment-option">
                                        <input type="radio" name="metode_pembayaran" value="Transfer">
                                        <i class="bi bi-bank"></i> Transfer Bank
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="col-lg-4">
                <div class="total-box">
                    <div class="label">Total Pembayaran</div>
                    <div class="amount" id="totalAmount">
                        Rp <?php echo number_format($aktivitas['harga'], 0, ',', '.'); ?>
                    </div>
                    <div class="label">
                        <span id="jumlahText">1</span> orang × Rp <?php echo number_format($aktivitas['harga'], 0, ',', '.'); ?>
                    </div>
                </div>
                
                <button type="submit" form="formPemesanan" class="btn btn-pesan">
                    <i class="bi bi-check-circle"></i> Konfirmasi Pemesanan
                </button>
                
                <div class="mt-3 text-center">
                    <a href="../detail/aktivitas.php?id=<?php echo $id_aktivitas; ?>" class="text-muted">
                        <i class="bi bi-arrow-left"></i> Kembali ke Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="success-icon mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h4 class="mb-3" style="color: var(--primary-green);">Pemesanan Berhasil!</h4>
                    <p class="text-muted mb-4">
                        Terima kasih telah memesan. Silakan lakukan pembayaran untuk mengkonfirmasi pesanan Anda.
                    </p>
                    <a href="../pemesanan/index.php" class="btn btn-pesan" style="width: auto; padding: 12px 40px;">
                        Lihat Pesanan Saya
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-2">&copy; 2025 LOKASAPA - Platform Wisata Berkelanjutan Indonesia</p>
            <p class="mb-0">
                <small>Menghubungkan wisatawan dengan komunitas lokal</small>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const hargaPerOrang = <?php echo $aktivitas['harga']; ?>;
        const jumlahInput = document.getElementById('jumlah_orang');
        const totalAmount = document.getElementById('totalAmount');
        const jumlahText = document.getElementById('jumlahText');
        
        jumlahInput.addEventListener('input', function() {
            const jumlah = parseInt(this.value) || 1;
            const total = jumlah * hargaPerOrang;
            totalAmount.textContent = 'Rp ' + total.toLocaleString('id-ID');
            jumlahText.textContent = jumlah;
        });
        
        // Handle payment option selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
        
        // Show success modal if needed
        <?php if($success): ?>
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        <?php endif; ?>
    </script>

<?php 
include '../layouts/footer.php';
mysqli_stmt_close($stmt);
mysqli_close($conn); 
?>