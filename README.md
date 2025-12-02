# SiMakmur POS - Sistem Point of Sales

## 📖 Deskripsi Sistem
SiMakmur POS adalah sistem kasir berbasis web yang dirancang untuk memfasilitasi operasional bisnis F&B (Food & Beverage). Sistem ini mengintegrasikan tiga modul utama: Pelanggan (Self-Order), Kasir (POS), dan Admin (Manajemen).

## 🚀 Quick Start

### Clone & Setup (First Time)

```bash
# 1. Clone repository
git clone https://github.com/Karta-Sena/simakmur-pos.git
cd simakmur-pos

# 2. Setup environment
copy .env.example .env
php includes/generate_key.php

# 3. Configure database di .env
# Edit DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS

# 4. Import database
# Import simakmur_db.sql via phpMyAdmin

# 5. Test
# http://localhost/simakmur-pos/customer/
```

📘 **Panduan Lengkap:** Lihat [INSTALL.md](INSTALL.md)

---

## 🏗️ Arsitektur Sistem

Sistem ini dibagi menjadi 3 role utama:

1. **Customer (Pelanggan)**
   - **Fungsi**: Melakukan pemesanan mandiri (self-service), melihat menu, dan memantau status pesanan.
   - **Akses**: Folder `/customer`.
   - **Fitur**: Browsing menu, keranjang belanja, checkout, notifikasi status pesanan.

2. **Cashier (Kasir)**
   - **Fungsi**: Memproses pesanan masuk, menerima pembayaran, dan mencetak struk.
   - **Akses**: Folder `/cashier`.
   - **Fitur**: Antarmuka POS, manajemen antrian pesanan, proses pembayaran.

3. **Admin (Administrator)**
   - **Fungsi**: Mengelola data master (produk, kategori, user) dan melihat laporan.
   - **Akses**: Folder `/admin`.
   - **Fitur**: Dashboard analitik, manajemen produk, manajemen stok, laporan penjualan.

---

## 📂 Struktur Folder

```text
simakmur-pos/
├── admin/                  # Modul Administrator
├── api/                    # Backend API Endpoints (JSON Response)
├── assets/                 # Static Assets Global
│   ├── css/                # Stylesheet global/library
│   ├── img/                # Gambar statis (logo, icon)
│   └── js/                 # Javascript library/global
├── cashier/                # Modul Kasir
├── customer/               # Modul Pelanggan (Client-facing)
│   ├── css/                # Stylesheet khusus pelanggan
│   ├── js/                 # Logic frontend pelanggan (app.js)
│   └── index.php           # Halaman utama pemesanan
├── includes/               # Shared PHP Files
│   ├── db.php              # Koneksi Database
│   ├── env_loader.php      # Parse .env file
│   └── generate_key.php    # Generate APP_KEY
├── uploads/                # User Generated Content
│   └── products/           # Gambar produk yang diupload admin
├── .env                    # ⚠️ Environment config (JANGAN COMMIT!)
├── .env.example            # ✅ Template environment
├── .htaccess               # Security & routing config
├── config.php              # Konfigurasi utama (load dari .env)
├── simakmur_db.sql         # File Database Import
└── README.md               # Dokumentasi Proyek
```

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP Native (8.0+)
- **Frontend**: HTML5, CSS3 (Custom/Vanilla), JavaScript (Vanilla)
- **Database**: MySQL
- **Security**: .env configuration, CSRF protection, encryption
- **Format Data**: JSON (untuk komunikasi Frontend-Backend via API)

---

## 📚 Dokumentasi

- 📘 **[INSTALL.md](INSTALL.md)** - Setup & installation guide lengkap
- 🔐 **[ANALISIS_KEAMANAN.md](ANALISIS_KEAMANAN.md)** - Analisa keamanan & workflow tim
- 📖 **README.md** - Overview sistem (file ini)

---

## 🔐 Security & Environment

Sistem ini menggunakan **environment-based configuration** untuk keamanan:

### File Structure:
- `.env` - **JANGAN COMMIT!** Berisi kredensial lokal
- `.env.example` - Template untuk team members
- `config.php` - Load konfigurasi dari .env

### Setup Environment:
```bash
# Copy template
copy .env.example .env

# Generate encryption key
php includes/generate_key.php

# Configure database
# Edit .env file sesuai MySQL lokal kamu
```

⚠️ **PENTING:** Setiap developer harus punya `APP_KEY` dan database password sendiri!

---

## 📝 Catatan untuk Developer

### ✅ DO:
- ✅ Gunakan `.env` untuk konfigurasi lokal
- ✅ Generate `APP_KEY` unik untuk setiap environment
- ✅ Pull latest code sebelum mulai coding
- ✅ Commit `config.php` (hanya berisi logic, bukan kredensial)

### ❌ DON'T:
- ❌ **JANGAN** commit file `.env`
- ❌ **JANGAN** share APP_KEY ke orang lain
- ❌ **JANGAN** hardcode password di code
- ❌ **JANGAN** commit uploads/ folder

### API First:
- Komunikasi frontend-backend menggunakan folder `/api`
- Pastikan endpoint mengembalikan JSON yang valid
- Gunakan CSRF token untuk form submission

### Assets:
- Simpan gambar produk di `/uploads` (user-generated)
- Simpan asset statis di `/assets` (logo, icon, css, js)

---

## 🔄 Workflow Development

### Daily Work:
```bash
# 1. Pull latest
git pull origin main

# 2. Check .env.example untuk variable baru
# Update .env jika ada perubahan

# 3. Coding & testing

# 4. Commit & push
git add .
git commit -m "Your message"
git push origin main
```

---

## 🌐 Akses Aplikasi

Setelah setup selesai:

- **Customer**: `http://localhost/simakmur-pos/customer/`
- **Cashier**: `http://localhost/simakmur-pos/cashier/`
- **Admin**: `http://localhost/simakmur-pos/admin/`

---

## 🐛 Troubleshooting

### Error: "Configuration Error: .env file not found"
```bash
copy .env.example .env
php includes/generate_key.php
```

### Error: Database connection failed
- Cek `DB_PORT` di `.env` (biasanya 3306 atau 3307)
- Pastikan MySQL running di XAMPP
- Cek username & password MySQL

### Error: 403 Forbidden saat akses .env
✅ **NORMAL!** - File `.env` sengaja diblok untuk keamanan

**Lihat:** [INSTALL.md](INSTALL.md) untuk troubleshooting lengkap

---

## 👥 Team

**Project:** SiMakmur POS  
**Organization:** Karta-Sena  
**Last Updated:** 2 Desember 2025

---

## 📄 License

[Specify your license here]
