-- ============================================
-- Migration Template
-- ============================================
-- Gunakan template ini untuk membuat migration baru
-- Copy file ini dan rename sesuai format:
-- [nomor]_[deskripsi_singkat].sql
-- 
-- Contoh: 002_add_discount_column.sql
-- ============================================

-- ============================================
-- Migration: [JUDUL PERUBAHAN - contoh: Add Discount Column to Products]
-- Date: [YYYY-MM-DD]
-- Created By: [Nama Anda]
-- Description: 
--   [Penjelasan detail tentang perubahan yang akan dilakukan]
--   [Jelaskan alasan/tujuan migration ini]
--   [Cantumkan impact yang mungkin terjadi]
-- ============================================

USE simakmur_db;

-- ============================================
-- BACKUP REMINDER
-- ============================================
-- WAJIB: Backup database sebelum menjalankan migration!
-- Command: mysqldump -u root -p simakmur_db > backup_YYYYMMDD.sql
-- ============================================

-- ============================================
-- PRE-MIGRATION CHECKS
-- ============================================
-- [Optional: Query untuk cek kondisi database sebelum migration]
-- Contoh:
-- SELECT COUNT(*) as total_products FROM products;
-- SHOW COLUMNS FROM products;

-- ============================================
-- MIGRATION STEPS
-- ============================================

-- Step 1: [Jelaskan langkah pertama - contoh: Add new column]
-- [SQL Query]
-- Contoh:
-- ALTER TABLE products ADD COLUMN discount DECIMAL(5,2) DEFAULT 0.00 AFTER price;

-- Step 2: [Jelaskan langkah kedua - contoh: Set default values]
-- [SQL Query]
-- Contoh:
-- UPDATE products SET discount = 0.00 WHERE discount IS NULL;

-- Step 3: [Jelaskan langkah berikutnya jika ada]
-- [SQL Query]

-- ============================================
-- POST-MIGRATION VERIFICATION
-- ============================================
-- [Query untuk memverifikasi bahwa migration berhasil]
-- Contoh:
-- SELECT * FROM products LIMIT 5;
-- SHOW COLUMNS FROM products WHERE Field = 'discount';

-- ============================================
-- ROLLBACK SCRIPT (Optional tapi recommended)
-- ============================================
-- Simpan di file terpisah: [nomor]_[nama]_rollback.sql
-- Contoh rollback untuk migration ini:
-- 
-- ALTER TABLE products DROP COLUMN discount;
--

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT 'Migration completed successfully!' AS status,
       NOW() AS completed_at,
       '[NOMOR_MIGRATION]' AS migration_number;

-- ============================================
-- POST-MIGRATION CHECKLIST
-- ============================================
-- [ ] Verify structure change (SHOW COLUMNS)
-- [ ] Test application functionality
-- [ ] Update APPLIED_MIGRATIONS.md
-- [ ] Create rollback script if not exists
-- [ ] Commit to Git
-- ============================================
