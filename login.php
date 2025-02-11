<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Connect to database
require_once 'db_connect.php';
$pdo = $conn;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query to find the user by email
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);

    try {
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and passwords match
        if ($user && password_verify($password, $user['password_hash'])) {
            // Start session and store user data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];

            // Get cart from cookies
            $cookieCart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

            // Get cart from database
            $dbCart = json_decode($user['cart'], true);

            // Merge carts
            $mergedCart = $dbCart;
            foreach ($cookieCart as $cookieItem) {
                $found = false;
                foreach ($mergedCart as &$dbItem) {
                    if ($dbItem['productId'] === $cookieItem['productId'] && $dbItem['size'] === $cookieItem['size']) {
                        $dbItem['quantity'] += $cookieItem['quantity'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $mergedCart[] = $cookieItem;
                }
            }

            // Update the cart in the database
            $mergedCartJson = json_encode($mergedCart);
            $updateQuery = "UPDATE users SET cart = :cart WHERE user_id = :user_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':cart', $mergedCartJson);
            $updateStmt->bindParam(':user_id', $user['user_id']);
            $updateStmt->execute();

            // Clear the cart cookie
            setcookie('cart', '', time() - 3600, '/');


            header("Location: dashboard.php");
            echo "Login successful! Redirecting...";
            exit();
        } else {
            // Redirect to login with an error message
            $errorMessage = urlencode("Invalid email or password.");
            header("Location: login.html?error=$errorMessage");
            exit();
        }
    } catch (PDOException $e) {
        // Redirect with database error
        $errorMessage = urlencode("An error occurred: " . $e->getMessage());
        header("Location: login.html?error=$errorMessage");
        exit();
    }
} else {
    // Redirect with invalid request message
    $errorMessage = urlencode("Invalid request.");
    header("Location: login.html?error=$errorMessage");
    exit();
}
?>
