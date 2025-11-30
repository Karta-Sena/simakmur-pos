# Database Migrations

Folder ini berisi file-file SQL migration untuk mengelola perubahan struktur database secara terorganisir.

## 📋 Apa itu Migration?

Migration adalah file SQL yang berisi script untuk **mengubah struktur database** (seperti menambah kolom, mengubah tipe data, dll). Setiap migration memiliki:
- **Nomor urut** (001, 002, dst) untuk tracking
- **Deskripsi** yang jelas tentang perubahan
- **Tanggal** pembuatan

## 🗂️ Struktur Penamaan File

```
[nomor]_[deskripsi_singkat].sql

Contoh:
001_addon_type_varchar.sql
002_add_user_role_column.sql
003_create_promotions_table.sql
```

## 🚀 Cara Menjalankan Migration

### Opsi 1: Via phpMyAdmin (Recommended untuk pemula)
1. Buka http://localhost/phpmyadmin
2. Pilih database `simakmur_db`
3. Klik tab **SQL**
4. Copy-paste isi file migration
5. Klik **Go**

### Opsi 2: Via MySQL Command Line
```bash
# Jalankan satu migration
mysql -u root -p simakmur_db < database/migrations/001_addon_type_varchar.sql

# Atau dari Windows
cd C:\xampp\htdocs\simakmur-pos
type database\migrations\001_addon_type_varchar.sql | mysql -u root -p simakmur_db
```

### Opsi 3: Manual Step-by-Step (Paling Aman)
Buka file migration, copy satu query per satu dan jalankan di phpMyAdmin SQL tab.

## ⚠️ Best Practices

### ✅ DO (Lakukan):
- **Backup database** sebelum menjalankan migration
  ```bash
  mysqldump -u root -p simakmur_db > backup_before_migration.sql
  ```
- Jalankan migration di **environment development** dulu
- Catat migration yang sudah diterapkan di `APPLIED_MIGRATIONS.md`
- Beri komentar yang jelas di setiap migration file
- Test aplikasi setelah migration

### ❌ DON'T (Jangan):
- Jangan edit migration yang **sudah diterapkan**
- Jangan hapus migration files (simpan sebagai dokumentasi)
- Jangan jalankan migration langsung di **production** tanpa test
- Jangan skip nomor urut migration

## 📝 Template Migration Baru

Saat membuat migration baru, gunakan template ini:

```sql
-- ============================================
-- Migration: [Deskripsi Perubahan]
-- Date: [YYYY-MM-DD]
-- Description: [Penjelasan detail tentang perubahan ini]
-- ============================================

USE simakmur_db;

-- Step 1: [Jelaskan langkah pertama]
-- [SQL Query]

-- Step 2: [Jelaskan langkah kedua]
-- [SQL Query]

-- Verify migration
SELECT '[Contoh query untuk verifikasi]';

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT 'Migration completed successfully!' AS status;
```

## 🔄 Rollback Migration

Jika perlu membatalkan migration, buat file rollback:

```
database/migrations/rollback/
└── 001_addon_type_varchar_rollback.sql
```

**Contoh Rollback:**
```sql
-- Rollback: 001_addon_type_varchar.sql
-- Mengembalikan type dari VARCHAR ke ENUM

ALTER TABLE addons MODIFY type ENUM('sambal','saos') NOT NULL;
SELECT 'Rollback completed!' AS status;
```

## 📊 Migration History

Lihat file `APPLIED_MIGRATIONS.md` untuk daftar migration yang sudah diterapkan.

## 🆘 Troubleshooting

### Error: "Table doesn't exist"
- Pastikan Anda sudah `USE simakmur_db;` di awal file
- Cek apakah database name benar

### Error: "Column already exists"
- Migration mungkin sudah pernah dijalankan
- Cek `APPLIED_MIGRATIONS.md` untuk konfirmasi

### Error: "Data truncated"
- Backup data dulu sebelum migration
- Cek apakah ada data yang tidak kompatibel dengan perubahan

## 📞 Kontak

Jika ada pertanyaan tentang migrations, hubungi database admin atau buka issue di repository.

---

**Last Updated:** 2025-11-30
