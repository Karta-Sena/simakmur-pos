<?php
// FILE: api/products/create.php
require_once '../../includes/db.php';
require_once '../../includes/response.php';

// 1. Cek Request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method Not Allowed', 405);
}

// 2. Ambil Data Form
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? 0;
$categoryId = $_POST['category_id'] ?? 1;

if (empty($name) || empty($price)) {
    Response::error('Nama dan Harga wajib diisi');
}

try {
    // 3. Handle Upload Gambar
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../../uploads/products/";
        
        // Buat folder jika belum ada
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        // Generate nama file unik (biar gak bentrok)
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = "MENU-" . time() . "." . $ext;
        $targetFile = $targetDir . $imageName;

        // Pindahkan file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            throw new Exception("Gagal upload gambar.");
        }
    }

    // 4. Simpan ke Database
    $sql = "INSERT INTO products (name, category_id, price, image, is_active, stock) VALUES (?, ?, ?, ?, 1, 100)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $categoryId, $price, $imageName]);

    Response::json(['id' => $pdo->lastInsertId()], 'Menu berhasil ditambahkan');

} catch (Exception $e) {
    Response::error($e->getMessage());
}
?>