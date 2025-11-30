<?php
// FILE: api/reports/stats.php
require_once '../../includes/db.php';
require_once '../../includes/response.php';

try {
    $today = date('Y-m-d');

    // 1. Hitung Omzet Hari Ini
    $sqlOmzet = "SELECT SUM(final_amount) as total, COUNT(id) as trx_count 
                 FROM transactions WHERE DATE(created_at) = ?";
    $stmt = $pdo->prepare($sqlOmzet);
    $stmt->execute([$today]);
    $omzet = $stmt->fetch();

    // 2. Hitung Menu Terlaris (Top 5)
    $sqlTop = "SELECT product_name, SUM(quantity) as qty 
               FROM transaction_items 
               JOIN transactions ON transaction_items.transaction_id = transactions.id
               WHERE DATE(transactions.created_at) = ?
               GROUP BY product_id 
               ORDER BY qty DESC LIMIT 5";
    $stmtTop = $pdo->prepare($sqlTop);
    $stmtTop->execute([$today]);
    $topProducts = $stmtTop->fetchAll();

    // 3. Data Grafik (Per Jam)
    $sqlChart = "SELECT HOUR(created_at) as jam, COUNT(id) as jumlah 
                 FROM transactions 
                 WHERE DATE(created_at) = ? 
                 GROUP BY HOUR(created_at)";
    $stmtChart = $pdo->prepare($sqlChart);
    $stmtChart->execute([$today]);
    $chartRaw = $stmtChart->fetchAll(PDO::FETCH_KEY_PAIR); // Output: [Jam => Jumlah]

    // Format Data Grafik (Isi jam kosong dengan 0)
    $chartData = [];
    $labels = [];
    for ($i = 8; $i <= 22; $i++) { // Buka jam 8 pagi sampai 10 malam
        $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ":00";
        $chartData[] = $chartRaw[$i] ?? 0;
    }

    Response::json([
        'omzet' => (int) $omzet['total'],
        'trx_count' => (int) $omzet['trx_count'],
        'top_products' => $topProducts,
        'chart' => [
            'labels' => $labels,
            'data' => $chartData
        ]
    ]);

} catch (Exception $e) {
    Response::error($e->getMessage());
}
?>