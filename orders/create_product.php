<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connect to database
require_once '../db_connect.php';
require_once '../auth_helper.php';

// Check if user is admin
$stmt = $conn->prepare("SELECT type FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isLoggedIn() || $user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get JSON data from request body
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required_fields = ['name', 'description', 'price', 'type', 'gender', 'image_url'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate price
        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            throw new Exception("Price must be a positive number");
        }
        
        // Validate gender
        if (!in_array($data['gender'], ['men', 'women', 'unisex'])) {
            throw new Exception("Invalid gender value");
        }

        // Prepare the SQL query
        $query = "INSERT INTO products (
            name, 
            description, 
            price, 
            type, 
            gender, 
            available_sizes, 
            image_url, 
            secondary_image_url
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        
        // Execute with parameters
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['type'],
            $data['gender'],
            $data['available_sizes'] ?? null,
            $data['image_url'],
            $data['secondary_image_url'] ?? null
        ]);

        // Get the ID of the newly created product
        $productId = $conn->lastInsertId();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $productId
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
