<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to database using PDO
require_once 'db_connect.php'; // db_connect.php now returns a PDO instance in $conn

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "Invalid product ID"]);
    exit();
}
$product_id = intval($_GET['id']);

// Prepare and execute the PDO query to fetch product details including available sizes
$sql = "SELECT id, name, description, price, image_url, available_sizes FROM products WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $product_id, PDO::PARAM_INT);

if (!$stmt->execute()) {
    $errorInfo = $stmt->errorInfo();
    echo json_encode(["error" => "Database query failed: " . $errorInfo[2]]);
    exit();
}

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    $sizes = isset($product['available_sizes']) ? explode(',', $product['available_sizes']) : [];
    $product['sizes'] = $sizes;
    echo json_encode($product);
} else {
    echo json_encode(["error" => "Product not found"]);
}
?>
