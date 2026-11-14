<?php
/**
 * LOKASAPA - Helper Functions
 * 
 * Fungsi-fungsi helper yang digunakan di seluruh aplikasi
 */

/**
 * Tampilkan flash message dari session
 * Digunakan untuk menampilkan notifikasi sukses/error
 */
function show_flash_message() {
    if(isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> ' . htmlspecialchars($_SESSION['success_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['success_message']);
    }
    
    if(isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['error_message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['error_message']);
    }
}

/**
 * Format tanggal Indonesia
 */
function format_tanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

/**
 * Format rupiah
 */
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Cek apakah user sudah login
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect jika belum login
 */
function require_login($redirect_url = '') {
    if(!is_logged_in()) {
        $redirect = !empty($redirect_url) ? '?redirect=' . urlencode($redirect_url) : '';
        header('Location: ../auth/login.php' . $redirect);
        exit;
    }
}

/**
 * Sanitize input
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Get image path with fallback to default
 * @param string $filename - Nama file gambar dari database
 * @param string $folder - Folder dalam assets/images (aktivitas/kategori/default)
 * @param string $base_url - Base URL aplikasi
 * @return string - URL gambar lengkap
 */
function get_image_path($filename, $folder = 'aktivitas', $base_url = '/lokasapa/') {
    // Jika filename kosong atau null, return default
    if(empty($filename)) {
        return $base_url . 'assets/images/default/no-image.jpg';
    }
    
    // Mapping nama file database ke nama file asli user (di root folder images/)
    $image_mapping = [
        // Aktivitas Budaya
        'jaipong.jpg' => 'Belajar Tari Jaipong-aktivitas budaya.jpg',
        'batik.jpg' => 'Membatik Tradisional-aktivitas budaya.jpg',
        'angklung.jpg' => 'Kelas Angklung-aktivitas budaya.jpeg',
        'kampung.jpg' => 'Tur Kampung Adat-aktivitas budaya.jpg',
        
        // Aktivitas Alam
        'salak.jpg' => 'Tracking Gunung Salak-aktivitas alam.jpg',
        'camping.jpg' => 'Camping Hutan Pinus-aktivitas alam.jpg',
        'sungai.jpg' => 'Susur Sungai-aktivitas alam.jpg',
        'sunrise.jpg' => 'Trekking Sunrise Hill-aktivitas alam.jpg',
        
        // Aktivitas Kuliner
        'gudeg.jpg' => 'Tour Gudeg Tradisional-aktivitas kuliner.jpg',
        'jajanan.jpg' => 'Workshop Jajanan Pasar-aktivitas kuliner.jpg',
        'streetfood.jpg' => 'Street Food Night Tour-aktivitas kuliner.jpg',
        'kopi.jpg' => 'Kopi Lokal Experience-aktivitas kuliner.jpg',
        
        // Aktivitas Konservasi
        'mangrove.jpg' => 'Tanam Mangrove-aktivitas konservasi.jpg',
        'pantai.jpg' => 'Bersih Pantai-aktivitas konservasi.jpg',
        'karang.jpg' => 'Edukasi Terumbu Karang-aktivitas konservasi.jpg',
        'urbanfarm.jpg' => 'Urban Farming-aktivitas konservasi.jpg'
    ];
    
    // Gunakan mapping jika ada
    $actual_filename = isset($image_mapping[$filename]) ? $image_mapping[$filename] : $filename;
    
    // Cari di ROOT folder images/ (bukan di subfolder)
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/lokasapa/assets/images/' . $actual_filename;
    
    // Cek apakah file exists
    if(file_exists($file_path)) {
        return $base_url . 'assets/images/' . rawurlencode($actual_filename);
    } else {
        // Jika tidak ada, return gambar placeholder
        return 'https://via.placeholder.com/400x300/7fa663/ffffff?text=No+Image';
    }
}
?>
