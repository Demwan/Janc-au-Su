<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect to database
require_once 'db_connect.php';
$pdo = $conn;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password confirmation
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Check if email already exists
    $checkEmail = "SELECT COUNT(*) FROM users WHERE email = :email";
    $checkStmt = $pdo->prepare($checkEmail);
    $checkStmt->execute([':email' => $email]);
    
    if ($checkStmt->fetchColumn() > 0) {
        die("This email address is already registered. Please use a different email or try logging in.");
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL query
    $query = "INSERT INTO users (email, password_hash, created_at, first_name, last_name, phone_number, cart) 
              VALUES (:email, :password_hash, NOW(), :first_name, :last_name, NULL, NULL)";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute([
            ':email' => $email,
            ':password_hash' => $password_hash,
            ':first_name' => $first_name,
            ':last_name' => $last_name
        ]);
        header("Location: login.html");
    } catch (PDOException $e) {
        die("An error occurred: " . $e->getMessage());
    }
} else {
    echo "Invalid request.";
}
?>
