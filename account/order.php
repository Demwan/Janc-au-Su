<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

try {
    // Verify the order belongs to the user
    $stmt = $conn->prepare("
        SELECT o.*, a.* 
        FROM orders o 
        JOIN addresses a ON o.address_id = a.address_id 
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(403);
        echo json_encode(['error' => 'Order not found or access denied']);
        exit;
    }

    // Fetch order items
    $stmt = $conn->prepare("
        SELECT oi.*, p.name, p.description, p.price, p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process images for each item
    foreach ($items as &$item) {
        $images = json_decode($item['image_url'], true);
        $item['image'] = is_array($images) && !empty($images) ? $images[0] : null;
    }

    // Return order data
    echo json_encode([
        'order' => $order,
        'items' => $items,
        'user' => [
            'email' => $_SESSION['user_email'] ?? ''
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
