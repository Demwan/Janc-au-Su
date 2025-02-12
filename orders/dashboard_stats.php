<?php
include __DIR__ . '/../db_connect.php';

// Query for total revenue from all orders
$stmt1 = $conn->prepare("SELECT SUM(total_amount) AS total_revenue FROM orders");
$stmt1->execute();
$totalRevenue = $stmt1->fetch(PDO::FETCH_ASSOC)['total_revenue'];

// Query for today's sales (sum total_amount)
$stmt2 = $conn->prepare("SELECT SUM(total_amount) AS todays_sales FROM orders WHERE DATE(order_date) = CURDATE()");
$stmt2->execute();
$todaysSales = $stmt2->fetch(PDO::FETCH_ASSOC)['todays_sales'];

// Count orders not processed (Pending)
$stmt3 = $conn->prepare("SELECT COUNT(*) AS orders_not_processed FROM orders WHERE status = 'Pending'");
$stmt3->execute();
$ordersNotProcessed = $stmt3->fetch(PDO::FETCH_ASSOC)['orders_not_processed'];

// Count orders awaiting shipment (Processing)
$stmt4 = $conn->prepare("SELECT COUNT(*) AS orders_awaiting_shipment FROM orders WHERE status = 'Processing'");
$stmt4->execute();
$ordersAwaitingShipment = $stmt4->fetch(PDO::FETCH_ASSOC)['orders_awaiting_shipment'];

header('Content-Type: application/json');
echo json_encode([
    'total_revenue' => $totalRevenue ? number_format($totalRevenue, 2) : "0.00",
    'todays_sales' => $todaysSales ? number_format($todaysSales, 2) : "0.00",
    'orders_not_processed' => $ordersNotProcessed,
    'orders_awaiting_shipment' => $ordersAwaitingShipment
]);
?>
