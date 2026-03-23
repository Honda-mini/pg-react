<?php
// Start session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Not logged in – redirect to login page
    header("Location: login.php");
    exit();
}
?>
