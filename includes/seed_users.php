<?php
// includes/seed_users.php
require_once __DIR__ . '/db.php';

echo "🚀 Starting User Seeder...\n";

if (!isset($pdo)) {
    die("❌ Database connection variable \$pdo not found.\n");
}

$seederFile = __DIR__ . '/../database/seeders/001_users_seeder.sql';

if (!file_exists($seederFile)) {
    die("❌ Seeder file not found: $seederFile\n");
}

$sql = file_get_contents($seederFile);

try {
    // Use query() or exec()
    $stmt = $pdo->exec($sql);
    echo "✅ Seeder executed successfully.\n";
} catch (PDOException $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "✨ Seeding completed!\n";
?>
