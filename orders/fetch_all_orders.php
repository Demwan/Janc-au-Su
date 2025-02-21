<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth_helper.php';

header('Content-Type: application/json');
requireStaffAccess($conn);

try {
    $stmt = $conn->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            o.status,
            u.email,
            COUNT(oi.order_item_id) as number_of_items,
            SUM(oi.quantity * oi.price) as total_amount
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN order_items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ");
    
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($orders);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
} 