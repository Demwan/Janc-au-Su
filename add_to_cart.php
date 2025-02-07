<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Start the session to access user information
session_start();

// Set the appropriate header for JSON response
header('Content-Type: application/json');

$response = [];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['success'] = false;
    $response['error'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

// Connect to database
require_once 'db_connect.php'; // $conn is a PDO instance

// Get the raw POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Validate the received data
if (!isset($data['productId'], $data['size'], $data['quantity'])) {
    $response['success'] = false;
    $response['error'] = 'Invalid input data.';
    echo json_encode($response);
    exit;
}

$productId = strval($data['productId']);
$size = $data['size'];
$quantity = intval($data['quantity']);
$userId = $_SESSION['user_id'];

try {
    // Fetch the current cart from the `users` table
    $query = "SELECT cart FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cart = $row['cart'] ? json_decode($row['cart'], true) : [];

    // Update the cart array
    $productExists = false;
    foreach ($cart as &$item) {
        if ($item['productId'] === $productId && $item['size'] === $size) {
            $item['quantity'] += $quantity; // Increment quantity if product+size exists
            $productExists = true;
            break;
        }
    }

    if (!$productExists) {
        // Add new product with selected size and quantity
        $cart[] = ['productId' => $productId, 'size' => $size, 'quantity' => $quantity];
    }

    // Save the updated cart back to the `users` table
    $cartJson = json_encode($cart);
    $updateQuery = "UPDATE users SET cart = :cart WHERE user_id = :user_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute([':cart' => $cartJson, ':user_id' => $userId]);

    $response['success'] = true;
    $response['message'] = 'Cart updated successfully.';
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>
