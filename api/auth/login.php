<?php
// FILE: api/auth/login.php

require_once '../../includes/db.php';
require_once '../../includes/response.php';

// 1. Terima Data JSON
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['username']) || empty($input['password'])) {
    Response::error("Username dan password wajib diisi", 400);
}

try {
    // 2. Cari User di Database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$input['username']]);
    $user = $stmt->fetch();

    // 3. Verifikasi Password
    if ($user && password_verify($input['password'], $user['password'])) {
        
        // Cek Role (Hanya cashier yang boleh login via endpoint ini, atau admin juga boleh)
        if ($user['role'] !== 'cashier' && $user['role'] !== 'admin') {
            Response::error("Akses ditolak. Bukan akun kasir.", 403);
        }

        // 4. Set Session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();

        // Response Sukses
        Response::json([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role']
        ], "Login Berhasil");

    } else {
        Response::error("Username atau password salah", 401);
    }

} catch (Exception $e) {
    Response::error("Terjadi kesalahan server: " . $e->getMessage(), 500);
}
?>
