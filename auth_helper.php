<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Please login first', 'redirect' => '/login.html']);
        } else {
            header('Location: /login.html');
        }
        exit;
    }
}

function requireStaffAccess($conn) {
    requireLogin();
    
    $stmt = $conn->prepare("SELECT type FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!in_array($user['type'], ['admin', 'picker'])) {
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied', 'redirect' => '/index.html']);
        } else {
            header('Location: /index.html');
        }
        exit;
    }
}

function isApiRequest() {
    return (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) || 
    (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);
}
?> 