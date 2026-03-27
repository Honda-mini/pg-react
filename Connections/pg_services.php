<?php
$hostname_pg_services = "localhost";
$database_pg_services = "pg_services";
$username_pg_services = "root";
$password_pg_services = "root";

// Create mysqli connection
$pg_services = new mysqli(
    $hostname_pg_services, 
    $username_pg_services, 
    $password_pg_services, 
    $database_pg_services
    );

// Check for connection error
if ($pg_services->connect_error) {
    die("Connection failed: " . $pg_services->connect_error);
}
?>