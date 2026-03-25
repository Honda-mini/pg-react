<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: http://localhost:5173"); // or login.php or homepage
exit();
