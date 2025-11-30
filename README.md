# SiMakmur POS - Sistem Point of Sales

## 📖 Deskripsi Sistem
SiMakmur POS adalah sistem kasir berbasis web yang dirancang untuk memfasilitasi operasional bisnis F&B (Food & Beverage). Sistem ini mengintegrasikan tiga modul utama: Pelanggan (Self-Order), Kasir (POS), dan Admin (Manajemen).

## 🏗️ Arsitektur Sistem

Sistem ini dibagi menjadi 3 role utama:

1.  **Customer (Pelanggan)**
    *   **Fungsi**: Melakukan pemesanan mandiri (self-service), melihat menu, dan memantau status pesanan.
    *   **Akses**: Folder `/customer`.
    *   **Fitur**: Browsing menu, keranjang belanja, checkout, notifikasi status pesanan.

2.  **Cashier (Kasir)**
    *   **Fungsi**: Memproses pesanan masuk, menerima pembayaran, dan mencetak struk.
    *   **Akses**: Folder `/cashier`.
    *   **Fitur**: Antarmuka POS, manajemen antrian pesanan, proses pembayaran.

3.  **Admin (Administrator)**
    *   **Fungsi**: Mengelola data master (produk, kategori, user) dan melihat laporan.
    *   **Akses**: Folder `/admin`.
    *   **Fitur**: Dashboard analitik, manajemen produk, manajemen stok, laporan penjualan.

## 📂 Struktur Folder

Berikut adalah penjelasan detail mengenai struktur direktori proyek ini agar memudahkan kolaborasi tim:

```text
simakmur-pos/
├── admin/                  # Modul Administrator
│   ├── index.php           # Dashboard Admin
│   └── ...                 # File manajemen (produk, user, laporan)
│
├── api/                    # Backend API Endpoints (JSON Response)
│   ├── products/           # API untuk data produk
│   ├── transactions/       # API untuk transaksi
│   └── ...                 # Endpoint lainnya
│
├── assets/                 # Static Assets Global
│   ├── css/                # Stylesheet global/library
│   ├── img/                # Gambar statis (logo, icon)
│   └── js/                 # Javascript library/global
│
├── cashier/                # Modul Kasir
│   ├── index.php           # Antarmuka POS Kasir
│   └── ...                 # Logika kasir
│
├── customer/               # Modul Pelanggan (Client-facing)
│   ├── css/                # Stylesheet khusus pelanggan
│   ├── js/                 # Logic frontend pelanggan (app.js)
│   └── index.php           # Halaman utama pemesanan
│
├── includes/               # Shared PHP Files
│   ├── db.php              # Koneksi Database
│   └── functions.php       # Fungsi helper global
│
├── uploads/                # User Generated Content
│   └── products/           # Gambar produk yang diupload admin
│
├── config.php              # Konfigurasi Utama (Database, Base URL)
├── simakmur_db.sql         # File Database Import
└── README.md               # Dokumentasi Proyek
```

## 🚀 Cara Instalasi & Setup

1.  **Persyaratan Sistem**
    *   Web Server (Apache/Nginx)
    *   PHP 7.4 atau lebih baru
    *   MySQL/MariaDB

2.  **Instalasi Database**
    *   Buat database baru di phpMyAdmin (misal: `simakmur_db`).
    *   Import file `simakmur_db.sql` yang ada di root folder.

3.  **Konfigurasi**
    *   Copy file `config.example.php` menjadi `config.php` (jika belum ada).
    *   Sesuaikan kredensial database di dalam `config.php`:
        ```php
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'simakmur_db');
        ```

4.  **Akses Aplikasi**
    *   **Customer**: `http://localhost/simakmur-pos/customer`
    *   **Cashier**: `http://localhost/simakmur-pos/cashier`
    *   **Admin**: `http://localhost/simakmur-pos/admin`

## 🛠️ Teknologi yang Digunakan
*   **Backend**: PHP Native
*   **Frontend**: HTML5, CSS3 (Custom/Vanilla), JavaScript (Vanilla)
*   **Database**: MySQL
*   **Format Data**: JSON (untuk komunikasi Frontend-Backend via API)

## 📝 Catatan untuk Developer
*   **API First**: Komunikasi antara frontend (terutama modul Customer) dan backend sangat bergantung pada folder `/api`. Pastikan endpoint mengembalikan JSON yang valid.
*   **Assets**: Simpan gambar produk di `/uploads`, jangan di `/assets`. `/assets` hanya untuk file statis aplikasi.
*   **Git**: Jangan commit file `config.php` jika berisi password production. Gunakan `.gitignore`.
