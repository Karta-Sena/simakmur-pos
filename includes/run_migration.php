<?php
// includes/run_migration.php
require_once __DIR__ . '/db.php';

echo "🚀 Starting Database Migration...\n";

if (!isset($pdo)) {
    die("❌ Database connection variable \$pdo not found.\n");
}

$migrationFile = __DIR__ . '/../database/migrations/001_create_transactions_table.sql';

if (!file_exists($migrationFile)) {
    die("❌ Migration file not found: $migrationFile\n");
}

$sql = file_get_contents($migrationFile);

// Split SQL by semicolon to execute multiple queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

foreach ($queries as $query) {
    if (empty($query)) continue;
    
    try {
        // Use query() instead of exec() for CREATE TABLE to catch errors better
        $stmt = $pdo->query($query);
        echo "✅ Query executed successfully.\n";
    } catch (PDOException $e) {
        // Ignore "Table already exists" error
        if (strpos($e->getMessage(), 'already exists') !== false) {
             echo "⚠️ Table already exists (Skipping).\n";
        } else {
             echo "❌ Exception: " . $e->getMessage() . "\n";
             echo "Query: " . substr($query, 0, 100) . "...\n";
        }
    }
}

echo "✨ Migration completed!\n";
?>
