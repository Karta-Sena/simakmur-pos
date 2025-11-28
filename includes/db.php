<?php
// FILE: includes/db.php
// Lokasi: folder includes/

// Memanggil config.php yang ada di folder luar (root)
require_once __DIR__ . '/../config.php';

try {
    // Menyusun Data Source Name (DSN)
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    
    // Opsi konfigurasi PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Error muncul sebagai Exception (mudah dibaca)
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Data diambil sebagai Array Associative
        PDO::ATTR_EMULATE_PREPARES   => false,                 // Security: Mencegah SQL Injection
    ];

    // Membuat objek koneksi PDO
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Jika koneksi gagal, hentikan program dan tampilkan pesan error
    // Tampilan error dibuat agak rapi sedikit
    die("
        <div style='font-family:sans-serif; padding:20px; border:1px solid red; background:#ffe6e6; color:red;'>
            <h3>❌ Gagal Terhubung ke Database!</h3>
            <p><strong>Pesan Error:</strong> " . $e->getMessage() . "</p>
            <p>Tips: Cek username, password, atau nama database di file <code>config.php</code>.</p>
        </div>
    ");
}
?>