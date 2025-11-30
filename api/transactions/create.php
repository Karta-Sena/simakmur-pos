<?php
// FILE: api/transactions/create.php

require_once '../../includes/db.php';
require_once '../../includes/response.php';

// 1. Terima Data JSON dari Frontend
$input = json_decode(file_get_contents('php://input'), true);

// Validasi sederhana
if (empty($input['items']) || empty($input['total']) || empty($input['payment_method'])) {
    Response::error("Data transaksi tidak lengkap", 400);
}

try {
    // Mulai Transaksi Database (Atomic)
    $pdo->beginTransaction();

    // 2. Generate Nomor Transaksi (Format: TRX-YYMMDD-XXXX)
    $dateCode = date('ymd');
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

    // 3. Simpan ke Tabel TRANSACTIONS (Header)
    // Asumsi: cashier_id = 1 dulu (karena belum ada fitur login)
    $sqlHeader = "INSERT INTO transactions (transaction_number, cashier_id, total_amount, final_amount, payment_method, cash_received, change_amount, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'completed', NOW())";
    
    $stmtHeader = $pdo->prepare($sqlHeader);
    $stmtHeader->execute([
        $trxNumber,
        1, // Default Cashier ID
        $input['total'],
        $input['total'], // Final amount (bisa beda kalau ada diskon nanti)
        $input['payment_method'],
        $input['cash_received'] ?? $input['total'], // Kalau non-tunai, anggap uang pas
        $input['change_amount'] ?? 0
    ]);

    $trxId = $pdo->lastInsertId();

    // 4. Simpan ke Tabel TRANSACTION_ITEMS (Detail Menu)
    $sqlDetail = "INSERT INTO transaction_items (transaction_id, product_id, product_name, quantity, price, subtotal) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmtDetail = $pdo->prepare($sqlDetail);

    foreach ($input['items'] as $item) {
        $subtotal = $item['price'] * $item['qty'];
        $stmtDetail->execute([
            $trxId,
            $item['id'],
            $item['name'],
            $item['qty'],
            $item['price'],
            $subtotal
        ]);
        
        // Opsional: Kurangi Stok Produk di sini jika perlu
    }

    // Commit Simpan
    $pdo->commit();

    Response::json([
        'transaction_id' => $trxId,
        'transaction_number' => $trxNumber,
        'total' => $input['total']
    ], 'Transaksi Berhasil Disimpan');

} catch (Exception $e) {
    // Rollback jika ada error (Batal simpan semua)
    $pdo->rollBack();
    Response::error("Gagal menyimpan transaksi: " . $e->getMessage(), 500);
}
?>