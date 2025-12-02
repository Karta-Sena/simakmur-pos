<?php
// FILE: api/transactions/history.php

require_once '../../includes/db.php';
require_once '../../includes/response.php';

// Cek Session (Opsional: jika ingin proteksi endpoint ini)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil Parameter Filter
$date = $_GET['date'] ?? date('Y-m-d'); // Default hari ini
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$search = $_GET['search'] ?? '';

try {
    // Query Dasar
    $sql = "SELECT t.*, u.full_name as cashier_name 
            FROM transactions t 
            LEFT JOIN users u ON t.cashier_id = u.id 
            WHERE 1=1";
    
    $params = [];

    // Filter Tanggal (jika ada)
    if (!empty($date)) {
        $sql .= " AND DATE(t.created_at) = ?";
        $params[] = $date;
    }

    // Filter Pencarian (No Transaksi)
    if (!empty($search)) {
        $sql .= " AND t.transaction_number LIKE ?";
        $params[] = "%$search%";
    }

    // Sorting & Pagination
    $sql .= " ORDER BY t.created_at DESC LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();

    // Hitung Total (untuk pagination info)
    // (Simplified count query)
    
    // Format Data
    $data = array_map(function($row) {
        return [
            'id' => $row['id'],
            'transaction_number' => $row['transaction_number'],
            'cashier_name' => $row['cashier_name'] ?? 'Unknown',
            'total_amount' => (float)$row['total_amount'],
            'payment_method' => $row['payment_method'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            // Items bisa di-fetch terpisah jika detail view diminta
        ];
    }, $transactions);

    Response::json($data, "Data Transaksi Berhasil Diambil");

} catch (Exception $e) {
    Response::error("Gagal mengambil riwayat: " . $e->getMessage(), 500);
}
?>
