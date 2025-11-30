-- ============================================
-- Rollback: 001_addon_type_varchar.sql
-- Date: 2025-11-30
-- Description: Rollback addon type dari VARCHAR kembali ke ENUM
-- ============================================
-- WARNING: Rollback ini akan mengembalikan tipe data ke ENUM.
-- Pastikan tidak ada nilai selain 'sambal' atau 'saos' di kolom type!
-- ============================================

USE simakmur_db;

-- ============================================
-- PRE-ROLLBACK CHECK
-- ============================================
-- Cek apakah ada nilai selain 'sambal' atau 'saos'
SELECT type, COUNT(*) as count 
FROM addons 
WHERE type NOT IN ('sambal', 'saos')
GROUP BY type;

-- Jika query di atas mengembalikan hasil, 
-- JANGAN lanjutkan rollback! Ada data yang akan hilang.
-- ============================================

-- ============================================
-- ROLLBACK STEPS
-- ============================================

-- Step 1: Hapus index yang ditambahkan di migration
ALTER TABLE addons DROP INDEX idx_type;

-- Step 2: Ubah kembali ke ENUM
ALTER TABLE addons MODIFY type ENUM('sambal', 'saos') NOT NULL;

-- ============================================
-- VERIFICATION
-- ============================================
SHOW COLUMNS FROM addons WHERE Field = 'type';

-- Expected result:
-- Field: type
-- Type: enum('sambal','saos')
-- Null: NO

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT 'Rollback completed successfully!' AS status,
       'Type changed back to ENUM' AS change,
       NOW() AS completed_at;

-- ============================================
-- POST-ROLLBACK NOTES
-- ============================================
-- [ ] Update APPLIED_MIGRATIONS.md
-- [ ] Test aplikasi untuk ensure compatibility
-- [ ] Dokumentasikan alasan rollback
-- ============================================
