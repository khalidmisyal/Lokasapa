# 🗂️ STRUKTUR FILE LOKASAPA - QUICK REFERENCE

## 📁 Struktur Lengkap

```
lokasapa/
│
├── 📄 index.php                 # Landing page dengan kategori
├── 📄 README.md                 # Dokumentasi lengkap proyek
├── 📄 CHECKLIST_PRESENTASI.md   # Panduan presentasi
├── 📄 database_testing.sql      # Query testing tambahan
│
├── 📁 config/
│   ├── db.php                   # Koneksi database
│   └── helpers.php              # Helper functions
│
├── 📁 layouts/
│   ├── header.php               # Navbar & header reusable
│   └── footer.php               # Footer reusable
│
├── 📁 assets/                   # (Kosong - untuk CSS/JS custom di masa depan)
│   ├── css/
│   └── js/
│
├── 📁 auth/                     # ⭐ ORANG 1
│   ├── login.php                # Login user
│   ├── register.php             # ✏️ CREATE: Registrasi user baru
│   └── logout.php               # Logout & destroy session
│
├── 📁 user/                     # ⭐ ORANG 1
│   ├── profile.php              # 📖 READ: Tampilkan profil user
│   └── edit.php                 # ✏️ UPDATE: Edit profil user
│
├── 📁 kategori/                 # ⭐ ORANG 2
│   └── aktivitas.php            # 📖 READ: List aktivitas per kategori
│
├── 📁 aktivitas/                # ⭐ ORANG 2
│   └── detail.php               # 📖 READ: Detail lengkap aktivitas
│
├── 📁 pemesanan/                # ⭐ ORANG 3
│   ├── create.php               # ✏️ CREATE: Buat pemesanan baru
│   ├── index.php                # 📖 READ: Riwayat pemesanan user
│   ├── detail.php               # 📖 READ: Detail 1 pemesanan
│   └── cancel.php               # 🗑️ DELETE: Batalkan pemesanan
│
├── 📁 ulasan/                   # ⭐ ORANG 4
│   ├── create.php               # ✏️ CREATE: Buat ulasan baru
│   ├── edit.php                 # ✏️ UPDATE: Edit ulasan
└── 📁 ulasan/                   # ⭐ ORANG 4
    ├── create.php               # ✏️ CREATE: Buat ulasan baru
    ├── edit.php                 # ✏️ UPDATE: Edit ulasan
    └── delete.php               # 🗑️ DELETE: Hapus ulasan

## 🔑 FILE PENTING PER ORANG

### 👤 ORANG 1: AUTH & USER
```
auth/register.php    → CREATE user
user/profile.php     → READ profil
user/edit.php        → UPDATE profil
```

### 📦 ORANG 2: KATEGORI & AKTIVITAS
```
kategori/aktivitas.php  → READ list aktivitas
aktivitas/detail.php    → READ detail aktivitas
```

### 🛒 ORANG 3: PEMESANAN
```
pemesanan/create.php    → CREATE pemesanan
pemesanan/index.php     → READ list pemesanan
pemesanan/detail.php    → READ detail pemesanan
pemesanan/cancel.php    → DELETE pemesanan
```

### ⭐ ORANG 4: ULASAN
```
ulasan/create.php    → CREATE ulasan
ulasan/edit.php      → UPDATE ulasan
ulasan/delete.php    → DELETE ulasan
```

---

## 📊 DATABASE TABLES & RELASI

```
user (id_user)
  └─── pemesanan (id_user, id_aktivitas) ───┐
  └─── ulasan (id_user, id_aktivitas) ──────┤
                                             │
kategori (id_kategori)                       │
  └─── aktivitas (id_kategori, id_komunitas) ┘
         └─── pemesanan (id_aktivitas)
         └─── ulasan (id_aktivitas)

komunitas (id_komunitas)
  └─── aktivitas (id_komunitas)
```

---

## 🔄 USER FLOW LENGKAP

```
1. index.php
   └─> Pilih Kategori
       
2. kategori/aktivitas.php
   └─> Lihat List Aktivitas
       
3. aktivitas/detail.php
   └─> Lihat Detail + Ulasan
       └─> Klik "Pesan Sekarang"
           
4. Cek Login
   ├─> Belum Login → auth/login.php atau auth/register.php
   └─> Sudah Login → Lanjut ke Form Pemesanan
       
5. pemesanan/create.php
   └─> Isi Form & Submit
       └─> Sukses → Redirect ke pemesanan/index.php
           
6. pemesanan/index.php
   └─> Lihat Riwayat Pesanan
       ├─> Klik Detail → pemesanan/detail.php
       ├─> Klik Batalkan → pemesanan/cancel.php
       └─> Jika Status "Selesai" → Bisa Beri Ulasan
           
7. ulasan/create.php
   └─> Beri Rating & Komentar
       └─> Ulasan Muncul di aktivitas/detail.php
           ├─> Edit → ulasan/edit.php
           └─> Hapus → ulasan/delete.php
```

---

## 🎨 KOMPONEN REUSABLE

### layouts/header.php
- Navbar dengan menu dinamis (login/logout)
- Link ke Beranda, Pesanan, Profil
- Notifikasi user yang login

### layouts/footer.php
- Copyright & informasi footer
- Bootstrap JS

### config/db.php
- Koneksi ke database `lokasapa`
- Host: localhost, User: root, Pass: (kosong)

### config/helpers.php
- `show_flash_message()` → Tampilkan notifikasi
- `format_tanggal()` → Format tanggal Indonesia
- `format_rupiah()` → Format harga
- `require_login()` → Cek login

---

## 🔐 FITUR KEAMANAN

✅ **Sudah Diimplementasi:**
- Prepared Statements (mysqli_prepare)
- Session management
- Validasi input form
- CSRF protection (basic)

⚠️ **Untuk Production:**
- Password hashing (password_hash)
- HTTPS/SSL
- Environment variables (.env)
- Input sanitization lebih ketat
- Rate limiting login

---

## 📱 RESPONSIVE DESIGN

- Bootstrap 5.3 Grid System
- Mobile-friendly navbar
- Responsive cards & tables
- Touch-friendly buttons

---

## 🎯 FITUR UTAMA

### ✅ Implemented
- [x] Registrasi & Login
- [x] CRUD User Profile
- [x] List Aktivitas per Kategori
- [x] Detail Aktivitas + Ulasan
- [x] CRUD Pemesanan
- [x] CRUD Ulasan
- [x] Rating System (1-5 bintang)
- [x] Status Pemesanan
- [x] Flash Messages

### 🔮 Future Features (Bisa Dikembangkan)
- [ ] Dashboard Admin
- [ ] CRUD Aktivitas (untuk admin)
- [ ] Upload gambar aktivitas
- [ ] Payment gateway integration
- [ ] Email notification
- [ ] Export laporan PDF
- [ ] Filter & Search aktivitas
- [ ] Pagination
- [ ] Charts & Analytics

---

## 🛠️ TEKNOLOGI

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.0 Native |
| Database | MySQL (MariaDB) |
| Frontend | Bootstrap 5.3 |
| Icons | Bootstrap Icons 1.10 |
| Server | XAMPP (Apache + MySQL) |
| Editor | VS Code |

---

## 📞 QUICK COMMANDS

### Start XAMPP
```
- Buka XAMPP Control Panel
- Start Apache
- Start MySQL
```

### Import Database
```
1. Buka http://localhost/phpmyadmin
2. Create database "lokasapa"
3. Import file lokasapa.sql
4. (Opsional) Run database_testing.sql
```

### Access Website
```
http://localhost/lokasapa/
```

### Default Login
```
Email: ariel@mail.com
Password: 123456
```

---

## 🐛 COMMON ERRORS & FIXES

| Error | Fix |
|-------|-----|
| Cannot connect to database | Cek XAMPP MySQL running |
| Undefined variable $base_url | Tambahkan di setiap file |
| Headers already sent | Cek output sebelum header() |
| CSS tidak muncul | Cek koneksi internet (CDN) |
| Session tidak jalan | Pastikan session_start() di awal |

---

**Last Updated:** December 2025  
**Version:** 1.0  
**Author:** Kelompok LOKASAPA - S1 Sistem Informasi Semester 3
