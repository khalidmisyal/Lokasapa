-- =====================================================
-- LOKASAPA - Script Testing & Data Tambahan
-- =====================================================
-- Gunakan script ini untuk testing fitur-fitur aplikasi

-- 1. Update status pemesanan untuk testing ulasan
-- (Ubah status menjadi "Selesai" agar bisa beri ulasan)
UPDATE pemesanan SET status = 'Selesai' WHERE id_pemesanan = 1;
UPDATE pemesanan SET status = 'Selesai' WHERE id_pemesanan = 2;

-- 2. Tambah user testing baru
INSERT INTO user (nama, email, password, domisili, no_hp) VALUES
('Testing User', 'test@mail.com', '123456', 'Jakarta', '081234567899'),
('Demo User', 'demo@mail.com', '123456', 'Surabaya', '081298765433');

-- 3. Tambah pemesanan testing untuk user baru
INSERT INTO pemesanan (id_user, id_aktivitas, tgl_pesan, tgl_kunjungan, jumlah_orang, metode_pembayaran, status, total_harga) VALUES
(4, 3, '2025-01-10', '2025-01-25', 2, 'Transfer Bank', 'Menunggu Pembayaran', 200000),
(5, 7, '2025-01-11', '2025-01-28', 4, 'E-Wallet', 'Diproses', 520000);

-- 4. Query untuk cek data
-- Cek semua pemesanan
SELECT p.*, u.nama, a.nama_aktivitas 
FROM pemesanan p
JOIN user u ON p.id_user = u.id_user
JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
ORDER BY p.tgl_pesan DESC;

-- Cek semua ulasan
SELECT u.*, us.nama, a.nama_aktivitas 
FROM ulasan u
JOIN user us ON u.id_user = us.id_user
JOIN aktivitas a ON u.id_aktivitas = a.id_aktivitas
ORDER BY u.id_ulasan DESC;

-- 5. Reset data untuk testing ulang
-- HATI-HATI: Ini akan menghapus semua data testing!
-- DELETE FROM ulasan WHERE id_ulasan > 2;
-- DELETE FROM pemesanan WHERE id_pemesanan > 3;
-- DELETE FROM user WHERE id_user > 3;

-- 6. Query statistik untuk dashboard (future feature)
-- Total pemesanan per user
SELECT u.nama, COUNT(p.id_pemesanan) as total_pemesanan, SUM(p.total_harga) as total_belanja
FROM user u
LEFT JOIN pemesanan p ON u.id_user = p.id_user
GROUP BY u.id_user;

-- Aktivitas paling populer
SELECT a.nama_aktivitas, COUNT(p.id_pemesanan) as total_pemesanan
FROM aktivitas a
LEFT JOIN pemesanan p ON a.id_aktivitas = p.id_aktivitas
GROUP BY a.id_aktivitas
ORDER BY total_pemesanan DESC;

-- Rating rata-rata per aktivitas
SELECT a.nama_aktivitas, 
       AVG(u.rating) as avg_rating, 
       COUNT(u.id_ulasan) as total_ulasan
FROM aktivitas a
LEFT JOIN ulasan u ON a.id_aktivitas = u.id_aktivitas
GROUP BY a.id_aktivitas
ORDER BY avg_rating DESC;
