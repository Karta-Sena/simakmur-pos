<?php
require_once '../../includes/db.php';
require_once '../../includes/response.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;

if (!$id) Response::error("ID tidak valid");

try {
    // Soft Delete (Cuma set non-aktif) atau Hard Delete (Hapus Permanen)
    // Kita pakai Hard Delete biar bersih
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    Response::json(null, "Menu dihapus");
} catch (Exception $e) {
    Response::error($e->getMessage());
}
?>