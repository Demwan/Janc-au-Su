<?php
session_start();
$user_id = $_SESSION['user_id']; // Retrieve user_id from session

require_once 'db_connect.php'; // $conn is a PDO instance

if (!isset($_SESSION['user_id'])) {
    die('User is not logged in.');
}

$stmt = $conn->prepare("SELECT cart FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_json = $stmt->fetchColumn();

echo $cart_json;
?>