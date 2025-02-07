<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in by verifying a session variable, e.g., 'user_id'
if (isset($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => true]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>
