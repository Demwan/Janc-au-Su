<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

// Databaseverbinding
require_once __DIR__ . '/../db_connect.php'; // Now returns $conn as PDO instance
// Fix: assign $pdo for compatibility
$pdo = $conn;

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Gebruik PDO in plaats van mysqli
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone_number FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT street, city, postal_code, country FROM addresses WHERE user_id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "name" => $user['first_name'] . " " . $user['last_name'],
        "email" => $user['email'],
        "phone" => $user['phone_number'],
        "address" => $address ? "{$address['street']}\n{$address['postal_code']} {$address['city']}\n{$address['country']}" : "Geen adres beschikbaar"
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
