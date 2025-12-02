-- ============================================
-- SEEDER: Create Cashier Users
-- ============================================

INSERT INTO `users` (`username`, `password`, `role`, `full_name`, `is_active`) VALUES
('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier', 'Kasir 1', 1),
('kasir2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cashier', 'Kasir 2', 1)
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`);

-- Note: Password is 'admin123'
-- Note: Passowrd for kasir1 change to kasir1makmur
-- Note: Passowrd for kasir2 change to kasir2makmur