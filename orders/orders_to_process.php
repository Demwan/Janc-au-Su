<?php
include __DIR__ . '/../db_connect.php';

// Query updated to include order_id
$query = "SELECT o.order_id, o.order_date, u.email, SUM(oi.quantity) AS number_of_items, o.total_amount 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          JOIN order_items oi ON o.order_id = oi.order_id 
          WHERE o.status = 'Pending'
          GROUP BY o.order_id";
$stmt = $conn->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($orders);
?>
