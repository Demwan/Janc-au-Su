<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../auth_helper.php';

header('Content-Type: application/json');
requireStaffAccess($conn);

try {
    // Get JSON input and decode it
    $input = file_get_contents('php://input');
    $orderIds = json_decode($input, true);

    // Validate input
    if (!is_array($orderIds) || empty($orderIds)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input: order IDs required']);
        exit;
    }

    // Start transaction
    $conn->beginTransaction();

    // Updated query to use correct enum values from the database
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = 'Shipped'
        WHERE order_id = ? 
        AND status = 'Processing'
    ");

    $updatedCount = 0;
    $errors = [];

    // Update each order
    foreach ($orderIds as $orderId) {
        try {
            $stmt->execute([$orderId]);
            $updatedCount += $stmt->rowCount();
        } catch (PDOException $e) {
            $errors[] = "Error updating order #$orderId: " . $e->getMessage();
        }
    }

    // If there were any errors, rollback
    if (!empty($errors)) {
        $conn->rollBack();
        throw new Exception(implode(", ", $errors));
    }

    // If we got here, commit the transaction
    $conn->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => "$updatedCount orders marked as shipped",
        'updated_count' => $updatedCount
    ]);

} catch (Exception $e) {
    // If anything went wrong, rollback and return error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    error_log("Error in mark_as_shipped.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error occurred while updating orders',
        'debug_message' => $e->getMessage()
    ]);
} 