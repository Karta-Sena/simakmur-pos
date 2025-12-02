<?php
// FILE: api/transactions/create.php

require_once '../../includes/db.php';
require_once '../../includes/response.php';

// 1. Terima Data JSON dari Frontend
$input = json_decode(file_get_contents('php://input'), true);

// Validasi sederhana
if (empty($input['items']) || !isset($input['total']) || empty($input['payment_method'])) {
    Response::error("Data transaksi tidak lengkap", 400);
}

try {
    // Mulai Transaksi Database (Atomic)
    $pdo->beginTransaction();

    // 2. Generate Nomor Transaksi (Format: TRX-YYYYMMDD-XXXX)
    $dateCode = date('Ymd');
    $prefix = "TRX-{$dateCode}-";
    
    // Cari nomor urut terakhir hari ini
    $stmt = $pdo->prepare("SELECT transaction_number FROM transactions WHERE transaction_number LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $lastTrx = $stmt->fetchColumn();

    if ($lastTrx) {
        $lastNo = (int) substr($lastTrx, -4);
        $nextNo = $lastNo + 1;
    } else {
        $nextNo = 1;
    }
    
    $trxNumber = $prefix . str_pad($nextNo, 4, '0', STR_PAD_LEFT);

    // 3. Hitung Ulang Total (Security Check)
    $subtotal = 0;
    foreach ($input['items'] as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }
    
    // Tax 10%
    $tax = $subtotal * 0.1;
    $totalCalculated = $subtotal + $tax;
    
    // Toleransi perbedaan floating point (opsional, tapi good practice)
    // if (abs($totalCalculated - $input['total']) > 100) { ... }

    // 4. Simpan ke Tabel TRANSACTIONS (Header)
    $sqlHeader = "INSERT INTO transactions (
        transaction_number, 
        cashier_id, 
        customer_type,
        payment_method, 
        subtotal,
        tax,
        total_amount, 
        cash_received, 
        change_amount, 
        status, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())";
    
    $stmtHeader = $pdo->prepare($sqlHeader);
    $stmtHeader->execute([
        $trxNumber,
        $input['cashier_id'] ?? 1, // Default to 1 if not set (temporary until auth)
        $input['customer_type'] ?? 'walk-in',
        $input['payment_method'],
        $subtotal,
        $tax,
        $totalCalculated,
        $input['cash_received'] ?? $totalCalculated,
        $input['change_amount'] ?? 0
    ]);

    $trxId = $pdo->lastInsertId();

    // 5. Simpan ke Tabel TRANSACTION_ITEMS (Detail Menu)
    $sqlDetail = "INSERT INTO transaction_items (transaction_id, product_id, product_name, quantity, product_price, subtotal) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmtDetail = $pdo->prepare($sqlDetail);

    foreach ($input['items'] as $item) {
        $itemSubtotal = $item['price'] * $item['qty'];
        $stmtDetail->execute([
            $trxId,
            $item['id'],
            $item['name'],
            $item['qty'],
            $item['price'],
            $itemSubtotal
        ]);
        
        // TODO: Kurangi Stok Produk di sini
    }

    // Commit Simpan
    $pdo->commit();

    Response::json([
        'transaction_id' => $trxId,
        'transaction_number' => $trxNumber,
        'subtotal' => $subtotal,
        'tax' => $tax,
        'total' => $totalCalculated,
        'change' => $input['change_amount'] ?? 0,
        'timestamp' => date('Y-m-d H:i:s')
    ], 'Transaksi Berhasil Disimpan');

} catch (Exception $e) {
    // Rollback jika ada error (Batal simpan semua)
    $pdo->rollBack();
    Response::error("Gagal menyimpan transaksi: " . $e->getMessage(), 500);
}
?>