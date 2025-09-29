<?php
// auth.php - Session authentication
session_start();

// Regenerate session ID for security
session_regenerate_id(true);

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Include database connection
include 'config/connection.php';
?>