<?php
session_start();
header('Content-Type: application/json');

// Vernietig de sessie
if (session_destroy()) {
    echo json_encode(['success' => true, 'message' => 'Successfully logged out.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to log out.']);
}
?>
