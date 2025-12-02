# 🚀 Setup & Installation Guide - SiMakmur POS

## Prerequisites

- XAMPP (PHP 8.0+, MySQL)
- Git
- Web Browser

---

## 📥 Installation Steps

### 1. Clone Repository

```bash
git clone https://github.com/Karta-Sena/simakmur-pos.git
cd simakmur-pos
```

### 2. Setup Environment File

```bash
# Windows
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

### 3. Generate APP_KEY (WAJIB!)

```bash
php includes/generate_key.php
```

**Pilih `y` saat ditanya untuk auto-update .env**

Output:
```
✅ APP_KEY berhasil di-generate!
📝 Update .env file otomatis? (y/n): y
✅ .env file berhasil di-update!
```

### 4. Configure Database

Edit file `.env` sesuai konfigurasi MySQL lokal:

```env
DB_HOST=localhost
DB_PORT=3307          # Sesuaikan dengan port MySQL kamu
DB_NAME=simakmur_db
DB_USER=root
DB_PASS=              # Password MySQL kamu (biasanya kosong di XAMPP)
```

### 5. Import Database

1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: `simakmur_db`
3. Import file: `simakmur_db.sql`

### 6. Start XAMPP

- Start Apache
- Start MySQL

### 7. Test Application

Buka browser:
```
http://localhost/simakmur-pos/customer/index.php
```

**Expected:** Halaman customer muncul tanpa error ✅

---

## ⚠️ Common Issues

### Issue 1: "Configuration Error: .env file not found"

**Solution:**
```bash
copy .env.example .env
php includes/generate_key.php
```

### Issue 2: "Missing required environment variables: APP_KEY"

**Solution:**
```bash
php includes/generate_key.php
# Pilih 'y' untuk auto-update
```

### Issue 3: Database Connection Error

**Solution:**
- Cek port MySQL di XAMPP Control Panel
- Update `DB_PORT` di `.env` (biasanya 3306 atau 3307)
- Pastikan MySQL sudah running

### Issue 4: 403 Forbidden saat akses .env

**Solution:**
✅ Ini **NORMAL** - `.env` sengaja diblok untuk keamanan!

---

## 🔄 Update dari Repository

```bash
# Pull latest changes
git pull origin main

# Check jika ada perubahan di .env.example
# Jika ada, update .env kamu sesuai template baru
```

**PENTING:** Jangan overwrite `.env` kamu dengan `.env.example`!

---

## 🛠️ Development Workflow

### Setup Baru (First Time)
```bash
1. git clone
2. copy .env.example .env
3. php includes/generate_key.php
4. Edit .env (database config)
5. Import database
6. Test di browser
```

### Daily Development
```bash
1. git pull
2. Cek perubahan di .env.example
3. Update .env jika perlu (variable baru dll)
4. Test perubahan
5. git add, commit, push
```

---

## 📋 File Structure Overview

```
simakmur-pos/
├── .env                # ⚠️ JANGAN COMMIT! (kredensial lokal kamu)
├── .env.example        # ✅ Template (commit ini)
├── config.php          # ✅ Load dari .env (commit ini)
├── .htaccess           # Security config
├── includes/
│   ├── env_loader.php  # Parse .env
│   └── generate_key.php # Generate APP_KEY
├── customer/           # Customer module
├── cashier/            # Cashier module
├── admin/              # Admin module
└── api/                # REST API
```

---

## 🔐 Security Notes

### ✅ DO:
- ✅ Generate APP_KEY unik untuk setiap developer
- ✅ Keep `.env` di `.gitignore`
- ✅ Use strong database password (production)
- ✅ Update `.env` sendiri, jangan copy dari teman

### ❌ DON'T:
- ❌ JANGAN commit `.env` ke git
- ❌ JANGAN share APP_KEY ke orang lain
- ❌ JANGAN push kredensial ke public repo
- ❌ JANGAN pakai APP_KEY yang sama untuk production

---

## 👥 Team Members

Setiap developer punya:
- ✅ `.env` sendiri (different database password)
- ✅ `APP_KEY` sendiri (different encryption key)
- ✅ Database lokal sendiri

Yang sama:
- ✅ `.env.example` (template)
- ✅ `config.php` (logic)
- ✅ Source code

---

## 📞 Need Help?

- Check `ANALISIS_KEAMANAN.md` untuk detail security
- Check `README.md` untuk project overview
- Contact team lead jika masih error

---

**Last Updated:** 2 Desember 2025  
**Maintainer:** Karta-Sena Team
