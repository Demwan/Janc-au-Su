<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: /login");
  exit();}

$user_id = $_SESSION['user_id'];

// Connect to database using PDO
require_once 'db_connect.php'; // $conn is a PDO instance

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];
    $payment_provider = $_POST['payment_provider'];
    $cart_data = $_POST['cart_data'];

    $cart = json_decode($cart_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Invalid cart data");
    }

    // Check if the address already exists
    $stmt = $conn->prepare("SELECT address_id FROM addresses WHERE user_id = ? AND street = ? AND city = ? AND postal_code = ? AND country = ?");
    $stmt->execute([$user_id, $address, $city, $postal_code, $country]);
    $addressRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($addressRow) {
        $address_id = $addressRow['address_id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, street, city, postal_code, country) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $address, $city, $postal_code, $country]);
        $address_id = $conn->lastInsertId();
    }

    $total_amount = 0;
    $order_items = [];

    foreach ($cart as $item) {
        $product_id = $item['productId'];
        $size = $item['size'];
        $quantity = $item['quantity'];

        // Fetch product price using PDO
        $product_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $product_stmt->execute([$product_id]);
        $price = $product_stmt->fetchColumn();

        if (!$price) {
            die("Product price not found for product ID: $product_id");
        }

        $total_amount += $price * $quantity;
        $order_items[] = ['product_id' => $product_id, 'quantity' => $quantity, 'price' => $price, 'size' => $size];
    }

    // Insert order
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, address_id, total_amount, payment_provider) VALUES (?, ?, ?, ?)");
    $order_stmt->execute([$user_id, $address_id, $total_amount, $payment_provider]);
    $order_id = $conn->lastInsertId();

    // Insert order items
    foreach ($order_items as $item) {
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
        $item_stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['size']]);
    }

    // Clear the cart cookie
    setcookie('cart', '', time() - 3600, '/');

    // Redirect to thank you page
    header('Location: thank_you.php?order_id=' . $order_id);
    exit();

} else {
    echo "Invalid request method.";
}

$conn->close();
?>
