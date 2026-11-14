# 📚 DOKUMENTASI PROYEK LOKASAPA
## Platform Wisata Berkelanjutan Indonesia

---

## 🎯 PEMBAGIAN TUGAS KELOMPOK (4 ORANG)

### 👤 **ORANG 1: AUTH & USER**
**Folder:** `auth/` dan `user/`

#### Files yang Dikerjakan:
1. **auth/register.php** - CRUD: **CREATE**
   - Form registrasi user baru
   - Validasi email unik
   - Auto login setelah registrasi
   - Insert ke tabel `user`

2. **user/profile.php** - CRUD: **READ**
   - Menampilkan data profil user
   - Statistik pemesanan dan ulasan
   - Query: SELECT dari tabel `user`

3. **user/edit.php** - CRUD: **UPDATE**
   - Form edit profil user
   - Update nama, email, domisili, no_hp
   - Optional: ubah password
   - Query: UPDATE tabel `user`

**Database Tables:** `user`

---

### 📦 **ORANG 2: KATEGORI & AKTIVITAS**
**Folder:** `kategori/` dan `aktivitas/`

#### Files yang Dikerjakan:
1. **kategori/aktivitas.php** - CRUD: **READ**
   - List aktivitas per kategori (budaya, alam, kuliner, konservasi)
   - Filter berdasarkan parameter kategori
   - Query: SELECT dengan JOIN kategori, aktivitas, komunitas

2. **aktivitas/detail.php** - CRUD: **READ**
   - Detail lengkap 1 aktivitas
   - Informasi komunitas penyelenggara
   - List ulasan dari user lain
   - Tombol pesan aktivitas
   - Query: SELECT dengan JOIN multiple table

**Database Tables:** `kategori`, `aktivitas`, `komunitas`, `ulasan`

---

### 🛒 **ORANG 3: PEMESANAN**
**Folder:** `pemesanan/`

#### Files yang Dikerjakan:
1. **pemesanan/create.php** - CRUD: **CREATE**
   - Form pemesanan aktivitas
   - Validasi kuota, tanggal
   - Insert ke tabel `pemesanan`
   - Status: "Menunggu Pembayaran"

2. **pemesanan/index.php** - CRUD: **READ**
   - List semua pemesanan user
   - Filter berdasarkan user yang login
   - Tampilkan status (Menunggu, Diproses, Selesai)
   - Link ke detail dan form ulasan

3. **pemesanan/detail.php** - CRUD: **READ**
   - Detail lengkap 1 pemesanan
   - Informasi aktivitas yang dipesan
   - Total pembayaran
   - Tombol batal (jika status pending)

4. **pemesanan/cancel.php** - CRUD: **DELETE**
   - Batalkan pemesanan
   - Hanya bisa batalkan jika status "Menunggu Pembayaran"
   - Query: DELETE dari tabel `pemesanan`

**Database Tables:** `pemesanan`, `aktivitas`

---

### ⭐ **ORANG 4: ULASAN**
**Folder:** `ulasan/`

#### Files yang Dikerjakan:
1. **ulasan/create.php** - CRUD: **CREATE**
   - Form buat ulasan (rating + komentar)
   - Validasi: user sudah pesan & aktivitas sudah selesai
   - Prevent duplikasi ulasan
   - Insert ke tabel `ulasan`

2. **ulasan/edit.php** - CRUD: **UPDATE**
   - Form edit ulasan yang sudah dibuat
   - Update rating dan komentar
   - Validasi: hanya pemilik ulasan yang bisa edit
   - Query: UPDATE tabel `ulasan`

3. **ulasan/delete.php** - CRUD: **DELETE**
   - Hapus ulasan
   - Validasi kepemilikan
   - Query: DELETE dari tabel `ulasan`

**Database Tables:** `ulasan`, `aktivitas`, `pemesanan`

---

## 🗂️ STRUKTUR FILE LENGKAP

```
lokasapa/
├── index.php                    # Landing page (kategori)
├── config/
│   └── db.php                   # Koneksi database
├── layouts/
│   ├── header.php               # Navbar & header reusable
│   └── footer.php               # Footer reusable
├── auth/                        # ORANG 1
│   ├── login.php                # Login (sudah ada)
│   ├── register.php             # CREATE user baru
│   └── logout.php               # Logout (sudah ada)
├── user/                        # ORANG 1
│   ├── profile.php              # READ profil
│   └── edit.php                 # UPDATE profil
├── kategori/                    # ORANG 2
│   └── aktivitas.php            # READ list aktivitas per kategori
├── aktivitas/                   # ORANG 2
│   └── detail.php               # READ detail aktivitas
├── pemesanan/                   # ORANG 3
│   ├── create.php               # CREATE pemesanan
│   ├── index.php                # READ list pemesanan
│   ├── detail.php               # READ detail pemesanan
│   └── cancel.php               # DELETE pemesanan
└── ulasan/                      # ORANG 4
    ├── create.php               # CREATE ulasan
    ├── edit.php                 # UPDATE ulasan
    └── delete.php               # DELETE ulasan
```

---

## 🔄 USER FLOW

1. **Landing Page** (`index.php`)
   → User lihat kategori (Budaya, Alam, Kuliner, Konservasi)

2. **Pilih Kategori** (`kategori/aktivitas.php`)
   → Tampilkan list aktivitas dalam kategori tersebut

3. **Detail Aktivitas** (`aktivitas/detail.php`)
   → Lihat detail lengkap + ulasan user lain
   → Klik "Pesan Sekarang"

4. **Login/Register** (`auth/login.php` atau `auth/register.php`)
   → Jika belum login, redirect ke login
   → Setelah login, kembali ke detail aktivitas

5. **Form Pemesanan** (`pemesanan/create.php`)
   → Isi jumlah orang, tanggal kunjungan, metode pembayaran
   → Submit → Insert ke database

6. **Riwayat Pemesanan** (`pemesanan/index.php`)
   → User bisa lihat semua pesanannya
   → Status: Menunggu / Diproses / Selesai

7. **Beri Ulasan** (`ulasan/create.php`)
   → Setelah aktivitas selesai, user bisa beri rating & komentar
   → Ulasan muncul di halaman detail aktivitas

---

## 💾 DATABASE TABLES

### `user`
- id_user (PK)
- nama
- email
- password
- domisili
- no_hp

### `kategori`
- id_kategori (PK)
- nama_kategori (enum: budaya, alam, kuliner, konservasi)

### `komunitas`
- id_komunitas (PK)
- nama_komunitas
- lokasi
- tipe_komunitas
- deskripsi

### `aktivitas`
- id_aktivitas (PK)
- id_kategori (FK)
- id_komunitas (FK)
- nama_aktivitas
- deskripsi
- harga
- kuota
- lokasi
- foto

### `pemesanan`
- id_pemesanan (PK)
- id_user (FK)
- id_aktivitas (FK)
- tgl_pesan
- tgl_kunjungan
- jumlah_orang
- metode_pembayaran
- status
- total_harga

### `ulasan`
- id_ulasan (PK)
- id_user (FK)
- id_aktivitas (FK)
- rating (1-5)
- komentar

---

## 🚀 CARA MENJALANKAN PROJECT

1. **Setup Database:**
   ```
   - Buka phpMyAdmin
   - Import file lokasapa.sql
   - Database "lokasapa" akan terbuat otomatis
   ```

2. **Konfigurasi:**
   ```
   - Buka config/db.php
   - Sesuaikan kredensial database jika perlu
   ```

3. **Akses Website:**
   ```
   http://localhost/lokasapa/
   ```

4. **Test Login:**
   ```
   Email: ariel@mail.com
   Password: 123456
   ```

---

## 📝 CATATAN PENTING

### Untuk Presentasi ke Dosen:
- Setiap orang harus bisa menjelaskan **file-file yang dikerjakan**
- Jelaskan **CRUD operation** di masing-masing file
- Tunjukkan **query SQL** yang digunakan
- Demonstrasikan **flow dari awal sampai akhir**

### Tips Demo:
1. **ORANG 1:** Demo registrasi → login → lihat profil → edit profil
2. **ORANG 2:** Demo pilih kategori → lihat list aktivitas → detail aktivitas
3. **ORANG 3:** Demo pesan aktivitas → lihat riwayat → detail pesanan → batalkan
4. **ORANG 4:** Demo buat ulasan → edit ulasan → hapus ulasan

---

## 🔐 SECURITY NOTES

⚠️ **UNTUK PRODUCTION (Jika mau deploy):**
- Gunakan `password_hash()` dan `password_verify()` untuk password
- Validasi semua input user
- Gunakan prepared statements (sudah digunakan di project ini)
- Tambahkan file `.env` untuk kredensial database
- Jangan commit password ke Git

---

## 🎨 TEKNOLOGI YANG DIGUNAKAN

- **Backend:** PHP Native (Procedural)
- **Frontend:** Bootstrap 5.3
- **Database:** MySQL (MariaDB)
- **Icons:** Bootstrap Icons
- **Server:** XAMPP (Apache + MySQL)

---

## 📞 KONTAK & SUPPORT

Jika ada pertanyaan tentang bagian masing-masing:
- Diskusikan dengan tim
- Baca komentar di dalam file
- Lihat alur flow di dokumentasi ini

---

**Good Luck untuk Presentasi! 🚀**

---

_Dibuat untuk Proyek Akhir Mata Kuliah Sistem Basis Data_
_Prodi S1 Sistem Informasi Semester 3_
