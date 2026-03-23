<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: ../../HomePage"); // or login.php or homepage
exit();
