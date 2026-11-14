# ✅ CHECKLIST PRESENTASI PROYEK LOKASAPA

## 📋 PERSIAPAN SEBELUM PRESENTASI

### Setup Database
- [ ] Import `lokasapa.sql` ke phpMyAdmin
- [ ] Cek semua tabel sudah terisi data sample
- [ ] Test koneksi database di `config/db.php`
- [ ] Jalankan `database_testing.sql` untuk data tambahan (opsional)

### Test Fitur
- [ ] Test login dengan akun: `ariel@mail.com` / `123456`
- [ ] Test registrasi user baru
- [ ] Test semua halaman tidak error
- [ ] Test CRUD setiap bagian

---

## 👥 PEMBAGIAN DEMO PRESENTASI

### 🎤 ORANG 1: AUTH & USER (5 menit)

**Yang Harus Dijelaskan:**
- [ ] Konsep autentikasi user
- [ ] Alur registrasi (CREATE)
- [ ] Validasi email unik
- [ ] Alur login & session management

**Demo Langsung:**
1. [ ] Buka `auth/register.php`
2. [ ] Registrasi user baru (tunjukkan validasi)
3. [ ] Auto login setelah registrasi
4. [ ] Lihat profil di `user/profile.php` (READ)
5. [ ] Edit profil di `user/edit.php` (UPDATE)
6. [ ] Tunjukkan data berubah di database

**Query SQL yang Dipakai:**
```sql
-- CREATE (register.php)
INSERT INTO user (nama, email, password, domisili, no_hp) VALUES (?, ?, ?, ?, ?)

-- READ (profile.php)
SELECT * FROM user WHERE id_user = ?

-- UPDATE (edit.php)
UPDATE user SET nama = ?, email = ?, domisili = ?, no_hp = ? WHERE id_user = ?
```

**Pertanyaan yang Mungkin Ditanya:**
- Bagaimana validasi email unik?
- Kenapa password disimpan plain text? (Jawab: untuk keperluan testing, di production harus pakai password_hash)
- Apa fungsi session?

---

### 🎤 ORANG 2: KATEGORI & AKTIVITAS (5 menit)

**Yang Harus Dijelaskan:**
- [ ] Konsep kategori aktivitas (enum)
- [ ] Relasi antara aktivitas, kategori, dan komunitas
- [ ] JOIN query multiple table

**Demo Langsung:**
1. [ ] Buka beranda `index.php`
2. [ ] Klik salah satu kategori (misal: Budaya)
3. [ ] Masuk ke `kategori/aktivitas.php` (READ list aktivitas)
4. [ ] Klik "Lihat Detail" salah satu aktivitas
5. [ ] Masuk ke `aktivitas/detail.php` (READ detail lengkap)
6. [ ] Tunjukkan informasi komunitas dan ulasan

**Query SQL yang Dipakai:**
```sql
-- READ list aktivitas per kategori
SELECT a.*, k.nama_kategori, kom.nama_komunitas 
FROM aktivitas a
JOIN kategori k ON a.id_kategori = k.id_kategori
JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
WHERE k.nama_kategori = ?

-- READ detail aktivitas + ulasan
SELECT a.*, k.nama_kategori, kom.* 
FROM aktivitas a
JOIN kategori k ON a.id_kategori = k.id_kategori
JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
WHERE a.id_aktivitas = ?

-- Rating rata-rata
SELECT AVG(rating) as avg_rating, COUNT(*) as total_ulasan 
FROM ulasan WHERE id_aktivitas = ?
```

**Pertanyaan yang Mungkin Ditanya:**
- Kenapa pakai ENUM untuk kategori?
- Bagaimana JOIN bekerja?
- Bagaimana hitung rating rata-rata?

---

### 🎤 ORANG 3: PEMESANAN (5 menit)

**Yang Harus Dijelaskan:**
- [ ] Alur pemesanan dari awal sampai akhir
- [ ] Validasi kuota dan tanggal
- [ ] Status pemesanan (Menunggu, Diproses, Selesai)
- [ ] Kapan bisa membatalkan pesanan

**Demo Langsung:**
1. [ ] Login dulu (jika belum)
2. [ ] Dari detail aktivitas, klik "Pesan Sekarang"
3. [ ] Isi form di `pemesanan/create.php` (CREATE)
4. [ ] Tunjukkan validasi (coba jumlah > kuota, tanggal kemarin)
5. [ ] Submit pesanan berhasil
6. [ ] Lihat riwayat di `pemesanan/index.php` (READ list)
7. [ ] Klik detail pesanan di `pemesanan/detail.php` (READ detail)
8. [ ] Demo batalkan pesanan di `pemesanan/cancel.php` (DELETE)

**Query SQL yang Dipakai:**
```sql
-- CREATE (create.php)
INSERT INTO pemesanan (id_user, id_aktivitas, tgl_pesan, tgl_kunjungan, 
                       jumlah_orang, metode_pembayaran, total_harga, status) 
VALUES (?, ?, ?, ?, ?, ?, ?, 'Menunggu Pembayaran')

-- READ list (index.php)
SELECT p.*, a.nama_aktivitas, a.lokasi
FROM pemesanan p
JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
WHERE p.id_user = ?

-- READ detail (detail.php)
SELECT p.*, a.*, k.nama_kategori, kom.nama_komunitas
FROM pemesanan p
JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
WHERE p.id_pemesanan = ? AND p.id_user = ?

-- DELETE (cancel.php)
DELETE FROM pemesanan WHERE id_pemesanan = ?
```

**Pertanyaan yang Mungkin Ditanya:**
- Bagaimana validasi kuota?
- Kenapa pakai DELETE bukan UPDATE status?
- Bagaimana kalkulasi total harga?

---

### 🎤 ORANG 4: ULASAN (5 menit)

**Yang Harus Dijelaskan:**
- [ ] Syarat bisa memberi ulasan (sudah pesan & selesai)
- [ ] Prevent duplikasi ulasan
- [ ] Sistem rating bintang 1-5
- [ ] Hak edit/delete hanya untuk pemilik ulasan

**Demo Langsung:**
1. [ ] Dari pesanan dengan status "Selesai", klik "Beri Ulasan"
2. [ ] Isi form di `ulasan/create.php` (CREATE)
3. [ ] Submit ulasan
4. [ ] Lihat ulasan muncul di halaman detail aktivitas
5. [ ] Klik "Edit Ulasan Saya" di `ulasan/edit.php` (UPDATE)
6. [ ] Ubah rating atau komentar
7. [ ] Demo hapus ulasan di `ulasan/delete.php` (DELETE)

**Query SQL yang Dipakai:**
```sql
-- CREATE (create.php)
-- Validasi sudah pernah pesan & selesai
SELECT p.* FROM pemesanan p
WHERE p.id_pemesanan = ? AND p.id_user = ? AND p.status = 'Selesai'

-- Cek duplikasi
SELECT id_ulasan FROM ulasan WHERE id_user = ? AND id_aktivitas = ?

-- Insert ulasan
INSERT INTO ulasan (id_user, id_aktivitas, rating, komentar) VALUES (?, ?, ?, ?)

-- UPDATE (edit.php)
UPDATE ulasan SET rating = ?, komentar = ? WHERE id_ulasan = ?

-- DELETE (delete.php)
DELETE FROM ulasan WHERE id_ulasan = ?
```

**Pertanyaan yang Mungkin Ditanya:**
- Bagaimana prevent duplikasi ulasan?
- Kenapa user lain tidak bisa edit ulasan orang lain?
- Bagaimana sistem rating bekerja?

---

## 🎯 TIPS PRESENTASI

### DO ✅
- [ ] Bicara jelas dan percaya diri
- [ ] Tunjukkan kode dan database secara bergantian
- [ ] Jelaskan logika bisnis, bukan cuma kode
- [ ] Siapkan backup jika demo gagal
- [ ] Timing: masing-masing 5 menit, total 20 menit + QA 10 menit

### DON'T ❌
- [ ] Jangan baca kode baris per baris
- [ ] Jangan terlalu cepat saat demo
- [ ] Jangan panik jika ada error
- [ ] Jangan lupa logout/login untuk demo user berbeda

---

## 📊 PERTANYAAN UMUM YANG SERING DITANYA

**1. Kenapa pakai PHP Native, bukan framework?**
→ Karena requirement tugas Sistem Basis Data fokus ke query SQL dan CRUD, bukan framework

**2. Kenapa password tidak di-hash?**
→ Untuk kemudahan testing. Di production harus pakai `password_hash()` dan `password_verify()`

**3. Bagaimana relasi antar tabel?**
→ Tunjukkan ERD dari `lokasapa.sql` dan jelaskan FK

**4. Apakah ada fitur tambahan?**
→ Ada sistem notifikasi (flash message), validasi form, responsive design dengan Bootstrap

**5. Siapa yang mengerjakan apa?**
→ Tunjukkan README.md dan jelaskan pembagian tugas

---

## 🚨 TROUBLESHOOTING

**Error: Undefined variable $base_url**
→ Tambahkan `$base_url = '/lokasapa/';` di setiap file

**Error: Call to undefined function mysqli_connect()**
→ Aktifkan extension mysqli di php.ini

**Error: Access denied for user 'root'@'localhost'**
→ Cek kredensial di `config/db.php`

**Halaman blank/error 500**
→ Cek `error_log` di XAMPP atau aktifkan `display_errors` di php.ini

**CSS/Bootstrap tidak muncul**
→ Cek koneksi internet (pakai CDN)

---

## 📝 CATATAN AKHIR

- Backup database sebelum presentasi
- Test semua fitur 1 jam sebelum presentasi
- Siapkan jawaban untuk pertanyaan di atas
- Bawa laptop sendiri atau USB cadangan
- Screenshot hasil demo jika proyektor error

**SEMANGAT! 🚀**
