<?php
// Konfigurasi utama aplikasi - Load dari .env
// PENTING: Generate APP_KEY dengan: php includes/generate_key.php

require_once __DIR__ . '/includes/env_loader.php';

try {
    EnvLoader::load(__DIR__ . '/.env');
    EnvLoader::validateRequired(['DB_HOST', 'DB_NAME', 'DB_USER', 'APP_KEY']);
} catch (Exception $e) {
    die("⚠️ Configuration Error: " . $e->getMessage() . 
        "\n\n💡 Hint: Copy .env.example ke .env dan generate APP_KEY");
}

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3307'));
define('DB_NAME', env('DB_NAME', 'simakmur_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

// Application Configuration
define('APP_NAME', env('APP_NAME', 'SiMakmur POS'));
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', env('APP_DEBUG', true));
define('APP_KEY', env('APP_KEY'));

// Auto-detect Base URL
if (!empty(env('APP_URL'))) {
    define('BASE_URL', env('APP_URL'));
} else {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $path = isset($_SERVER['SCRIPT_NAME']) ? str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']) : '/';
    define('BASE_URL', $protocol . $domain . $path);
}

// Security Configuration
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 3600));
define('HASH_COST', env('HASH_COST', 12));
define('CSRF_TOKEN_EXPIRY', env('CSRF_TOKEN_EXPIRY', 7200));

// File Upload Configuration
define('MAX_UPLOAD_SIZE', env('MAX_UPLOAD_SIZE', 5) * 1024 * 1024);
define('ALLOWED_IMAGE_EXTENSIONS', explode(',', env('ALLOWED_IMAGE_EXTENSIONS', 'jpg,jpeg,png,gif,webp')));
define('UPLOAD_PATH', __DIR__ . '/uploads/');

// Timezone
date_default_timezone_set(env('TIMEZONE', 'Asia/Jakarta'));

// Error Reporting
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// CSRF Token Functions
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

function validateCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRY) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Encryption Functions
function encryptData($data) {
    $key = hash('sha256', APP_KEY);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decryptData($data) {
    $key = hash('sha256', APP_KEY);
    list($encrypted, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}
?>