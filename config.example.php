<?php
// FILE: config.php
// Lokasi: Root folder (simakmur-pos/config.php)

// --------------------------------------------------------------------------
// 1. PENGATURAN DATABASE
// --------------------------------------------------------------------------
// Sesuaikan dengan settingan XAMPP kamu
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');       // Default XAMPP biasanya kosong
define('DB_NAME', 'simakmur_db'); 

// PENTING: Cek port MySQL di XAMPP Control Panel
// Jika tertulis 3306, biarkan 3306. 
// Jika di SQL Dump kemarin 3307, ganti jadi 3307.
define('DB_PORT', '3307'); 

// --------------------------------------------------------------------------
// 2. PENGATURAN URL APLIKASI
// --------------------------------------------------------------------------
// Ini mendeteksi otomatis URL website (http://localhost/simakmur-pos/)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
// Mengambil path folder project secara dinamis
$path = str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

define('BASE_URL', $protocol . $domain . $path);

// --------------------------------------------------------------------------
// 3. PENGATURAN ZONA WAKTU
// --------------------------------------------------------------------------
// Wajib agar jam di struk kasir sesuai dengan WIB
date_default_timezone_set('Asia/Jakarta');

// --------------------------------------------------------------------------
// 4. ERROR REPORTING (Mode Developer)
// --------------------------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>