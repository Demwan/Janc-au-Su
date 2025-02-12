<?php
require_once __DIR__ . '/../db_connect.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$new_status = isset($_POST['new_status']) ? $_POST['new_status'] : '';

if (!$order_id || empty($new_status)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters.']);
    exit;
}


$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$stmt->execute([$new_status, $order_id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'new_status' => $new_status]);
} else {
    echo json_encode(['error' => 'Order not updated.']);
}
?>
