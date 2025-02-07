<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connect to database
require_once 'db_connect.php';

// Fetch products from the database
$query = "SELECT id, name, description, price, image_url FROM products";
$result = $conn->query($query);

if (!$result) {
    $errorInfo = $conn->errorInfo();
    error_log("Failed to fetch products: " . $errorInfo[2]);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch products: ' . $errorInfo[2]]);
    exit;
}

$products = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) { // replaced fetch_assoc() with fetch(PDO::FETCH_ASSOC)
    $products[] = $row;
}

// Output the products array directly
echo json_encode($products);
?>
