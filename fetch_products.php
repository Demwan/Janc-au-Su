<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connect to database
require_once 'db_connect.php';
require_once 'auth_helper.php';

// Check if this is an admin request (from the admin dashboard)
$isAdminRequest = strpos($_SERVER['HTTP_REFERER'] ?? '', '/orders/') !== false;

if ($isAdminRequest) {
    // Verify admin access for admin dashboard requests
    $stmt = $conn->prepare("SELECT type FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!isLoggedIn() || $user['type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    // Admin view (limited fields)
    $fields = "id, name, price, type, gender";
} else {
    // Customer view (all necessary fields)
    $fields = "id, name, description, price, image_url, secondary_image_url, type, gender";
}

// Build the query based on filters
$query = "SELECT $fields FROM products WHERE 1=1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $query .= " AND name LIKE ?";
    $params[] = '%' . $_GET['search'] . '%';
}

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
            $query .= " ORDER BY id ASC";
    }
} else {
    $query .= " ORDER BY id ASC";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output the products array
echo json_encode($products);
?>
