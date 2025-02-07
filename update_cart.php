<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Debug raw input
$input = file_get_contents('php://input');

// Decode JSON payload
$data = json_decode($input, true);

// Validate JSON structure
if (!isset($data['cart']) || !is_array($data['cart'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON format']);
    exit;
}

// Include PDO connection (adjust path as needed)
require_once __DIR__ . '/db_connect.php';
// Fix: assign $pdo for compatibility with existing code
$pdo = $conn;

// Assume user is logged in and their ID is available
// Replace with your session or authentication logic
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Convert the cart array to JSON
$cartJson = json_encode($data['cart']);

// Update the user's cart in the database using PDO
$query = "UPDATE users SET cart = :cart WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
    exit;
}
$stmt->bindValue(':cart', $cartJson, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to execute statement']);
    exit;
}

// Success response
echo json_encode(['success' => true]);
?>
