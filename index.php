<?php
// FILE: index.php (Sementara untuk Test)
// Lokasi: Root folder

require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SiMakmur Setup Check</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 400px; text-align: center; border-top: 5px solid #6B1C23; }
        h1 { color: #6B1C23; margin: 0 0 10px 0; }
        .status-box { background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-top: 20px; font-weight: bold; border: 1px solid #a7f3d0; }
        .details { text-align: left; margin-top: 20px; font-size: 13px; color: #555; background: #fafafa; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
    </style>
</head>
<body>

    <div class="card">
        <h1>SiMakmur POS</h1>
        <p>System Health Check</p>

        <?php if(isset($pdo)): ?>
            <div class="status-box">
                ✅ Database Terhubung!
            </div>
            
            <div class="details">
                <strong>Host:</strong> <?= DB_HOST ?><br>
                <strong>Port:</strong> <?= DB_PORT ?><br>
                <strong>Database:</strong> <?= DB_NAME ?><br>
                <strong>Base URL:</strong> <?= BASE_URL ?><br>
            </div>
            
            <p style="margin-top: 25px; color: #6B1C23; font-weight: 600;">
                Siap lanjut ke Fase Migrasi CSS! 🚀
            </p>
        <?php endif; ?>
    </div>

</body>
</html>