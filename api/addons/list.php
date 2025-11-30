<?php
error_reporting(0);
// FILE: api/addons/list.php
// Get all active addons

// Use same includes as products API
require_once '../../includes/db.php';
require_once '../../includes/response.php';

try {
    $sql = "SELECT id, name, type, price FROM addons WHERE is_active = 1 ORDER BY type, id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $addons = $stmt->fetchAll();
    
    // Format data
    $formattedAddons = [];
    foreach ($addons as $addon) {
        $formattedAddons[] = [
            'id' => (int)$addon['id'],
            'name' => $addon['name'],
            'type' => $addon['type'],
            'price' => (float)$addon['price']
        ];
    }
    
    // Send JSON response using Response helper
    Response::json($formattedAddons, 'Addons loaded successfully');
    
} catch (Exception $e) {
    Response::error($e->getMessage(), 500);
}
?>
