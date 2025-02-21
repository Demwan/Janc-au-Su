<?php
require_once '../db_connect.php';
require_once '../auth_helper.php';

// Check if user is admin
$stmt = $conn->prepare("SELECT type FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isLoggedIn() || $user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch product details
    $product_id = $_GET['id'] ?? null;
    
    if (!$product_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Product ID required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    echo json_encode($product);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update product details
    $data = json_decode(file_get_contents('php://input'), true);
    
    $product_id = $data['id'] ?? null;
    if (!$product_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Product ID required']);
        exit;
    }

    $fields = [
        'name' => 'string',
        'description' => 'string',
        'price' => 'float',
        'image_url' => 'string',
        'secondary_image_url' => 'string',
        'type' => 'string',
        'gender' => 'string',
        'available_sizes' => 'string'
    ];

    $updates = [];
    $params = [];
    foreach ($fields as $field => $type) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            if ($type === 'float') {
                $params[] = floatval($data[$field]);
            } else {
                $params[] = $data[$field];
            }
        }
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }

    $params[] = $product_id;
    $query = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ?";
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update product']);
    }
}
?> 