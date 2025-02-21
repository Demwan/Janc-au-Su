<?php
include __DIR__ . '/../db_connect.php';
include __DIR__ . '/../auth_helper.php';

header('Content-Type: application/json');
requireStaffAccess($conn);

// Query to fetch today's sales joining orders and users tables
$stmt = $conn->prepare("SELECT o.status, u.email, o.total_amount FROM orders o JOIN users u ON o.user_id = u.user_id WHERE DATE(o.order_date) = CURDATE()");
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output as JSON
echo json_encode($sales);
?>
