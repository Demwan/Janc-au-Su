<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo("You are not logged in.");
  http_response_code(401);  // Unauthorized
  exit();
  }

$user_id = $_SESSION['user_id'];

try {
    // Fetch all orders for the user
    $stmt = $conn->prepare("
        SELECT o.*, a.street, a.city, a.postal_code, a.country,
        (SELECT SUM(oi.quantity * p.price)
         FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id = o.order_id) as total
        FROM orders o
        JOIN addresses a ON o.address_id = a.address_id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'orders' => $orders,
        'user' => [
            'email' => $_SESSION['user_email'] ?? ''
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
