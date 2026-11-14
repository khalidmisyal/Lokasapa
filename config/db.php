<?php

// Konfigurasi Database
$host = "localhost";        // Host database (biasanya localhost)
$username = "root";         // Username MySQL (default: root)
$password = "";             // Password MySQL (default: kosong)
$database = "lokasapa";     // Nama database

// Membuat koneksi ke database
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if(!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset UTF-8 untuk mendukung karakter Indonesia
mysqli_set_charset($conn, "utf8mb4");

?>