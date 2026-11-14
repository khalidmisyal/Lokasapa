<?php
/**
 * LOKASAPA - Batalkan Pemesanan
 * CRUD: UPDATE (Mengubah status pemesanan menjadi dibatalkan)
 * 
 * Fitur:
 * - Membatalkan pemesanan
 * - Hanya bisa membatalkan jika status "Menunggu Pembayaran"
 * - Validasi kepemilikan pesanan
 */

session_start();
require_once '../config/db.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$id_pemesanan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah pemesanan ada dan milik user yang login
$query_check = "SELECT * FROM pemesanan WHERE id_pemesanan = ? AND id_user = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_pemesanan, $user_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if(mysqli_num_rows($result_check) == 0) {
    $_SESSION['error_message'] = 'Pemesanan tidak ditemukan';
    header('Location: index.php');
    exit;
}

$pesanan = mysqli_fetch_assoc($result_check);

// Cek apakah bisa dibatalkan (hanya jika status "Menunggu Pembayaran")
if($pesanan['status'] != 'Menunggu Pembayaran') {
    $_SESSION['error_message'] = 'Pemesanan tidak dapat dibatalkan karena sudah diproses';
    header('Location: detail.php?id=' . $id_pemesanan);
    exit;
}

// Proses pembatalan
$query_delete = "DELETE FROM pemesanan WHERE id_pemesanan = ?";
$stmt_delete = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_pemesanan);

if(mysqli_stmt_execute($stmt_delete)) {
    $_SESSION['success_message'] = 'Pemesanan berhasil dibatalkan';
} else {
    $_SESSION['error_message'] = 'Gagal membatalkan pemesanan';
}

mysqli_close($conn);
header('Location: index.php');
exit;
?>
