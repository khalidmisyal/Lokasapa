# CATATAN CRUD PER MODUL - LOKASAPA
## Panduan Menjawab Pertanyaan Dosen tentang CRUD

---

## 📁 MODUL 1: AUTH (Autentikasi)
**File:** `auth/register.php`, `auth/login.php`, `auth/logout.php`

### CRUD Operations:
#### ✅ CREATE - Register User Baru
**File:** `auth/register.php`
- **Query:** `INSERT INTO user (nama_lengkap, email, password, no_telepon, tanggal_lahir, alamat, tanggal_daftar)`
- **Fungsi:** Mendaftarkan user baru ke sistem
- **Validasi:**
  - Cek email sudah terdaftar atau belum
  - Semua field wajib diisi
  - Email harus format valid
- **Fitur Tambahan:**
  - Auto-login setelah register berhasil
  - Session langsung dibuat
  - Redirect ke halaman home

#### ✅ READ - Login (Verifikasi User)
**File:** `auth/login.php`
- **Query:** `SELECT * FROM user WHERE email = ?`
- **Fungsi:** Membaca data user untuk verifikasi login
- **Proses:**
  - Cek email ada di database
  - Validasi password (untuk production pakai password_verify)
  - Buat session jika berhasil

#### ✅ DELETE - Logout (Hapus Session)
**File:** `auth/logout.php`
- **Fungsi:** Menghapus session user (logout)
- **Proses:**
  - `session_destroy()` untuk hapus semua session
  - Redirect ke login page

---

## 📁 MODUL 2: USER (Manajemen Profil User)
**File:** `user/profile.php`, `user/edit.php`

### CRUD Operations:
#### ✅ READ - Lihat Profil User
**File:** `user/profile.php`
- **Query:**
  ```sql
  SELECT * FROM user WHERE id_user = ?
  ```
- **Query Statistik Pemesanan:**
  ```sql
  SELECT COUNT(*) as total FROM pemesanan WHERE id_user = ?
  ```
- **Query Statistik Ulasan:**
  ```sql
  SELECT COUNT(*) as total FROM ulasan WHERE id_user = ?
  ```
- **Fungsi:** Menampilkan profil lengkap user dengan statistik
- **Data yang ditampilkan:**
  - Nama lengkap, email, nomor telepon
  - Tanggal lahir, alamat
  - Total pemesanan yang pernah dibuat
  - Total ulasan yang pernah ditulis

#### ✅ UPDATE - Edit Profil User
**File:** `user/edit.php`
- **Query Baca Data Lama:**
  ```sql
  SELECT * FROM user WHERE id_user = ?
  ```
- **Query Update (tanpa password):**
  ```sql
  UPDATE user SET nama_lengkap = ?, email = ?, no_telepon = ?, 
                  tanggal_lahir = ?, alamat = ? 
  WHERE id_user = ?
  ```
- **Query Update (dengan password):**
  ```sql
  UPDATE user SET nama_lengkap = ?, email = ?, password = ?, 
                  no_telepon = ?, tanggal_lahir = ?, alamat = ? 
  WHERE id_user = ?
  ```
- **Fungsi:** Mengubah data profil user
- **Validasi:**
  - Cek email baru tidak dipakai user lain
  - Password bersifat opsional (kalau kosong tidak diubah)
  - Semua field lain wajib diisi

---

## 📁 MODUL 3: KATEGORI (Browse by Category)
**File:** `kategori/aktivitas.php`

### CRUD Operations:
#### ✅ READ - Lihat Aktivitas per Kategori
**File:** `kategori/aktivitas.php`
- **Query:**
  ```sql
  SELECT a.*, k.nama_kategori, kom.nama_komunitas 
  FROM aktivitas a
  JOIN kategori k ON a.id_kategori = k.id_kategori
  JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
  WHERE k.nama_kategori = ?
  ORDER BY a.tanggal_dibuat DESC
  ```
- **Fungsi:** Menampilkan daftar aktivitas berdasarkan kategori tertentu
- **Kategori yang ada:** Budaya, Alam, Kuliner, Konservasi
- **Data yang ditampilkan:**
  - Nama aktivitas, deskripsi singkat
  - Harga per orang
  - Nama komunitas penyelenggara
  - Durasi aktivitas
  - Lokasi

**Catatan:** Ini modul READ-ONLY karena user tidak bisa menambah/edit/hapus kategori

---

## 📁 MODUL 4: AKTIVITAS (Detail Aktivitas)
**File:** `aktivitas/detail.php`

### CRUD Operations:
#### ✅ READ - Lihat Detail Aktivitas Lengkap
**File:** `aktivitas/detail.php`
- **Query Detail Aktivitas:**
  ```sql
  SELECT a.*, k.nama_kategori, 
         kom.nama_komunitas, kom.tipe_komunitas, kom.deskripsi as deskripsi_komunitas
  FROM aktivitas a
  JOIN kategori k ON a.id_kategori = k.id_kategori
  JOIN komunitas kom ON a.id_komunitas = kom.id_komunitas
  WHERE a.id_aktivitas = ?
  ```
- **Query Rating Rata-rata:**
  ```sql
  SELECT AVG(rating) as avg_rating, COUNT(*) as total_ulasan
  FROM ulasan
  WHERE id_aktivitas = ?
  ```
- **Query Daftar Ulasan:**
  ```sql
  SELECT u.*, us.nama_lengkap, us.email
  FROM ulasan u
  JOIN user us ON u.id_user = us.id_user
  WHERE u.id_aktivitas = ?
  ORDER BY u.tanggal_ulasan DESC
  ```
- **Fungsi:** Menampilkan detail lengkap aktivitas wisata
- **Data yang ditampilkan:**
  - Detail aktivitas (nama, deskripsi, harga, durasi, kuota)
  - Informasi komunitas penyelenggara
  - Rating rata-rata (bintang)
  - Daftar ulasan dari user lain
  - Tombol "Pesan Sekarang"
  - Tombol "Edit Ulasan" (jika user pernah review)

**Catatan:** Modul ini READ-ONLY, tapi menjadi halaman krusial yang menghubungkan:
- User → Aktivitas → Pemesanan
- User → Aktivitas → Ulasan

---

## 📁 MODUL 5: PEMESANAN (Booking System)
**File:** `pemesanan/create.php`, `pemesanan/index.php`, `pemesanan/detail.php`, `pemesanan/cancel.php`

### CRUD Operations:
#### ✅ CREATE - Buat Pemesanan Baru
**File:** `pemesanan/create.php`
- **Query Baca Data Aktivitas:**
  ```sql
  SELECT a.*, k.nama_kategori 
  FROM aktivitas a
  JOIN kategori k ON a.id_kategori = k.id_kategori
  WHERE a.id_aktivitas = ?
  ```
- **Query Insert Pemesanan:**
  ```sql
  INSERT INTO pemesanan (id_user, id_aktivitas, tanggal_booking, 
                         jumlah_orang, total_harga, metode_pembayaran, status)
  VALUES (?, ?, ?, ?, ?, ?, 'Menunggu Pembayaran')
  ```
- **Fungsi:** Membuat pemesanan/booking aktivitas wisata
- **Validasi:**
  - User harus login
  - Tanggal booking tidak boleh di masa lalu
  - Jumlah orang tidak boleh melebihi kuota
  - Total harga otomatis dihitung (harga × jumlah orang)
- **Data Input:**
  - Tanggal booking, jumlah orang
  - Metode pembayaran (Transfer Bank, E-Wallet, Cash)
  - Status otomatis: "Menunggu Pembayaran"

#### ✅ READ - Lihat Daftar Pemesanan (History)
**File:** `pemesanan/index.php`
- **Query:**
  ```sql
  SELECT p.*, a.nama_aktivitas, a.lokasi, a.harga_per_orang
  FROM pemesanan p
  JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
  WHERE p.id_user = ?
  ORDER BY p.tanggal_booking DESC
  ```
- **Query Cek Ulasan:**
  ```sql
  SELECT id_ulasan FROM ulasan 
  WHERE id_pemesanan = ? AND id_user = ?
  ```
- **Fungsi:** Menampilkan riwayat pemesanan user
- **Data yang ditampilkan:**
  - Nama aktivitas, tanggal booking
  - Jumlah orang, total harga
  - Status pemesanan (badge warna)
  - Tombol "Beri Ulasan" (jika status Selesai & belum review)
  - Link ke detail pemesanan

#### ✅ READ - Lihat Detail 1 Pemesanan
**File:** `pemesanan/detail.php`
- **Query:**
  ```sql
  SELECT p.*, a.nama_aktivitas, a.deskripsi, a.lokasi, 
         a.harga_per_orang, a.durasi, k.nama_kategori
  FROM pemesanan p
  JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
  JOIN kategori k ON a.id_kategori = k.id_kategori
  WHERE p.id_pemesanan = ? AND p.id_user = ?
  ```
- **Fungsi:** Menampilkan detail lengkap 1 pemesanan
- **Data yang ditampilkan:**
  - Info pemesanan (tanggal, jumlah orang, total harga)
  - Detail aktivitas yang dipesan
  - Metode pembayaran
  - Status pemesanan
  - Tombol "Batalkan" (jika status masih Menunggu Pembayaran)

#### ✅ DELETE - Batalkan Pemesanan
**File:** `pemesanan/cancel.php`
- **Query Validasi:**
  ```sql
  SELECT status FROM pemesanan 
  WHERE id_pemesanan = ? AND id_user = ?
  ```
- **Query Delete:**
  ```sql
  DELETE FROM pemesanan 
  WHERE id_pemesanan = ? AND id_user = ?
  ```
- **Fungsi:** Membatalkan/menghapus pemesanan
- **Validasi:**
  - Hanya bisa batalkan jika status "Menunggu Pembayaran"
  - User harus pemilik pemesanan
  - Tidak bisa batalkan jika sudah "Diproses" atau "Selesai"

---

## 📁 MODUL 6: ULASAN (Review System)
**File:** `ulasan/create.php`, `ulasan/edit.php`, `ulasan/delete.php`

### CRUD Operations:
#### ✅ CREATE - Buat Ulasan/Review Baru
**File:** `ulasan/create.php`
- **Query Validasi Pemesanan:**
  ```sql
  SELECT p.*, a.nama_aktivitas 
  FROM pemesanan p
  JOIN aktivitas a ON p.id_aktivitas = a.id_aktivitas
  WHERE p.id_pemesanan = ? AND p.id_user = ? AND p.status = 'Selesai'
  ```
- **Query Cek Duplikat:**
  ```sql
  SELECT id_ulasan FROM ulasan 
  WHERE id_pemesanan = ? AND id_user = ?
  ```
- **Query Insert Ulasan:**
  ```sql
  INSERT INTO ulasan (id_user, id_aktivitas, id_pemesanan, rating, komentar)
  VALUES (?, ?, ?, ?, ?)
  ```
- **Fungsi:** Membuat review/ulasan untuk aktivitas yang sudah selesai
- **Validasi:**
  - Pemesanan harus berstatus "Selesai"
  - User belum pernah beri ulasan untuk pemesanan ini
  - Rating wajib (1-5 bintang)
  - Komentar wajib diisi
- **Data Input:**
  - Rating (1-5 bintang dengan radio button)
  - Komentar tekstual

#### ✅ UPDATE - Edit Ulasan
**File:** `ulasan/edit.php`
- **Query Baca Data Lama:**
  ```sql
  SELECT u.*, a.nama_aktivitas 
  FROM ulasan u
  JOIN aktivitas a ON u.id_aktivitas = a.id_aktivitas
  WHERE u.id_ulasan = ? AND u.id_user = ?
  ```
- **Query Update:**
  ```sql
  UPDATE ulasan SET rating = ?, komentar = ? 
  WHERE id_ulasan = ? AND id_user = ?
  ```
- **Fungsi:** Mengubah rating atau komentar ulasan yang sudah dibuat
- **Validasi:**
  - User harus pemilik ulasan
  - Rating dan komentar wajib diisi
- **Fitur:**
  - Form pre-filled dengan data lama
  - Link ke halaman delete
  - Redirect ke detail aktivitas setelah berhasil

#### ✅ DELETE - Hapus Ulasan
**File:** `ulasan/delete.php`
- **Query Delete:**
  ```sql
  DELETE FROM ulasan 
  WHERE id_ulasan = ? AND id_user = ?
  ```
- **Fungsi:** Menghapus ulasan yang sudah dibuat
- **Validasi:**
  - User harus pemilik ulasan
  - Konfirmasi sebelum hapus
- **Proses:**
  - Hapus dari database
  - Redirect ke detail aktivitas
  - Flash message "Ulasan berhasil dihapus"

---

## 📊 RINGKASAN PEMBAGIAN CRUD PER ORANG

### ORANG 1: AUTH + USER
**Total: 3 CRUD Operations**
- CREATE: Register user baru (`auth/register.php`)
- READ: Lihat profil user + statistik (`user/profile.php`)
- UPDATE: Edit profil user (`user/edit.php`)

**Pertanyaan yang Mungkin Ditanya:**
1. "Bagaimana validasi email unique saat register?"
   - Jawab: Pakai `SELECT COUNT(*) FROM user WHERE email = ?` sebelum INSERT
   
2. "Kenapa password tidak di-hash?"
   - Jawab: Untuk testing/demo saja. Production harus pakai `password_hash()` dan `password_verify()`
   
3. "Apa perbedaan update dengan password dan tanpa password?"
   - Jawab: Ada pengecekan `if (!empty($_POST['password']))` untuk update conditional

---

### ORANG 2: KATEGORI + AKTIVITAS
**Total: 2 READ Operations (fokus JOIN complex)**
- READ: List aktivitas per kategori (`kategori/aktivitas.php`)
- READ: Detail aktivitas + rating + ulasan (`aktivitas/detail.php`)

**Pertanyaan yang Mungkin Ditanya:**
1. "Kenapa tidak ada CREATE/UPDATE/DELETE?"
   - Jawab: Kategori dan Aktivitas dikelola admin (scope project hanya user-facing)
   
2. "Bagaimana menampilkan rating rata-rata?"
   - Jawab: Pakai `SELECT AVG(rating) FROM ulasan WHERE id_aktivitas = ?`
   
3. "Apa kegunaan JOIN dengan komunitas?"
   - Jawab: Menampilkan informasi penyelenggara aktivitas (nama komunitas, tipe)

---

### ORANG 3: PEMESANAN (FULL CRUD)
**Total: 4 CRUD Operations**
- CREATE: Buat pemesanan baru (`pemesanan/create.php`)
- READ: List semua pemesanan user (`pemesanan/index.php`)
- READ: Detail 1 pemesanan (`pemesanan/detail.php`)
- DELETE: Batalkan pemesanan (`pemesanan/cancel.php`)

**Pertanyaan yang Mungkin Ditanya:**
1. "Kenapa tidak ada UPDATE pemesanan?"
   - Jawab: Untuk integritas data. User hanya bisa batalkan, tidak edit. Kalau mau ubah, batalkan lalu buat baru
   
2. "Bagaimana validasi kuota aktivitas?"
   - Jawab: `if ($jumlah_orang > $aktivitas['kuota_peserta'])` sebelum INSERT
   
3. "Status apa saja yang ada?"
   - Jawab: "Menunggu Pembayaran", "Diproses", "Selesai"

---

### ORANG 4: ULASAN (FULL CRUD kecuali READ)
**Total: 3 CRUD Operations**
- CREATE: Buat ulasan baru (`ulasan/create.php`)
- UPDATE: Edit ulasan (`ulasan/edit.php`)
- DELETE: Hapus ulasan (`ulasan/delete.php`)

**Pertanyaan yang Mungkin Ditanya:**
1. "Kenapa harus status Selesai dulu baru bisa review?"
   - Jawab: Validasi bisnis logic - user harus sudah mengikuti aktivitas
   
2. "Bagaimana mencegah duplikat ulasan?"
   - Jawab: `SELECT FROM ulasan WHERE id_pemesanan = ? AND id_user = ?` sebelum INSERT
   
3. "Kenapa ada id_pemesanan di tabel ulasan?"
   - Jawab: Untuk track ulasan dari pemesanan mana + mencegah review berkali-kali untuk 1 pemesanan

---

## 🔗 ALUR LENGKAP USER FLOW

```
1. Register/Login (MODUL AUTH) 
   ↓
2. Browse Kategori (MODUL KATEGORI) 
   ↓
3. Lihat Detail Aktivitas (MODUL AKTIVITAS) 
   ↓
4. Buat Pemesanan (MODUL PEMESANAN - CREATE) 
   ↓
5. Cek Riwayat Pemesanan (MODUL PEMESANAN - READ) 
   ↓
6. Beri Ulasan setelah Selesai (MODUL ULASAN - CREATE)
   ↓
7. Edit/Hapus Ulasan jika perlu (MODUL ULASAN - UPDATE/DELETE)
```

---

## 💡 TIPS MENJAWAB DOSEN

### Jika ditanya: "Kenapa ada modul yang tidak full CRUD?"
**Jawab:** 
- Project ini fokus user-facing features
- Admin panel untuk kelola Kategori/Aktivitas di luar scope
- Kami fokus ke user experience: register, booking, review

### Jika ditanya: "Apa bedanya READ di user/profile.php dan pemesanan/index.php?"
**Jawab:**
- `user/profile.php`: Baca data user + agregasi statistik (COUNT pemesanan, COUNT ulasan)
- `pemesanan/index.php`: Baca data relasi (JOIN pemesanan dengan aktivitas)

### Jika ditanya: "Kenapa DELETE di pemesanan dan ulasan logic-nya berbeda?"
**Jawab:**
- `pemesanan/cancel.php`: Ada validasi status (hanya bisa delete jika "Menunggu Pembayaran")
- `ulasan/delete.php`: Langsung delete tanpa syarat khusus (user bebas hapus ulasannya kapan saja)

### Jika ditanya: "Bagaimana mencegah SQL Injection?"
**Jawab:**
- Semua query pakai **Prepared Statement** dengan mysqli_prepare()
- Parameter di-bind dengan `mysqli_stmt_bind_param()`
- Tidak ada direct query dengan string concatenation

---

## 📝 CHECKLIST PRESENTASI

**ORANG 1 (Auth + User):**
- [ ] Demo register user baru
- [ ] Tunjukkan validasi email unique
- [ ] Demo login
- [ ] Tunjukkan profil user dengan statistik
- [ ] Demo edit profil (dengan & tanpa ubah password)

**ORANG 2 (Kategori + Aktivitas):**
- [ ] Demo filter aktivitas per kategori
- [ ] Tunjukkan JOIN query dengan komunitas
- [ ] Demo detail aktivitas
- [ ] Tunjukkan rating rata-rata (hasil AVG)
- [ ] Tunjukkan daftar ulasan

**ORANG 3 (Pemesanan):**
- [ ] Demo buat pemesanan baru
- [ ] Tunjukkan validasi kuota
- [ ] Tunjukkan perhitungan total harga otomatis
- [ ] Demo lihat riwayat pemesanan
- [ ] Demo batalkan pemesanan (status Menunggu Pembayaran)

**ORANG 4 (Ulasan):**
- [ ] Demo buat ulasan (harus status Selesai)
- [ ] Tunjukkan validasi duplikat ulasan
- [ ] Demo edit ulasan
- [ ] Demo hapus ulasan
- [ ] Tunjukkan perubahan di detail aktivitas

---

**Good Luck! 🚀**
