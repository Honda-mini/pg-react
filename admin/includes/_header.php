<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PG Services Admin</title>
    <link rel="stylesheet" href="includes/admin.css">
</head>

<body>

<header class="admin-header">
    <div class="admin-header-inner">
        <div class="admin-logo">PG Services Admin</div>

        <nav class="admin-nav">
            <a href="index.php">Dashboard</a>
            <a href="manage_stock.php">Manage Stock</a>
            <a href="add_vehicle.php">Add Vehicle</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="scripts/logout.php" class="logout">Logout</a>
        </nav>
    </div>
</header>

<main class="admin-container">
