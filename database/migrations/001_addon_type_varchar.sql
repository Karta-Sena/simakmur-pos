-- ============================================
-- Migration: Change addon type from ENUM to VARCHAR
-- Date: 2025-11-30
-- Description: Increase flexibility for future addon types
-- ============================================

USE simakmur_db;

-- Step 1: Add new column with VARCHAR type
ALTER TABLE addons 
ADD COLUMN type_new VARCHAR(50) NULL AFTER type;

-- Step 2: Copy data from old ENUM to new VARCHAR
UPDATE addons SET type_new = type;

-- Step 3: Drop old ENUM column
ALTER TABLE addons DROP COLUMN type;

-- Step 4: Rename new column to 'type'
ALTER TABLE addons CHANGE type_new type VARCHAR(50) NOT NULL;

-- Step 5: Add index for better query performance
ALTER TABLE addons ADD INDEX idx_type (type);

-- Verify migration
SELECT id, name, type, price, is_active FROM addons;

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT 'Migration completed successfully!' AS status;
