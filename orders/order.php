<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth_helper.php';

header('Content-Type: application/json');
requireStaffAccess($conn);

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing order_id parameter."]);
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    http_response_code(404);
    echo json_encode(["error" => "Order not found."]);
    exit;
}

// Fetch address details
$stmt = $conn->prepare("SELECT * FROM addresses WHERE address_id = ?");
$stmt->execute([$order['address_id']]);
$address = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch order items with product details, including size and image_url
$stmt = $conn->prepare("
    SELECT oi.quantity, oi.size, p.name, p.description, p.price, p.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process each order item to extract the first image from the image_url JSON array
foreach($order_items as &$item) {
    $images = json_decode($item['image_url'], true);
    $item['first_image'] = (is_array($images) && count($images) > 0) ? $images[0] : $item['image_url'];
}
unset($item);

echo json_encode([
    "order" => $order,
    "address" => $address,
    "items" => $order_items,
]);
?>
