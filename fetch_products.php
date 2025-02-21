<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connect to database
require_once 'db_connect.php';

// Build the query based on filters
$query = "SELECT id, name, description, price, image_url FROM products WHERE 1=1";
$params = [];

if (isset($_GET['gender']) && $_GET['gender'] !== 'all') {
    $query .= " AND gender = ?";
    $params[] = $_GET['gender'];
}

if (isset($_GET['type']) && $_GET['type'] !== 'all') {
    $query .= " AND type = ?";
    $params[] = $_GET['type'];
}

// Add sorting
if (isset($_GET['sort'])) {
    switch($_GET['sort']) {
        case 'price_asc':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $query .= " ORDER BY price DESC";
            break;
        case 'name_asc':
            $query .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY name DESC";
            break;
        default:
            $query .= " ORDER BY id ASC"; // Default sorting
    }
} else {
    $query .= " ORDER BY id ASC"; // Default sorting when no sort parameter
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output the products array
echo json_encode($products);
?>
