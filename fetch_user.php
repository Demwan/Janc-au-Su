<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Connect to database
require_once 'db_connect.php'; // $conn is a PDO instance

if (!isset($_SESSION['user_id'])) {
    echo "<script> location.href='new_url'; </script>";
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id']; // Retrieve user_id from session
$stmt = $conn->prepare('SELECT first_name, last_name FROM users WHERE user_id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($user);
?>
