<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Start the session
session_start();

// Hard-code user_id to 1 (for testing without a login system)
$user_id = $_SESSION['user_id'];

// Connect to database using PDO
require_once 'db_connect.php'; // $conn is a PDO instance

// Fetch the order ID from the query string
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch order details
$sql = "SELECT o.order_id, o.total_amount, o.payment_provider, a.street, a.city, a.postal_code, a.country 
        FROM orders o
        JOIN addresses a ON o.address_id = a.address_id
        WHERE o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found or you do not have permission to view this order.";
    exit();
}

// Fetch order items
$sql_items = "SELECT oi.quantity, p.NAME, oi.price, oi.size 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2d2d2d;
        }
        h3 {
            color: #555;
        }
        .order-details, .order-items {
            margin-top: 30px;
        }
        .order-details p {
            font-size: 16px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thank You for Your Order!</h1>
        <div class="order-details">
            <h3>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h3>
            <p><strong>Total Amount:</strong> $<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_provider']); ?></p>
            
            <h3>Shipping Address</h3>
            <p><strong>Street:</strong> <?php echo htmlspecialchars($order['street']); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($order['postal_code']); ?></p>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($order['country']); ?></p>
        </div>

        <div class="order-items">
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="total-amount">
            <p><strong>Total: </strong>$<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></p>
        </div>
    </div>
</body>
</html>
