<?php
// Generate secure APP_KEY untuk enkripsi
// CARA PAKAI: php includes/generate_key.php

function generateSecureKey($length = 64) {
    $bytes = random_bytes($length / 2);
    return bin2hex($bytes);
}

function updateEnvFile($key) {
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($envPath)) {
        echo "❌ Error: .env file tidak ditemukan!\n";
        echo "💡 Hint: Copy .env.example menjadi .env terlebih dahulu\n";
        return false;
    }
    
    $content = file_get_contents($envPath);
    $pattern = '/^APP_KEY=.*$/m';
    $replacement = "APP_KEY={$key}";
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent === null || $newContent === $content) {
        echo "⚠️  Warning: APP_KEY tidak ditemukan di .env\n";
        echo "💡 Hint: Tambahkan baris 'APP_KEY=' di .env file\n";
        return false;
    }
    
    file_put_contents($envPath, $newContent);
    return true;
}

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║         SIMAKMUR POS - APP_KEY GENERATOR                   ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

try {
    $key = generateSecureKey(64);
    
    echo "✅ APP_KEY berhasil di-generate!\n\n";
    echo "🔑 Your new APP_KEY:\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo $key . "\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    echo "📝 Update .env file otomatis? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($response) === 'y' || strtolower($response) === 'yes') {
        if (updateEnvFile($key)) {
            echo "✅ .env file berhasil di-update!\n";
            echo "💾 APP_KEY sudah tersimpan di .env\n";
        }
    } else {
        echo "\n📋 Manual Update Instructions:\n";
        echo "1. Buka file .env\n";
        echo "2. Cari baris APP_KEY=\n";
        echo "3. Ganti dengan: APP_KEY={$key}\n";
        echo "4. Save file\n";
    }
    
    echo "\n⚠️  PENTING:\n";
    echo "• Jangan share APP_KEY ke siapapun!\n";
    echo "• Jangan commit .env ke Git!\n";
    echo "• Gunakan APP_KEY yang berbeda untuk production!\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
