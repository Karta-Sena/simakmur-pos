<?php
// FILE: api/reports/daily.php

require_once '../../includes/db.php';
require_once '../../includes/response.php';

// Cek Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$date = $_GET['date'] ?? date('Y-m-d');

try {
    // 1. Total Penjualan Hari Ini
    $stmtTotal = $pdo->prepare("SELECT SUM(total_amount) as total_sales, COUNT(*) as total_trx FROM transactions WHERE DATE(created_at) = ? AND status = 'completed'");
    $stmtTotal->execute([$date]);
    $summary = $stmtTotal->fetch();

    // 2. Penjualan per Metode Pembayaran
    $stmtMethods = $pdo->prepare("SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total FROM transactions WHERE DATE(created_at) = ? AND status = 'completed' GROUP BY payment_method");
    $stmtMethods->execute([$date]);
    $methods = $stmtMethods->fetchAll();

    // 3. Produk Terlaris Hari Ini
    $stmtTop = $pdo->prepare("
        SELECT product_name, SUM(quantity) as qty, SUM(subtotal) as revenue 
        FROM transaction_items ti
        JOIN transactions t ON ti.transaction_id = t.id
        WHERE DATE(t.created_at) = ? AND t.status = 'completed'
        GROUP BY product_id
        ORDER BY qty DESC
        LIMIT 5
    ");
    $stmtTop->execute([$date]);
    $topProducts = $stmtTop->fetchAll();

    Response::json([
        'date' => $date,
        'total_sales' => (float)($summary['total_sales'] ?? 0),
        'total_transactions' => (int)($summary['total_trx'] ?? 0),
        'payment_methods' => $methods,
        'top_products' => $topProducts
    ], "Laporan Harian Berhasil Diambil");

} catch (Exception $e) {
    Response::error("Gagal mengambil laporan: " . $e->getMessage(), 500);
}
?>
