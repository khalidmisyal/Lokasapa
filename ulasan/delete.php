<?php
/**
 * LOKASAPA - Hapus Ulasan
 * 
 * BAGIAN ORANG 4 - ULASAN
 * CRUD: DELETE (Menghapus ulasan dari database)
 * 
 * Fitur:
 * - Hapus ulasan
 * - Validasi kepemilikan (hanya pemilik yang bisa hapus)
 * - Redirect setelah berhasil
 */

session_start();
require_once '../config/db.php';
require_once '../config/helpers.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id_ulasan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah ulasan ada dan milik user yang login
$query_check = "SELECT id_aktivitas FROM ulasan WHERE id_ulasan = ? AND id_user = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_ulasan, $user_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if(mysqli_num_rows($result_check) == 0) {
    $_SESSION['error_message'] = 'Ulasan tidak ditemukan atau Anda tidak memiliki akses';
    header('Location: ../pemesanan/index.php');
    exit;
}

$ulasan_data = mysqli_fetch_assoc($result_check);
$id_aktivitas = $ulasan_data['id_aktivitas'];

// Hapus ulasan
$query_delete = "DELETE FROM ulasan WHERE id_ulasan = ?";
$stmt_delete = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_ulasan);

if(mysqli_stmt_execute($stmt_delete)) {
    $_SESSION['success_message'] = 'Ulasan berhasil dihapus';
} else {
    $_SESSION['error_message'] = 'Gagal menghapus ulasan';
}

mysqli_close($conn);

// Redirect ke halaman detail aktivitas
header('Location: ../aktivitas/detail.php?id=' . $id_aktivitas);
exit;
?>
