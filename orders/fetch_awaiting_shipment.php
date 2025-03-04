<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth_helper.php';

requireStaffAccess($conn);

try {
    $stmt = $conn->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            u.email,
            a.country,
            SUM(oi.quantity * p.price) as total_amount
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN addresses a ON o.address_id = a.address_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.status = 'Processing'
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