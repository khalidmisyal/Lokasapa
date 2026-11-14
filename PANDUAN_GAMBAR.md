# 📸 PANDUAN PENEMPATAN GAMBAR

## 📁 Struktur Folder Gambar

```
lokasapa/
└── assets/
    └── images/
        ├── aktivitas/        ← Taruh gambar aktivitas di sini
        ├── kategori/         ← Taruh gambar kategori di sini
        └── default/          ← Gambar default/placeholder
```

---

## 🎯 NAMA FILE GAMBAR YANG HARUS DISIAPKAN

### 1️⃣ GAMBAR KATEGORI (4 files)
Letakkan di: `assets/images/kategori/`

- `Budaya-home.jpg` - Gambar untuk kategori Budaya (contoh: tari tradisional, batik, wayang)
- `Alam-home.jpg` - Gambar untuk kategori Alam (contoh: gunung, hutan, air terjun)
- `Kuliner-home.jpg` - Gambar untuk kategori Kuliner (contoh: makanan tradisional, pasar)
- `Konservasi-home.jpg` - Gambar untuk kategori Konservasi (contoh: pantai bersih, mangrove)

**Ukuran Rekomendasi:** 800x600 px atau lebih

---

### 2️⃣ GAMBAR AKTIVITAS (16 files)
Letakkan di: `assets/images/aktivitas/`

**PERHATIAN:** Nama file harus **SAMA PERSIS** dengan nama di bawah ini!

#### Aktivitas Budaya:
- `Belajar Tari Jaipong.jpg` - Tari Jaipong
- `Membatik Tradisional.jpg` - Workshop Batik Tulis
- `Kelas Angklung.jpg` - Belajar Angklung
- `Tur Kampung Adat.jpg` - Wisata Kampung Batik

#### Aktivitas Alam:
- `Tracking Gunung Salak.jpg` - Petik Salak Pondoh
- `Camping Hutan Pinus.jpg` - Camping Gunung
- `Susur Sungai.jpg` - Tubing Sungai
- `Trekking Sunrise Hill.jpg` - Tracking Sunrise

#### Aktivitas Kuliner:
- `Tour Gudeg Tradisional.jpg` - Cooking Class Gudeg
- `Workshop Jajanan Pasar.jpg` - Food Tour Jajanan Pasar
- `Street Food Night Tour.jpg` - Kuliner Malam
- `Kopi Lokal Experience.jpg` - Workshop Kopi Tradisional

#### Aktivitas Konservasi:
- `Tanam Mangrove.jpg` - Penanaman Mangrove
- `Bersih Pantai.jpg` - Beach Clean Up
- `Edukasi Terumbu Karang.jpg` - Transplantasi Terumbu Karang
- `Urban Farming.jpg` - Urban Farming Workshop

**Ukuran Rekomendasi:** 1200x800 px atau lebih

---

### 3️⃣ GAMBAR DEFAULT (1 file)
Letakkan di: `assets/images/default/`

- `no-image.jpg` - Gambar placeholder jika gambar aktivitas tidak ditemukan

**Ukuran Rekomendasi:** 800x600 px

---

## ⚙️ CARA KERJA SISTEM GAMBAR

### Fungsi Helper: `get_image_path()`
File: `config/helpers.php`

```php
get_image_path($filename, $folder = 'aktivitas', $base_url = '/lokasapa/')
```

**Cara kerja:**
1. Cek apakah file gambar ada di `assets/images/{folder}/{filename}`
2. Jika **ADA** → Tampilkan gambar tersebut
3. Jika **TIDAK ADA** → Tampilkan gambar default (`no-image.jpg`)

**Contoh penggunaan di PHP:**
```php
// Untuk gambar aktivitas
$img_url = get_image_path($aktivitas['foto'], 'aktivitas', $base_url);

// Untuk gambar kategori
$img_url = $base_url . 'assets/images/kategori/budaya.jpg';
```

---

## ✅ CHECKLIST PENEMPATAN GAMBAR

### Kategori (4 gambar):
- [ ] `assets/images/kategori/Budaya-home.jpg`
- [ ] `assets/images/kategori/Alam-home.jpg`
- [ ] `assets/images/kategori/Kuliner-home.jpg`
- [ ] `assets/images/kategori/Konservasi-home.jpg`

### Aktivitas Budaya (4 gambar):
- [ ] `assets/images/aktivitas/Belajar Tari Jaipong.jpg`
- [ ] `assets/images/aktivitas/Membatik Tradisional.jpg`
- [ ] `assets/images/aktivitas/Kelas Angklung.jpg`
- [ ] `assets/images/aktivitas/Tur Kampung Adat.jpg`

### Aktivitas Alam (4 gambar):
- [ ] `assets/images/aktivitas/Tracking Gunung Salak.jpg`
- [ ] `assets/images/aktivitas/Camping Hutan Pinus.jpg`
- [ ] `assets/images/aktivitas/Susur Sungai.jpg`
- [ ] `assets/images/aktivitas/Trekking Sunrise Hill.jpg`

### Aktivitas Kuliner (4 gambar):
- [ ] `assets/images/aktivitas/Tour Gudeg Tradisional.jpg`
- [ ] `assets/images/aktivitas/Workshop Jajanan Pasar.jpg`
- [ ] `assets/images/aktivitas/Street Food Night Tour.jpg`
- [ ] `assets/images/aktivitas/Kopi Lokal Experience.jpg`

### Aktivitas Konservasi (4 gambar):
- [ ] `assets/images/aktivitas/Tanam Mangrove.jpg`
- [ ] `assets/images/aktivitas/Bersih Pantai.jpg`
- [ ] `assets/images/aktivitas/Edukasi Terumbu Karang.jpg`
- [ ] `assets/images/aktivitas/Urban Farming.jpg`

### Default (1 gambar):
- [ ] `assets/images/default/no-image.jpg`

**TOTAL: 21 file gambar**

---

## 🔍 TROUBLESHOOTING

### Gambar tidak muncul?
1. **Cek nama file** - Harus sama persis dengan database (case-sensitive)
2. **Cek lokasi folder** - Pastikan di `assets/images/aktivitas/`
3. **Cek ekstensi** - Harus `.jpg` (bisa diganti ke `.png` di kode jika perlu)
4. **Cek permission** - File harus readable oleh web server

### Muncul gambar default terus?
Berarti file gambar tidak ditemukan. Cek kembali:
- Nama file sudah benar?
- File ada di folder yang benar?
- Kolom `foto` di database sudah terisi?

---

## 💡 TIPS

### Untuk Testing:
1. Upload 1 gambar dulu (misal: `budaya.jpg`)
2. Buka halaman landing page
3. Cek apakah gambar kategori budaya muncul
4. Jika muncul, lanjut upload yang lain

### Untuk Development:
- Gunakan gambar dengan ukuran tidak terlalu besar (max 500KB per file)
- Format JPEG untuk gambar foto, PNG untuk gambar dengan transparansi
- Resize gambar dulu sebelum upload agar loading cepat

---

**File-file yang sudah diupdate untuk menggunakan gambar lokal:**

✅ `index.php` - Landing page kategori  
✅ `kategori/aktivitas.php` - List aktivitas  
✅ `aktivitas/detail.php` - Detail aktivitas  
✅ `pemesanan/index.php` - Riwayat pemesanan  
✅ `pemesanan/detail.php` - Detail pemesanan  
✅ `pemesanan/create.php` - Form pemesanan  
✅ `config/helpers.php` - Fungsi `get_image_path()`

**Semua sudah siap! Tinggal taruh gambar sesuai nama file di atas.** 🚀
