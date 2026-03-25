<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: ../index.php"); // or login.php or homepage
exit();
