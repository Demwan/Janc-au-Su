<?php
require_once '../db_connect.php';
require_once '../auth_helper.php';

// Ensure only admins can access this
requireStaffAccess($conn);

$stmt = $conn->prepare("SELECT type FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied', 'redirect' => '/login.html']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'add_picker':
                $email = $data['email'];
                $stmt = $conn->prepare("UPDATE users SET type = 'picker' WHERE email = ?");
                $result = $stmt->execute([$email]);
                echo json_encode(['success' => $result]);
                break;
                
            case 'change_type':
                $userId = $data['userId'];
                $newType = $data['newType'];
                if (!in_array($newType, ['admin', 'picker', 'customer'])) {
                    echo json_encode(['error' => 'Invalid user type']);
                    exit;
                }
                $stmt = $conn->prepare("UPDATE users SET type = ? WHERE user_id = ?");
                $result = $stmt->execute([$newType, $userId]);
                echo json_encode(['success' => $result]);
                break;
        }
        exit;
    }
}

// Get all staff members
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, type FROM users WHERE type IN ('admin', 'picker')");
$stmt->execute();
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($staff);
?> 