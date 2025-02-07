<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User is not logged in']);
    http_response_code(401);  // Unauthorized
    exit();
}

// Connect to database using PDO
require_once 'db_connect.php'; // $conn is a PDO instance

// Get the user_id from session
$user_id = $_SESSION['user_id'];

// Using PDO prepared statement
$stmt = $conn->prepare("SELECT street, city, postal_code, country FROM addresses WHERE user_id = ?");
$stmt->execute([$user_id]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($addresses);
?>
