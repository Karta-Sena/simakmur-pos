<?php
// FILE: api/products/list.php

// 1. Panggil Koneksi & Helper
require_once '../../includes/db.php';
require_once '../../includes/response.php';

try {
    // 2. Query ke Database
    // Ambil produk yang aktif (is_active = 1) dan stoknya > 0
    // Kita join dengan categories untuk dapat nama kategori juga (opsional)
    $sql = "SELECT p.*, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY c.sort_order ASC, p.name ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll();

    // 3. Modifikasi Data (Opsional)
    // Kita biarkan raw image dari DB, agar frontend yang mengatur path-nya
    // karena BASE_URL bisa tidak konsisten tergantung lokasi script.
    foreach ($products as &$p) {
        // Fallback jika tidak ada gambar
        if (empty($p['image'])) {
            $p['image'] = 'https://placehold.co/200x200?text=No+Image';
        }
    }

    // 4. Kirim JSON
    Response::json($products, 'Data produk berhasil diambil');

} catch (Exception $e) {
    Response::error($e->getMessage(), 500);
}
?>